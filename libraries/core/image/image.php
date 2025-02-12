<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Image
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * Class to manipulate an image.
 *
 * @package     	LongCMS.Platform
 * @subpackage  Image
 * @since       11.3
 */
class JImage
{
	/**
	 * @const  integer
	 * @since  11.3
	 */
	const SCALE_FILL = 1;

	/**
	 * @const  integer
	 * @since  11.3
	 */
	const SCALE_INSIDE = 2;

	/**
	 * @const  integer
	 * @since  11.3
	 */
	const SCALE_OUTSIDE = 3;

	/**
	 * @var    resource  The image resource handle.
	 * @since  11.3
	 */
	protected $handle;

	/**
	 * @var    string  The source image path.
	 * @since  11.3
	 */
	protected $path = null;

	/**
	 * @var    array  Whether or not different image formats are supported.
	 * @since  11.3
	 */
	protected static $formats = array();

	/**
	 * Class constructor.
	 *
	 * @param   mixed  $source  Either a file path for a source image or a GD resource handler for an image.
	 *
	 * @since   11.3
	 * @throws  RuntimeException
	 */
	public function __construct($source = null)
	{
		// Verify that GD support for PHP is available.
		if (!extension_loaded('gd'))
		{
			// @codeCoverageIgnoreStart
			JLog::add('The GD extension for PHP is not available.', JLog::ERROR);
			throw new RuntimeException('The GD extension for PHP is not available.');

			// @codeCoverageIgnoreEnd
		}

		// Determine which image types are supported by GD, but only once.
		if (!isset(self::$formats[IMAGETYPE_JPEG]))
		{
			$info = gd_info();
			self::$formats[IMAGETYPE_JPEG] = (isset($info['JPEG Support']) && $info['JPEG Support'] || isset($info['JPG Support']) && $info['JPG Support']) ? true : false;
			self::$formats[IMAGETYPE_PNG] = ($info['PNG Support']) ? true : false;
			self::$formats[IMAGETYPE_GIF] = ($info['GIF Read Support']) ? true : false;
		}

		// If the source input is a resource, set it as the image handle.
		if (is_resource($source) && (get_resource_type($source) == 'gd'))
		{
			$this->handle = &$source;
		}
		elseif (!empty($source) && is_string($source))
		{
			// If the source input is not empty, assume it is a path and populate the image handle.
			$this->loadFile($source);
		}
	}

	/**
	 * Method to return a properties object for an image given a filesystem path.  The
	 * result object has values for image width, height, type, attributes, mime type, bits,
	 * and channels.
	 *
	 * @param   string  $path  The filesystem path to the image for which to get properties.
	 *
	 * @return  object
	 *
	 * @since   11.3
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public static function getImageFileProperties($path)
	{
		// Make sure the file exists.
		if (!file_exists($path))
		{
			throw new InvalidArgumentException('The image file does not exist.');
		}

		// Get the image file information.
		$info = getimagesize($path);
		if (!$info)
		{
			// @codeCoverageIgnoreStart
			throw new RuntimeException('Unable to get properties for the image.');

			// @codeCoverageIgnoreEnd
		}

		// Build the response object.
		$properties = (object) array(
			'width' => $info[0],
			'height' => $info[1],
			'type' => $info[2],
			'attributes' => $info[3],
			'bits' => isset($info['bits']) ? $info['bits'] : null,
			'channels' => isset($info['channels']) ? $info['channels'] : null,
			'mime' => $info['mime']
		);

		return $properties;
	}

	/**
	 * Method to crop the current image.
	 *
	 * @param   mixed    $width      The width of the image section to crop in pixels or a percentage.
	 * @param   mixed    $height     The height of the image section to crop in pixels or a percentage.
	 * @param   integer  $left       The number of pixels from the left to start cropping.
	 * @param   integer  $top        The number of pixels from the top to start cropping.
	 * @param   bool     $createNew  If true the current image will be cloned, cropped and returned; else
	 *                               the current image will be cropped and returned.
	 *
	 * @return  JImage
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function crop($width, $height, $left, $top, $createNew = true)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Sanitize width.
		$width = $this->sanitizeWidth($width, $height);

		// Sanitize height.
		$height = $this->sanitizeHeight($height, $width);

		// Sanitize left.
		$left = $this->sanitizeOffset($left);

		// Sanitize top.
		$top = $this->sanitizeOffset($top);

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($width, $height);

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba = imageColorsForIndex($this->handle, imagecolortransparent($this->handle));
			$color = imageColorAllocate($this->handle, $rgba['red'], $rgba['green'], $rgba['blue']);

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
		}
		else
		{
			imagecopyresampled($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
		}

		// If we are cropping to a new image, create a new JImage object.
		if ($createNew)
		{
			// @codeCoverageIgnoreStart
			$new = new JImage($handle);

			return $new;

			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			$this->handle = $handle;

			return $this;
		}
	}

	private function _getCropDimensions($width, $height)
	{
		$src_img_width = $this->getWidth();
		$src_img_height = $this->getHeight();

		if (!$src_img_width || !$src_img_height) {
			throw new InvalidArgumentException('Wrong image dimensions.');
		}

		if ($src_img_width > $src_img_height) {
			if ($width && $height) {
				$mWidth = intval($height * $src_img_width / $src_img_height);
				if ($mWidth<=$width) {
					$mHeight = intval($width * $src_img_height / $src_img_width);
					$new_width = $width;
					$new_height = $mHeight;
				} else {
					$new_height = $height;
					$new_width = $mWidth;
				}
			} else if ($width) {
				$ratio = $src_img_height / $src_img_width;
				$new_width = $width;
				$new_height = intval($width * $ratio);
			} else if ($height) {
				$ratio = $src_img_width / $src_img_height;
				$new_height = $height;
				$new_width = intval($height * $ratio);
			} else {
				$new_height = $src_img_height;
				$new_width = $src_img_width;
			}
		} else {
			if ($width && $height) {
				$mWidth = intval($height * $src_img_width / $src_img_height);
				if ($mWidth<=$width) {
					$mHeight = intval($width * $src_img_height / $src_img_width);
					$new_width = $width;
					$new_height = $mHeight;
				} else {
					$new_height = $height;
					$new_width = $mWidth;
				}
			} else if ($width) {
				$ratio = $src_img_height / $src_img_width;
				$new_width = $width;
				$new_height = intval($width * $ratio);
			} else if ($height) {
				$ratio = $src_img_width / $src_img_height;
				$new_height = $height;
				$new_width = intval($height * $ratio);
			} else {
				$new_height = $src_img_height;
				$new_width = $src_img_width;
			}
		}

		if (!$new_width || !$new_height) {
			throw new InvalidArgumentException('The image sizes not valid.');
		}
		return array($new_width, $new_height);
	}


	private function _conformity($width, $height)
	{
		$response = true;
		$width = intval($width);
		$height = intval($height);
		$src_img_width = $this->getWidth();
		$src_img_height = $this->getHeight();
		if ($width) {
			$response = $width < $src_img_width;
		}
		if ($height) {
			$response = $height < $src_img_height;
		}
		$response = !(empty($width) && empty($height));
		return $response;
	}

	public function exactly($width, $height, $area = 'center', $createNew = false, $offsets = array())
	{
		$width = (int)$width;
		$height = (int)$height;
		// Make sure the resource handle is valid.
		if (!$this->isLoaded()) {
			throw new LogicException('No valid image was loaded.');
		}
		if (!$this->_conformity($width, $height)) {
			throw new LogicException('_conformity.');
		}
		list($new_width, $new_height) = $this->_getCropDimensions($width, $height);


		$src_img_width = $this->getWidth();
		$src_img_height = $this->getHeight();

		// Create the new truecolor image handle.
		$thumb = imagecreatetruecolor($new_width, $new_height);
		$status = imagecopyresampled($thumb, $this->handle, 0, 0, 0, 0, $new_width, $new_height, $src_img_width, $src_img_height);
		if (!$status) {
			throw new LogicException('Resample error.');
		}


		// Allow transparency for the new image handle.
		//imagealphablending($resample, false);
		//imagesavealpha($resample, true);

		switch($area) {
			default:
			case 'center':
				$w1 = ($new_width / 2) - ($width / 2);
				$h1 = ($new_height / 2) - ($height / 2);
				break;
			case 'left':
				$w1 = 0;
				$h1 = 0;
				break;
		}


		$thumb2 = imagecreatetruecolor($width, $height);
		$status = imagecopyresized($thumb2, $thumb, 0, 0, $w1, $h1, $width, $height, $width, $height);

		/*if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba = imageColorsForIndex($this->handle, imagecolortransparent($this->handle));
			$color = imageColorAllocate($this->handle, $rgba['red'], $rgba['green'], $rgba['blue']);

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
		}
		else
		{
			imagecopyresampled($handle, $this->handle, 0, 0, $left, $top, $width, $height, $width, $height);
		}*/
		imagedestroy($thumb);
		// If we are cropping to a new image, create a new JImage object.
		if ($createNew) {
			// @codeCoverageIgnoreStart
			$new = new JImage($thumb2);
			return $new;
			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			$this->handle = $thumb2;
			return $this;
		}
	}







	/**
	 * Method to apply a filter to the image by type.  Two examples are: grayscale and sketchy.
	 *
	 * @param   string  $type     The name of the image filter to apply.
	 * @param   array   $options  An array of options for the filter.
	 *
	 * @return  JImage
	 *
	 * @since   11.3
	 * @see     JImageFilter
	 * @throws  LogicException
	 * @throws  RuntimeException
	 */
	public function filter($type, array $options = array())
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Get the image filter instance.
		$filter = $this->getFilterInstance($type);

		// Execute the image filter.
		$filter->execute($options);

		return $this;
	}

	/**
	 * Method to get the height of the image in pixels.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function getHeight()
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		return imagesy($this->handle);
	}

	/**
	 * Method to get the width of the image in pixels.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function getWidth()
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		return imagesx($this->handle);
	}

	/**
	 * Method to return the path
	 *
	 * @return	string
	 *
	 * @since	11.3
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Method to determine whether or not an image has been loaded into the object.
	 *
	 * @return  bool
	 *
	 * @since   11.3
	 */
	public function isLoaded()
	{
		// Make sure the resource handle is valid.
		if (!is_resource($this->handle) || (get_resource_type($this->handle) != 'gd'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to determine whether or not the image has transparency.
	 *
	 * @return  bool
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function isTransparent()
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		return (imagecolortransparent($this->handle) >= 0);
	}

	/**
	 * Method to load a file into the JImage object as the resource.
	 *
	 * @param   string  $path  The filesystem path to load as an image.
	 *
	 * @return  void
	 *
	 * @since   11.3
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function loadFile($path)
	{
		// Make sure the file exists.
		if (!file_exists($path))
		{
			throw new InvalidArgumentException('The image file does not exist.');
		}

		// Get the image properties.
		$properties = self::getImageFileProperties($path);

		// Attempt to load the image based on the MIME-Type
		switch ($properties->mime)
		{
			case 'image/gif':
				// Make sure the image type is supported.
				if (empty(self::$formats[IMAGETYPE_GIF]))
				{
					// @codeCoverageIgnoreStart
					JLog::add('Attempting to load an image of unsupported type GIF.', JLog::ERROR);
					throw new RuntimeException('Attempting to load an image of unsupported type GIF.');

					// @codeCoverageIgnoreEnd
				}

				// Attempt to create the image handle.
				$handle = @imagecreatefromgif($path);
				if (!is_resource($handle))
				{
					// @codeCoverageIgnoreStart
					throw new RuntimeException('Unable to process GIF image.');

					// @codeCoverageIgnoreEnd
				}
				$this->handle = $handle;
				break;

			case 'image/jpeg':
				// Make sure the image type is supported.
				if (empty(self::$formats[IMAGETYPE_JPEG]))
				{
					// @codeCoverageIgnoreStart
					JLog::add('Attempting to load an image of unsupported type JPG.', JLog::ERROR);
					throw new RuntimeException('Attempting to load an image of unsupported type JPG.');

					// @codeCoverageIgnoreEnd
				}

				// Attempt to create the image handle.
				$handle = @imagecreatefromjpeg($path);
				if (!is_resource($handle))
				{
					// @codeCoverageIgnoreStart
					throw new RuntimeException('Unable to process JPG image.');

					// @codeCoverageIgnoreEnd
				}
				$this->handle = $handle;
				break;

			case 'image/png':
				// Make sure the image type is supported.
				if (empty(self::$formats[IMAGETYPE_PNG]))
				{
					// @codeCoverageIgnoreStart
					JLog::add('Attempting to load an image of unsupported type PNG.', JLog::ERROR);
					throw new RuntimeException('Attempting to load an image of unsupported type PNG.');

					// @codeCoverageIgnoreEnd
				}

				// Attempt to create the image handle.
				$handle = @imagecreatefrompng($path);
				if (!is_resource($handle))
				{
					// @codeCoverageIgnoreStart
					throw new RuntimeException('Unable to process PNG image.');

					// @codeCoverageIgnoreEnd
				}
				$this->handle = $handle;
				break;

			default:
				JLog::add('Attempting to load an image of unsupported type: ' . $properties->mime, JLog::ERROR);
				throw new InvalidArgumentException('Attempting to load an image of unsupported type: ' . $properties->mime);
				break;
		}

		// Set the filesystem path to the source image.
		$this->path = $path;
	}

	/**
	 * Method to resize the current image.
	 *
	 * @param   mixed    $width        The width of the resized image in pixels or a percentage.
	 * @param   mixed    $height       The height of the resized image in pixels or a percentage.
	 * @param   bool     $createNew    If true the current image will be cloned, resized and returned; else
	 * the current image will be resized and returned.
	 * @param   integer  $scaleMethod  Which method to use for scaling
	 *
	 * @return  JImage
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function resize($width, $height, $createNew = true, $scaleMethod = self::SCALE_INSIDE)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Sanitize width.
		$width = $this->sanitizeWidth($width, $height);

		// Sanitize height.
		$height = $this->sanitizeHeight($height, $width);

		// Prepare the dimensions for the resize operation.
		$dimensions = $this->prepareDimensions($width, $height, $scaleMethod);

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($dimensions->width, $dimensions->height);

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		if ($this->isTransparent())
		{
			// Get the transparent color values for the current image.
			$rgba = imageColorsForIndex($this->handle, imagecolortransparent($this->handle));
			$color = imageColorAllocate($this->handle, $rgba['red'], $rgba['green'], $rgba['blue']);

			// Set the transparent color values for the new image.
			imagecolortransparent($handle, $color);
			imagefill($handle, 0, 0, $color);

			imagecopyresized($handle, $this->handle, 0, 0, 0, 0, $dimensions->width, $dimensions->height, $this->getWidth(), $this->getHeight());
		}
		else
		{
			imagecopyresampled($handle, $this->handle, 0, 0, 0, 0, $dimensions->width, $dimensions->height, $this->getWidth(), $this->getHeight());
		}

		// If we are resizing to a new image, create a new JImage object.
		if ($createNew)
		{
			// @codeCoverageIgnoreStart
			$new = new JImage($handle);

			return $new;

			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			$this->handle = $handle;

			return $this;
		}
	}

	/**
	 * Method to rotate the current image.
	 *
	 * @param   mixed    $angle       The angle of rotation for the image
	 * @param   integer  $background  The background color to use when areas are added due to rotation
	 * @param   bool     $createNew   If true the current image will be cloned, rotated and returned; else
	 * the current image will be rotated and returned.
	 *
	 * @return  JImage
	 *
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function rotate($angle, $background = -1, $createNew = true)
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		// Sanitize input
		$angle = floatval($angle);

		// Create the new truecolor image handle.
		$handle = imagecreatetruecolor($this->getWidth(), $this->getHeight());

		// Allow transparency for the new image handle.
		imagealphablending($handle, false);
		imagesavealpha($handle, true);

		// Copy the image
		imagecopy($handle, $this->handle, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

		// Rotate the image
		$handle = imagerotate($handle, $angle, $background);

		// If we are resizing to a new image, create a new JImage object.
		if ($createNew)
		{
			// @codeCoverageIgnoreStart
			$new = new JImage($handle);

			return $new;

			// @codeCoverageIgnoreEnd
		}
		// Swap out the current handle for the new image handle.
		else
		{
			$this->handle = $handle;

			return $this;
		}
	}

	/**
	 * Method to write the current image out to a file.
	 *
	 * @param   string   $path     The filesystem path to save the image.
	 * @param   integer  $type     The image type to save the file as.
	 * @param   array    $options  The image type options to use in saving the file.
	 *
	 * @return  void
	 *
	 * @see     http://www.php.net/manual/image.constants.php
	 * @since   11.3
	 * @throws  LogicException
	 */
	public function toFile($path, $type = IMAGETYPE_JPEG, array $options = array())
	{
		// Make sure the resource handle is valid.
		if (!$this->isLoaded())
		{
			throw new LogicException('No valid image was loaded.');
		}

		switch ($type)
		{
			case IMAGETYPE_GIF:
				$status = imagegif($this->handle, $path);
				break;

			case IMAGETYPE_PNG:
				$status = imagepng($this->handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 0);
				break;

			case IMAGETYPE_JPEG:
			default:
				$status = imagejpeg($this->handle, $path, (array_key_exists('quality', $options)) ? $options['quality'] : 100);
		}
		return $status;
	}

	/**
	 * Method to get an image filter instance of a specified type.
	 *
	 * @param   string  $type  The image filter type to get.
	 *
	 * @return  JImageFilter
	 *
	 * @since   11.3
	 * @throws  RuntimeException
	 */
	protected function getFilterInstance($type)
	{
		// Sanitize the filter type.
		$type = strtolower(preg_replace('#[^A-Z0-9_]#i', '', $type));

		// Verify that the filter type exists.
		$className = 'JImageFilter' . ucfirst($type);
		if (!class_exists($className))
		{
			JLog::add('The ' . ucfirst($type) . ' image filter is not available.', JLog::ERROR);
			throw new RuntimeException('The ' . ucfirst($type) . ' image filter is not available.');
		}

		// Instantiate the filter object.
		$instance = new $className($this->handle);

		// Verify that the filter type is valid.
		if (!($instance instanceof JImageFilter))
		{
			// @codeCoverageIgnoreStart
			JLog::add('The ' . ucfirst($type) . ' image filter is not valid.', JLog::ERROR);
			throw new RuntimeException('The ' . ucfirst($type) . ' image filter is not valid.');

			// @codeCoverageIgnoreEnd
		}

		return $instance;
	}

	/**
	 * Method to get the new dimensions for a resized image.
	 *
	 * @param   integer  $width        The width of the resized image in pixels.
	 * @param   integer  $height       The height of the resized image in pixels.
	 * @param   integer  $scaleMethod  The method to use for scaling
	 *
	 * @return  object
	 *
	 * @since   11.3
	 * @throws  InvalidArgumentException
	 */
	protected function prepareDimensions($width, $height, $scaleMethod)
	{
		// Instantiate variables.
		$dimensions = new stdClass;

		switch ($scaleMethod)
		{
			case self::SCALE_FILL:
				$dimensions->width = intval(round($width));
				$dimensions->height = intval(round($height));
				break;

			case self::SCALE_INSIDE:
			case self::SCALE_OUTSIDE:
				$rx = $width > 0 ? $this->getWidth() / $width : 0;
				$ry = $height > 0 ? $this->getHeight() / $height : 0;

				if ($scaleMethod == self::SCALE_INSIDE)
				{
					$ratio = ($rx > $ry) ? $rx : $ry;
				}
				else
				{
					$ratio = ($rx < $ry) ? $rx : $ry;
				}

				$dimensions->width = intval(round($this->getWidth() / $ratio));
				$dimensions->height = intval(round($this->getHeight() / $ratio));
				break;

			default:
				throw new InvalidArgumentException('Invalid scale method.');
				break;
		}
		return $dimensions;
	}

	/**
	 * Method to sanitize a height value.
	 *
	 * @param   mixed  $height  The input height value to sanitize.
	 * @param   mixed  $width   The input width value for reference.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 */
	protected function sanitizeHeight($height, $width)
	{
		// If no height was given we will assume it is a square and use the width.
		$height = ($height === null) ? $width : $height;

		// If we were given a percentage, calculate the integer value.
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $height))
		{
			$height = intval(round($this->getHeight() * floatval(str_replace('%', '', $height)) / 100));
		}
		// Else do some rounding so we come out with a sane integer value.
		else
		{
			$height = intval(round(floatval($height)));
		}

		return $height;
	}

	/**
	 * Method to sanitize an offset value like left or top.
	 *
	 * @param   mixed  $offset  An offset value.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 */
	protected function sanitizeOffset($offset)
	{
		return intval(round(floatval($offset)));
	}

	/**
	 * Method to sanitize a width value.
	 *
	 * @param   mixed  $width   The input width value to sanitize.
	 * @param   mixed  $height  The input height value for reference.
	 *
	 * @return  integer
	 *
	 * @since   11.3
	 */
	protected function sanitizeWidth($width, $height)
	{
		// If no width was given we will assume it is a square and use the height.
		$width = ($width === null) ? $height : $width;

		// If we were given a percentage, calculate the integer value.
		if (preg_match('/^[0-9]+(\.[0-9]+)?\%$/', $width))
		{
			$width = intval(round($this->getWidth() * floatval(str_replace('%', '', $width)) / 100));
		}
		// Else do some rounding so we come out with a sane integer value.
		else
		{
			$width = intval(round(floatval($width)));
		}

		return $width;
	}



	/**
	 * Add image ALT
	 *
	 * @param 	string	$content Text source
	 * @param 	string	$alt Alternative Text
	 */
	public static function addAlt($content, $alt = '')
	{
		if (empty($content))
		{
			return $content;
		}
		$content = str_replace('\'', '"', $content);
		preg_match_all('#(?:<img )([^>]*?)(?:/?>)#is', $content, $images);

		if (empty($images[0]))
		{
			return $content;
		}

		$alt = htmlspecialchars($alt, ENT_QUOTES);

		foreach($images[0] as $i)
		{
			$a = str_replace('>', ' />', str_replace('/>', '>', strtolower($i)));
			preg_match_all('/alt="[^"]*"/', $a, $atr);

			if (empty($atr[0]))
			{
				$n = str_replace('/>', ' alt="'.$alt.'" />', $a);
				$a = str_replace('  ', ' ', $i);
				$content = str_replace($i, $n, $content);
			}
		}
		return $content;
	}

	/**
	 * Add image Title
	 *
	 * @param 	string	$content Text source
	 * @param 	string	$title Image Title Text
	 */
	public static function addTitle($content, $title)
	{
		if (empty($content) || empty($title))
		{
			return $content;
		}
		$content = str_replace('\'', '"', $content);
		preg_match_all('#(?:<img )([^>]*?)(?:/?>)#is', $content, $images);

		if (empty($images[0]))
		{
			return $content;
		}

		$title = htmlspecialchars($title, ENT_QUOTES);

		foreach($images[0] as $i)
		{
			$a = str_replace('>', ' />', str_replace('/>', '>', strtolower($i)));
			preg_match_all('/title="[^"]*"/', $a, $atr);
			if (empty($atr[0]))
			{
				$t = str_replace('/>', ' title="'.$title.'" />', $n);
				$t = str_replace('  ', ' ', $t);
				$content = str_replace($n, $t, $content);
			}
		}
		return $content;
	}







}

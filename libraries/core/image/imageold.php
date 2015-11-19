<?php
/**
* @version		$Id: image.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('_JEXEC') or die('Restricted access');

/**
 * LongCMS WS Lib Image class
 *
 * @package		LongCMS.WSLib
 * @subpackage	Media
 * @since	1.5
 */
class JImageOld
{

	/**
	 * The Source image path.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_src_img_path;

	/**
	 * The Source image identificator.
	 *
	 * @var		resource
	 * @since	1.5
	 */
	private $_src_img_res;


	/**
	 * The Source image width.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_src_img_width;


	/**
	 * The Source image height.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_src_img_height;


	/**
	 * The Source image type.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_src_img_type;

	/**
	 * The Source image type.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_src_img_size;

	/**
	 * The Source image type.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_src_img_info;


	/**
	 * The destination image identificator.
	 *
	 * @var		resource
	 * @since	1.5
	 */
	private $_dst_img_res;


	/**
	 * The destination image type.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_dst_img_type;



	/**
	 * The destination image quality.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_dst_img_quality;



	/**
	 * The watermark image identificator.
	 *
	 * @var		resource
	 * @since	1.5
	 */
	private $_watermark_res;


	/**
	 * The script work start time to unix timestamp.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_starttime;

	/**
	 * The script work start memory usage.
	 *
	 * @var		int
	 * @since	1.5
	 */
	private $_startmemory;


	/**
	 * The  cache dir.
	 *
	 * @var		string
	 * @since	1.5
	 */
	private $_cache_dir = '';



	/**
	 * The test string to watermark for calculating font max height.
	 *
	 * @var		string
	 * @since	1.5
	 */
	private $_test_string = 'AIJQCjgx|/#{}()სფჭწ';



	/**
	 * The errors array.
	 *
	 * @var		array
	 * @since	1.5
	 */
	private $_errors = array();



	/**
	 * class constructor
	 */
	public function __construct()
	{
		$this->_cache_dir = JPATH_CACHE;
		$this->_starttime = microtime(TRUE);
		$this->_startmemory = memory_get_usage();
		if (!defined('DS'))
		{
			define('DS', '/');
		}
	}


	/**
	* Set cache directory
	*
	* @return int
	*/
	public function setCacheDir($cache_dir)
	{
		$this->_cache_dir = $cache_dir;
		return $this;
	}




	/**
	 * Add image
	 *
	 * @param 	string	$src_img_path	source image path
	 */
	public function setImage($src_img_path, $remote = false)
	{
		if (is_resource($src_img_path))
		{
			$width = imagesx($src_img_path);
			$height = imagesy($src_img_path);
			$type = null;
			$src_img_info = '';
			$size = null;


			$this->_src_img_width = $width;
			$this->_src_img_height = $height;
			$this->_src_img_type = $type;
			$this->_src_img_size = $size;
			$this->_src_img_info = $src_img_info;
			$this->_src_img_res = $src_img_path;
		}
		else
		{
			if (!file_exists($src_img_path) && !$remote)
			{
				$this->_errors[] = 'Image "'.$src_img_path.'" not found! (Line: '.__LINE__.')';
				return false;
			}
			$this->_src_img_path = $src_img_path;
			list($width, $height, $type, $attr) = @getimagesize($src_img_path, $src_img_info);

			if (!$width || !$height || !$type)
			{
				$this->_errors[] = 'Wrong dimensions to source image "'.$src_img_path.'"! (Line: '.__LINE__.')';
				return false;
			}

			$size = @filesize($src_img_path);



			$this->_src_img_width = $width;
			$this->_src_img_height = $height;
			$this->_src_img_type = $type;
			$this->_src_img_size = $size;
			$this->_src_img_info = $src_img_info;
			$this->_src_img_res = $this->_imageCreateFrom($type, $src_img_path, $remote);
		}
	}



	public function getSrcWidth()
	{
		return $this->_src_img_width;
	}


	public function getSrcHeight()
	{
		return $this->_src_img_height;
	}


	public function getSrcSize()
	{
		return $this->_src_img_size;
	}




	/**
	 * resize image
	 *
	 * @param 	int	$width		new width
	 * @param 	int	$height	new height
	 * @param 	bool	$square	image square
	 */
	public function resize($width, $height = 0, $square = 0)
	{
		$this->_getImage();

		$width = (int)$width;
		$height = (int)$height;


		if (!$width && !$height)
		{
			$this->_errors[] = 'Image width not defined! (Line: '.__LINE__.')';
			return false;
		}

		list($new_width, $new_height) = $this->_getNewDimensions($width, $height, $square);

		if (!$new_width && !$new_height)
		{
			$this->_errors[] = 'Error (Line: '.__LINE__.')';
			return false;
		}

		if (empty($this->dst_img_width) && empty($this->dst_img_height))
		{
			$this->_errors[] = 'Error (Line: '.__LINE__.')';
			return false;
		}



		if ($square) // if set square
		{
			if ($width)
			{
				$height = $width;
			}
			else
			{
				$width = $height;
			}


			//Resize the image
			$thumb = imagecreatetruecolor($new_width, $new_height);
			$resample = imagecopyresampled($thumb, $this->_dst_img_res, 0, 0, 0, 0, $new_width, $new_height, $this->dst_img_width, $this->dst_img_height);
			if (!$resample)
			{
				$this->_errors[] = 'Error with "imagecopyresampled"! (Line: '.__LINE__.')';
				return false;
			}

			//Create the cropped thumbnail
			$w1 =($new_width / 2) - ($width / 2);
			$h1 = ($new_height / 2) - ($height / 2);

			$thumb2 = imagecreatetruecolor($width, $height);
			$resample = imagecopyresampled($thumb2, $thumb, 0,0, $w1, $h1, $width, $height, $width, $height);
			if (!$resample)
			{
				$this->_errors[] = 'Error with "imagecopyresampled"! (Line: '.__LINE__.')';
				return false;
			}
			$new_width = $width;
			$new_height = $height;
			imagedestroy($thumb);
		}
		else // if square don't set
		{
			$thumb2 = imagecreatetruecolor($new_width, $new_height);
			$resample = imagecopyresampled($thumb2, $this->_dst_img_res, 0, 0, 0, 0, $new_width, $new_height, $this->dst_img_width, $this->dst_img_height);

			if (!$resample)
			{
				$this->_errors[] = 'Error with "imagecopyresampled"! (Line: '.__LINE__.')';
				return false;
			}
		}

		$this->dst_img_width = $new_width;
		$this->dst_img_height = $new_height;
		$this->_dst_img_res =& $thumb2;
	}

	/**
	 * Crop image
	 *
	 * @param 	int	$width		new width
	 * @param 	int	$height	new height
	 * @param 	bool	$fromLeft	crop from left side
	 */
	public function crop($width, $height, $fromLeft = false)
	{
		$this->_getImage();
		$width = (int)$width;
		$height = (int)$height;

		if (!$this->_conformity($width, $height))
		{
			$this->_errors[] = 'Image width not defined! (Line: '.__LINE__.')';
			return false;
		}
		list($new_width, $new_height) = $this->_getCropDimensions($width, $height);
		if (!is_resource($this->_dst_img_res))
		{
			$this->_errors[] = 'Destination Image is not resource! (Line: '.__LINE__.')';
			return false;

		}


		//Resize the image
		$thumb = imagecreatetruecolor($new_width, $new_height);
		$resample = imagecopyresampled($thumb, $this->_dst_img_res, 0, 0, 0, 0, $new_width, $new_height, $this->dst_img_width, $this->dst_img_height);
		if (!$resample)
		{
			$this->_errors[] = 'Error with "imagecopyresampled"! (Line: '.__LINE__.')';
			return false;
		}

		//Create the cropped thumbnail
		if ($fromLeft)
		{
			$w1 = 0;
			$h1 = 0;
		}
		else
		{
			$w1 = ($new_width / 2) - ($width / 2);
			$h1 = ($new_height / 2) - ($height / 2);
		}



		$thumb2 = imagecreatetruecolor($width, $height);
		$resample = imagecopyresized($thumb2, $thumb, 0, 0, $w1, $h1, $width, $height, $width, $height);
		if (!$resample)
		{
			$this->_errors[] = 'Error with "imagecopyresampled"! (Line: '.__LINE__.')';
			return false;
		}

		imagedestroy($thumb);


		$this->dst_img_width = $width;
		$this->dst_img_height = $height;
		$this->_dst_img_res =& $thumb2;
	}

	/**
	 * Puzzle image
	 *
	 * @param 	int	$width		new width
	 * @param 	int	$height	new height
	 * @param 	bool	$square	image square
	 */
	public function puzzle($step = 100)
	{
		$this->_getImage();
		$L = $this->dst_img_width;
		$H = $this->dst_img_height;

		//determine the number of pieces
		$Lv = floor((float)$L / (float)$step);
		$Ho = floor((float)$H / (float)$step);

		//obtain the puzzle by mixing the pieces
		$permut = 3 * $Lv * $Ho;
		$im = $this->_dst_img_res;
		for($i = 0; $i < $permut; $i++)
		{
			$fromx = mt_rand(0, $Lv - 1);
			$fromy = mt_rand(0, $Ho - 1);

			$tox = mt_rand(0, $Lv - 1);
			$toy = mt_rand(0, $Ho - 1);

			//create a new image
			$temp = imagecreatetruecolor($step, $step);
			//copy image into $temp
			imagecopy($temp, $im, 0, 0, $step * $fromx, $step * $fromy, $step, $step);

			//move in place the generated piece
			imagecopy($im, $im, $step * $fromx, $step * $fromy, $step * $tox, $step * $toy, $step, $step);

			//restore the empty piece with the $temp
			imagecopy($im, $temp, $step * $tox, $step * $toy, 0, 0, $step, $step);
		}

		$this->_dst_img_res =& $im;
	}



	/**
	 * Add image shadow
	 *
	 * @param 	int	$size		shadow size width 1-20
	 */
	public function addShadow($size = 5)
	{
		if ($size == 0)
		{
			return true;
		}

		$this->_getImage();


		$x = $this->dst_img_width;
		$y = $this->dst_img_height;
		$width  = $x + $size;
		$height = $y + $size;
		$img = imagecreatetruecolor($width, $height);
		for ($i = 0; $i < 10; $i++)
		{
			$col = 255 - ($i * 25);
			$colors[$i] = imagecolorallocate($img, $col, $col, $col);
		}

		// Create a new image
		imagefilledrectangle($img, 0, 0, $width, $height, $colors[0]);

		// Add the shadow effect
		for ($i = 0; $i < count($colors); $i++)
		{
			imagefilledrectangle($img, $size, $size, $width--, $height--, $colors[$i]);
		}

		// Merge with the original image
		imagecopymerge($img, $this->_dst_img_res, 0, 0, 0, 0, $x, $y, 100);

		$this->dst_img_width = imagesx($img);
		$this->dst_img_height = imagesy($img);
		$this->_dst_img_res =& $img;
		return true;
	}



	/**
	 * Add image border
	 *
	 * @param 	int		$border_width	border width 1-10
	 * @param 	int		$border_deep	border deep 1-20 (20 deep black)
	 * @param 	string	$bg_color			border color (e.g. #ff0000)
	 */
	public function addBorder($border_width = 4, $border_deep = 5, $bg_color = false)
	{
		$this->_getImage();

		$w = $this->dst_img_width;
		$h = $this->dst_img_height;
		$iw  = $w + 4 * $border_width;
		$ih  = $h + 4 * $border_width;

		$img = imagecreatetruecolor($iw, $ih);
		$border_deep = 255 - ($border_deep * 12);
		$border = imagecolorallocate($img, $border_deep, $border_deep, $border_deep);

		if (!$bg_color)
		{
			$bg = imagecolorallocate($img, 255, 255, 255);
		}
		else
		{
			list($r, $g, $b) = $this->_getRGB($bg_color);
			//$bg = imagecolorallocate($img, $r + 1, $g + 1, $b + 1);
			$bg = imagecolorallocate($img, $r, $g, $b);
		}

		imagefilledrectangle($img, 0, 0, $iw, $ih, $bg);

		imagefilledrectangle($img, 1 + $border_width, 1 + $border_width, $iw - 1 - $border_width, $ih - 1 - $border_width, $border);
		$matrix = array(
		array(1, 1, 1),
		array(1, 1, 1),
		array(1, 1, 1)
		);
		for ($i = 0; $i < $border_width * 2; $i++)
		{
			imageconvolution($img, $matrix, 9, 0);
		}
		imagecopyresampled($img, $this->_dst_img_res, 2 * $border_width, 2 * $border_width, 0, 0, $w, $h, $w, $h);


		$this->dst_img_width = imagesx($img);
		$this->dst_img_height = imagesy($img);
		$this->_dst_img_res =& $img;
		return true;
	}

	/**
	 * Add image filter and effects
	 *
	 * @param 	string	$filter	filter
	 * @param 	int		$arg1	argument 1
	 * @param 	int		$arg2	argument 2
	 * @param 	int		$arg3	argument 3
	 * @param 	int		$arg4	argument 4
	 */
	public function addFilter($filter = '', $arg1 = 0, $arg2 = 0, $arg3 = 0, $arg4 = 0)
	{
		if ($this->_dst_img_res)
		{
			$img =& $this->_dst_img_res;
		}
		else
		{
			$img =& $this->_src_img_res;
		}
		$arg1 = (int)$arg1;
		$arg2 = (int)$arg2;
		$arg3 = (int)$arg3;
		$arg4 = (int)$arg4;


		switch($filter)
		{
			default:
				return false;
				break;

			case 'negate':
				// reverse all colors
				imagefilter($img, IMG_FILTER_NEGATE);
				break;

			case 'grayscale':
				// black and white
				imagefilter($img, IMG_FILTER_GRAYSCALE);
				break;

			case 'brightness':
				// $arg1 - brightness (1-100)
				imagefilter($img, IMG_FILTER_BRIGHTNESS, $arg1);
				break;

			case 'contrast':
				// $arg1 - contrast (1-100)
				imagefilter($img, IMG_FILTER_CONTRAST, $arg1);
				break;

			case 'colorize':
				// $arg1 - red, $arg2 - green, $arg3 - blue, $arg4 - alfa channel  (0-255)
				imagefilter($img, IMG_FILTER_COLORIZE, $arg1, $arg2, $arg3);
				break;

			case 'edgedetect':
				// uses edge detection to highl the edges in the images
				imagefilter($img, IMG_FILTER_EDGEDETECT);
				break;

			case 'emboss':
				// embosses the image
				imagefilter($img, IMG_FILTER_EMBOSS);
				break;


			case 'gaussian_blur':
				// blurs the image using the Gaussian method
				imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
				break;

			case 'selective_blur':
				// blurs the image
				imagefilter($img, IMG_FILTER_SELECTIVE_BLUR);
				break;

			case 'mean_removal':
				// uses mean removal to achieve a "sketchy" effect
				imagefilter($img, IMG_FILTER_MEAN_REMOVAL);
				break;

			case 'smooth':
				// $arg1 - smoothing level
				imagefilter($img, IMG_FILTER_SMOOTH, $arg1);
				break;
		}
		$this->_dst_img_res = $img;
		return true;
	}



	/**
	 * Create watermark image
	 *
	 * @param 	string	$font_file					font path
	 * @param 	string	$text							text
	 * @param 	int		$font_size					font size
	 * @param 	string	$font_color					font color in hex (e.g. #ff0000)
	 * @param 	string	$background_color	background color in hex (e.g. #ff0000)
	 * @param 	bool		$transparent				background transparency
	 * @param 	int		$angle							watermark angle in °
	 *
	 * @return	string										watermark image path
	 */
	public function createWatermark($font_file, $text, $font_size = 12,
										$font_color = '#000000', $background_color = '#FFFFFF',
										$transparent = true, $angle = 0)
	{
		static $mb_loaded;
		if (!$mb_loaded)
		{
			$mb_loaded = true;
			mb_internal_encoding("UTF-8");
		}

		$cache_dir = $this->_cache_dir;


		if (!is_dir($cache_dir) || !is_writable($cache_dir))
		{
			$this->_errors[] = 'Cache dir not exists or not writable! (Line: '.__LINE__.')';
			return false;
		}
		if (!file_exists($font_file))
		{
			$this->_errors[] = 'Font file "'.$font_file.'" not found! (Line: '.__LINE__.')';
			return false;
		}

		if (empty($text))
		{
			$this->_errors[] = 'Watermark text not defined! (Line: '.__LINE__.')';
			return false;
		}

		$ext = '.png';
		$img_hash = 'wm_'.md5($font_file.$text.$font_size.$font_color.$background_color.$transparent.$angle);
		$cache_img = $cache_dir.DS.$img_hash.$ext;

		if (file_exists($cache_img))
		{
			return $cache_img;
		}
		@list($background_r, $background_g, $background_b) = $this->_getRGB($background_color);
		@list($font_r, $font_g, $font_b) = $this->_getRGB($font_color);
		//$dip = self::get_dip($font_file, $font_size);

		$box = imagettfbbox($font_size, $angle, $font_file, $text);
		$kerning = 2;
		$width = abs($box[2] - $box[0]) + 2 + $kerning;

		$maxhbox = imagettfbbox($font_size, $angle, $font_file, $this->_test_string);
		$height = abs($maxhbox[5]) + abs($maxhbox[3]);
		$image = imagecreate($width, $height);

		if (!$image || !$box)
		{
			$this->_errors[] = 'Can\'t create watermark image! (Line: '.__LINE__.')';
			return false;
		}
		// allocate colors and draw text
		$background_color = imagecolorallocate($image, $background_r, $background_g, $background_b);
		$font_color = imagecolorallocate($image, $font_r, $font_g, $font_b);
		$int_x = abs($maxhbox[5] - $maxhbox[3] ) - $maxhbox[1];
		imagettftext($image, $font_size, 0, $box[0], $int_x, $font_color, $font_file, $text);

		// set transparency

		if ($transparent)
		{
			imagecolortransparent($image, $background_color);
		}

		// rotate image
		if ($angle)
		{
			$image = imagerotate($image, $angle, 0, true);
		}
		imagepng($image, $cache_img);
		imagedestroy($image);

		return $cache_img;
	}

	/**
	 * Add watermark image to source image
	 *
	 * @param 	string	$watermark_path	watermark image path
	 * @param 	int		$position 				watermark position
	 *																1 - top left, 2 - top center
	 *																3 - top right, 4 - left center
	 *																5 - center, 6 - right center
	 *																7 - bottom left, 8 - bottom center
	 *																9 - bottom right
	 *
	 * @param 	int		$rotate					rotate right in °
	 * @param 	int		$transparency		watermark transparency
	 * @param 	array	$offsets					position offsets x, y
	 *
	 * @return	bool
	 */
	public function addWatermark($watermark_path, $position = 1, $rotate = 0, $transparency = 0, $offsets = array(0, 0))
	{
		$this->_getImage();

		// transparency passed in %
		$transparency = 100 - 	$transparency;

		if (empty($this->dst_img_width) || empty($this->dst_img_height))
		{
			$this->_errors[] = 'Can\'t load destination image sizes! (Line: '.__LINE__.')';
			return false;
		}
		// load source image
		$size_x = $this->dst_img_width;
		$size_y = $this->dst_img_height;

		// Determine watermark size and type
		$wsize = getimagesize($watermark_path);
		$watermark_x = $wsize[0];
		$watermark_y = $wsize[1];
		$watermark_type = $wsize[2]; // 1 = GIF, 2 = JPG, 3 = PNG



		// load watermark
		$watermark = $this->_imageCreateFrom($watermark_type, $watermark_path);

		// rotate image
		if ($rotate)
		{
			$watermark = imagerotate($watermark, $rotate, 0, 1);
		}
		imagealphablending($watermark, false);
		imagesavealpha($watermark, true);


		$watermark_x = imagesx($watermark);
		$watermark_y = imagesy($watermark);


		// watermark positions
		switch($position)
		{
			case 1: //top left
				$offset_x = $size_x - $watermark_x + $offsets[0];
				$offset_y = $size_y - $watermark_y + $offsets[1];
				break;

			case 2: //top center
				$offset_x = ($size_x - $watermark_x) / 2 + $offsets[0];
				$offset_y = $size_y - $watermark_y + $offsets[1];
				break;

			case 3: //top right
				$offset_x = 0 + $offsets[0];
				$offset_y = $size_y - $watermark_y + $offsets[1];
				break;

			case 4: //left
				$offset_x = $size_x - $watermark_x + $offsets[0];
				$offset_y = ($size_y - $watermark_y) / 2 + $offsets[1];
				break;

			case 5: //center
				$offset_x = ($size_x - $watermark_x) / 2 + $offsets[0];
				$offset_y = ($size_y - $watermark_y) / 2 + $offsets[1];
				break;

			case 6: //right
				$offset_x = 0 + $offsets[0];
				$offset_y = ($size_y - $watermark_y) / 2 + $offsets[1];
				break;

			case 7: //bottom left
				$offset_x = $size_x - $watermark_x + $offsets[0];
				$offset_y = 0 + $offsets[1];
				break;

			case 8: //bottom center
				$offset_x = ($size_x - $watermark_x) / 2 + $offsets[0];
				$offset_y = 0 + $offsets[1];
				break;

			case 9: //bottom right
				$offset_x = 0 + $offsets[0];
				$offset_y = 0 + $offsets[1];
				break;

		}

		// where do we put watermark on the image?
		$dest_x = $size_x - $watermark_x - $offset_x;
		$dest_y = $size_y - $watermark_y - $offset_y;

		imagecopymerge($this->_dst_img_res, $watermark, $dest_x, $dest_y, 0, 0, $watermark_x, $watermark_y, $transparency);

		//$this->watermark_res =& $watermark;
		return true;
	}

	/**
	 * Add string to source image
	 *
	 * @param 	string	$font			font path
	 * @param 	string	$text			text to add
	 * @param 	int		$size			font size
	 * @param 	int		$position 	text position
	 *													1 - top left, 2 - top center
	 *													3 - top right, 4 - left center
	 *													5 - center, 6 - right center
	 *													7 - bottom left, 8 - bottom center
	 *													9 - bottom right
	 *
	 * @param 	int		$angle 		rotate right in °
	 *
	 * @return	bool
	 */
	public function addString($font, $text, $size = 22, $position = 1, $angle = 0)
	{
		$this->_getImage();


		// load source image
		$size_x = $this->dst_img_width;
		$size_y = $this->dst_img_height;



		$box = imagettfbbox($size, $angle, $font, $text);
		$kerning = 4;
		$textwidth = abs($box[2] - $box[0]) + $kerning;

		$maxhbox = imagettfbbox($size, $angle, $font, $this->_test_string);
		$textheight = abs($maxhbox[5]) + abs($maxhbox[3]);

		$fontX = $size_x - $box[2] - 3;
		$fontY = $size_y - $textheight;
		$pointX = $size_x - ceil($box[2] / 2) - 3;
		$pointY = $size_y - ceil($textheight / 2) - 3;

		// watermark positions
		switch($position)
		{
			case 1: //top left
				$fontX = 5;
				$fontY = $textheight;
				break;

			case 2: //top center
				$fontX = ($size_x - $textwidth) / 2;
				$fontY = $textheight;
				break;

			case 3: //top right
				$fontX = $size_x - $textwidth;
				$fontY = $textheight;
				break;

			case 4: //left
				$fontX = 5;
				$fontY = ($size_y - $textheight) / 2;
				break;

			case 5: //center
				$offset_x = ($size_x - $watermark_x) / 2;
				$offset_y = ($size_y - $watermark_y) / 2;
				break;

			case 6: //right
				$fontX = $size_x - $textwidth;
				$fontY = ($size_y - $textheight) / 2;
				break;

			case 7: //bottom left
				$fontX = 5;
				$fontY = $size_y - $textheight;
				break;

			case 8: //bottom center
				$fontX = ($size_x - $textwidth) / 2;
				$fontY = $size_y - $textheight;
				break;

			case 9: //bottom right
				$fontX = $size_x - $textwidth;
				$fontY = $size_y - $textheight;
				break;


		}

		$white = imagecolorallocatealpha($this->_dst_img_res, 255, 255, 255, 50);
		$black = imagecolorallocatealpha($this->_dst_img_res, 0, 0, 0, 50);
		$gray = imagecolorallocate($this->_dst_img_res, 127, 127, 127);

		if (imagecolorat($this->_dst_img_res, $pointX, $pointY) > $gray)
		{
			$color = $black;
		}
		else
		{
			$color = $white;
		}
		imagettftext($this->_dst_img_res, $size, $angle, $fontX - 1, $fontY - 1, $color, $font, $text);


		return true;
	}


	/**
	 * save image
	 *
	 * @param 	string	$filename	destination image full path with name
	 * @param 	int		$quality	destination image quality 1-100
	 * @param	bool		$resetImage clear destination image resource
	 *
	 * @return	mixed	saved image path for success, or false
	 */
	public function save($filename, $quality = 100, $resetImage = TRUE)
	{
		$dir = dirname($filename);
		if (!is_dir($dir) || !is_writable($dir))
		{
			$this->_errors[] = 'Directory "'.$dir.'" not exists or not writable! (Line: '.__LINE__.')';
			return false;
		}
		$this->_dst_img_quality = $quality;

		$this->_getImage();

		if (!is_resource($this->_dst_img_res))
		{
			$this->_errors[] = 'Error (Line: '.__LINE__.')';
			return false;
		}

		$ext = strtolower(substr(strrchr($filename, '.'), 1));
		switch ($ext)
		{
			case 'gif':
				$type = 1;
				break;
			case 'jpeg':
			case 'jpg':
				$type = 2;
				break;
			case 'png':
				$type = 3;
				break;
			default:
				$this->_errors[] = 'Wrong extension to image "'.$filename.'"! (Line: '.__LINE__.')';
				return false;
				break;
		}

		$this->_dst_img_type = $type;
		if (!$type)
		{
			$type = $this->_src_img_type;
		}
		if ($type == 1  && !function_exists('imagegif'))
		{
			$type = 3;
		}


		switch ($type)
		{
			default:
				$this->_errors[] = 'Wrong extension to image "'.$filename.'"! (Line: '.__LINE__.')';
				return false;
				break;
			case 1:
				$res = imagegif($this->_dst_img_res, $filename);
				break;

			case 2:
				$res = imagejpeg($this->_dst_img_res, $filename, $this->_dst_img_quality);
				break;

			case 3:
				if (PHP_VERSION >= '5.1.2')
				{
					// PNG quality: 0 (best quality, bigger file) to 9 (worst quality, smaller file)
					$quality = 9 - min(round($this->_dst_img_quality / 10), 9);
					$res = imagepng($this->_dst_img_res, $filename, $quality);
				}
				else
				{
					$res = imagepng($this->_dst_img_res, $filename);
				}
				break;
		}
		if ($resetImage)
		{
			imagedestroy($this->_dst_img_res);
		}
		return $res;
	}



	/**
	 * save watermark
	 *
	 * @param 	string	$filename	destination watermark image full path with name
	 * @param 	int		$quality		destination watermark image quality 1-100
	 *
	 * @return	mixed		saved watermark path for success, or false
	 */
	public function saveWatermark($filename, $quality = 100)
	{

		$this->_dst_img_quality = $quality;
		$dot = strrpos($filename, '.') + 1;
		$ext = strtolower(substr($filename, $dot));
		switch ($ext)
		{
			case 'gif':
				$type = 1;
				break;
			case 'jpeg':
			case 'jpg':
				$type = 2;
				break;
			case 'png':
				$type = 3;
				break;
			default:
				$type = 0;
				break;
		}

		$this->_dst_img_type = $type;
		if (!$type)
		{
			$type = $this->_src_img_type;
		}
		if (($type == 1)  && !function_exists('imagegif')) $type = 3;

		switch ($type)
		{
			default:
				return false;
				break;
			case 1:
				$res = imagegif($this->_watermark_res, $filename);
				break;

			case 2:
				$res = imagejpeg($this->_watermark_res, $filename, $this->quality);
				break;

			case 3:
				if (PHP_VERSION >= '5.1.2')
				{
					// PNG quality: 0 (best quality, bigger file) to 9 (worst quality, smaller file)
					$quality = 9 - min( round($this->_dst_img_quality / 10), 9);
					$res = imagepng($this->_watermark_res, $filename, $quality);
				}
				else
				{
					$res = imagepng($this->_watermark_res, $filename);
				}
				break;
		}
		return $res;
	}


	/**
	 * Get errors
	 *
	 * @param	bool	 $show	output errors in browser
	 *
	 * @return	string	errors
	 */
	public function getErrors($show = false)
	{
		$err = '';
		if (!empty($this->_errors))
		{
			foreach($this->_errors as $error)
			{
				$err .= $error.'<br/>';
			}
		}
		if ($show)
		{
			echo $err;
		}
		return $err;
	}


	/**
	 * Free memory
	 *
	 * @param	bool	 $show_mem_info	show memory info
	 *
	 * @return	string	memory info
	 */
	public function free($show_mem_info = false)
	{
		if (is_resource($this->_watermark_res))
		{
			imagedestroy($this->_watermark_res);
		}
		if (is_resource($this->_src_img_res))
		{
			imagedestroy($this->_src_img_res);
		}
		if (is_resource($this->_dst_img_res))
		{
			imagedestroy($this->_dst_img_res);
		}

		$info = round(microtime(TRUE) - $this->_starttime, 3).' sec.<br/>';
		$info .= round((memory_get_usage() - $this->_startmemory) / 1024, 3).' kb.<br/>';

		if ($show_mem_info)
		{
			echo $info;
		}
		return $info;
	}


	/**
	 * calculate new image dimensions
	 *
	 * @param 	int	$width		source image width
	 * @param 	int	$height	source image height
	 * @param 	bool	$square	square
	 *
	 * @return array new width and height
	 */
	private function _getCropDimensions($width, $height)
	{
		if (empty($this->_src_img_width) || empty($this->_src_img_height))
		{
			$this->_errors[] = 'Wrong source file dimensions! (Line: '.__LINE__.')';
			return false;
		}


		if ($this->_src_img_width > $this->_src_img_height)
		{
			if ($width && $height)
			{
				$mWidth = intval($height * $this->_src_img_width / $this->_src_img_height);
				if ($mWidth<=$width)
				{
					$mHeight = intval($width * $this->_src_img_height / $this->_src_img_width);
					$new_width = $width;
					$new_height = $mHeight;
				}
				else
				{
					$new_height = $height;
					$new_width = $mWidth;
				}
			}
			else if ($width)
			{
				$ratio = $this->_src_img_height / $this->_src_img_width;
				$new_width = $width;
				$new_height = intval($width * $ratio);
			}
			else if ($height)
			{
				$ratio = $this->_src_img_width / $this->_src_img_height;
				$new_height = $height;
				$new_width = intval($height * $ratio);
			}
			else
			{
				$new_height = $this->_src_img_height;
				$new_width = $this->_src_img_width;
			}
		}
		else
		{
			if ($width && $height)
			{
				$mWidth = intval($height * $this->_src_img_width / $this->_src_img_height);
				if($mWidth<=$width)
				{
					$mHeight = intval($width * $this->_src_img_height / $this->_src_img_width);
					$new_width = $width;
					$new_height = $mHeight;
				}
				else
				{
					$new_height = $height;
					$new_width = $mWidth;
				}
			}
			else if ($width)
			{
				$ratio = $this->_src_img_height / $this->_src_img_width;
				$new_width = $width;
				$new_height = intval($width * $ratio);
			}
			else if ($height)
			{
				$ratio = $this->_src_img_width / $this->_src_img_height;
				$new_height = $height;
				$new_width = intval($height * $ratio);
			}
			else
			{
				$new_height = $this->_src_img_height;
				$new_width = $this->_src_img_width;
			}
		}


		if (!$new_width || !$new_height)
		{
			$this->_errors[] = 'Wrong new dimensions! (Line: '.__LINE__.')';
			return false;
		}
		return array($new_width, $new_height);
	}


	/**
	 * calculate new image dimensions
	 *
	 * @param 	int	$width		source image width
	 * @param 	int	$height	source image height
	 * @param 	bool	$square	square
	 *
	 * @return array new width and height
	 */
	private function _getNewDimensions($width, $height, $square = false)
	{
		if ($width > $this->_src_img_width)
		{
			$width = $this->_src_img_width;
		}
		if ($height > $this->_src_img_height)
		{
			$height = $this->_src_img_height;
		}
		if ($square)
		{
			// if isset width
			if ($width)
			{
				$height = $width;
				if ($this->_src_img_width < $this->_src_img_height)
				{
					$new_width = $width;
					$new_height = ($width / $this->_src_img_width) * $this->_src_img_height;
				}
				else
				{
					$new_width = ($height / $this->_src_img_height) * $this->_src_img_width;
					$new_height = $height;
				}

				//if the width is smaller than supplied thumbnail size
				if ($new_width < $width)
				{
					$new_width = $width;
					$new_height = ($width / $this->_src_img_width) * $this->_src_img_height;
				}

				//if the height is smaller than supplied thumbnail size
				if ($new_height < $height)
				{
					$new_height = $height;
					$new_width = ($height / $this->_src_img_height) * $this->_src_img_width;
				}
			}
			else // if width not defined
			{
				$width = $height;
				if ($this->_src_img_width < $this->_src_img_height)
				{
					$new_width = $width;
					$new_height = ($width / $this->_src_img_width) * $this->_src_img_height;
				}
				else
				{
					$new_width = ($height / $this->_src_img_height) * $this->_src_img_width;
					$new_height = $height;
				}

				//if the width is smaller than supplied thumbnail size
				if ($new_width < $width)
				{
					$new_width = $width;
					$new_height = ($width / $this->_src_img_width) * $this->_src_img_height;
				}

				//if the height is smaller than supplied thumbnail size
				if ($new_height < $height)
				{
					$new_height = $height;
					$new_width = ($height / $this->_src_img_height) * $this->_src_img_width;
				}
			}
		}
		else
		{
			if ($this->_src_img_width > $this->_src_img_height)
			{
				if ($width)
				{
					$ratio = $this->_src_img_height / $this->_src_img_width;
					$new_width = $width;
					$new_height = intval($width * $ratio);
				}
				else if ($height)
				{
					$ratio = $this->_src_img_width / $this->_src_img_height;
					$new_height = $height;
					$new_width = intval($height * $ratio);
				}
				else
				{
					$new_height = $this->_src_img_height;
					$new_width = $this->_src_img_width;
				}
			}
			else
			{
				if ($width)
				{
					$ratio = $this->_src_img_height / $this->_src_img_width;
					$new_width = $width;
					$new_height = intval($width * $ratio);
				}
				else if ($height)
				{
					$ratio = $this->_src_img_width / $this->_src_img_height;
					$new_height = $height;
					$new_width = intval($height * $ratio);
				}
				else
				{
					$new_height = $this->_src_img_height;
					$new_width = $this->_src_img_width;
				}
			}
		}

		if (!$new_width || !$new_height)
		{
			$this->_errors[] = 'Wrong new dimensions! (Line: '.__LINE__.')';
			return false;
		}
		return array($new_width, $new_height);
	}


	private function _conformity($width, $height)
	{
		$response = true;
		$width = intval($width);
		$height = intval($height);
		if ($width)
		{
			$response = $width < $this->_src_img_width;
		}
		if ($height)
		{
			$response = $height < $this->_src_img_height;
		}
		$response = !(empty($width) && empty($height));
		return $response;
	}





	/**
	 * create image
	 *
	 * @param 	int		$type	image type 1-3 (1 - GIF, 2 - JPG, 3 - PNG)
	 * @param 	string	$path	source image path
	 *
	 * @return resource image
	 */
	private function _imageCreateFrom($type, $path, $remote = false)
	{
		if (!file_exists($path) && !$remote)
		{
			$this->_errors[] = 'Image "'.$path.'" not found! (Line: '.__LINE__.')';
			return false;
		}

		$im = NULL;
		switch ($type)
		{
			case 1:
				$im = @imagecreatefromgif($path);
				break;
			case 2:
				$im = @imagecreatefromjpeg($path);
				break;
			case 3:
				$im = @imagecreatefrompng($path);
				break;
			default:
				$this->_errors[] = 'Wrong type to source image "'.$path.'"! (Line: '.__LINE__.')';
				return false;
				break;
		}
		if (!$im)
		{
			$this->_errors[] = 'Wrong type to source image "'.$path.'"! (Line: '.__LINE__.')';
			return false;
		}


		return $im;
	}


	/**
	 * Convert HEX color to RGB
	 *
	 * @param 	string	$hex	color (e.g. #ff0000)
	 *
	 * @return	array			converted color map
	 */
	private function _getRGB($hex)
	{
		$convert = array_map('hexdec', str_split(ltrim($hex, '#'), 2));
		if (!is_array($convert))
		{
			$convert = array(0, 0, 0);
		}
		return $convert;
	}



	/**
	 * Get internal image
	 *
	 * @return	bool
	 */
	private function _getImage()
	{
		if (is_resource($this->_dst_img_res))
		{
			return true;
		}
		if (is_resource($this->_src_img_res))
		{
			$thumb = imagecreatetruecolor($this->_src_img_width, $this->_src_img_height);
			imagecopyresampled($thumb, $this->_src_img_res, 0, 0, 0, 0, $this->_src_img_width, $this->_src_img_height, $this->_src_img_width, $this->_src_img_height);

			$this->dst_img_width = $this->_src_img_width;
			$this->dst_img_height = $this->_src_img_height;
			$this->_dst_img_type = $this->_src_img_type;
			$this->_dst_img_res =& $thumb;
			return true;
		}
		return false;
	}

	public function __destruct()
	{
		$this->free();
	}

}

<?php
/**
* @version		$Id: text2image.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('JPATH_PLATFORM') or die('Restricted access');


class JText2Image
{
	private $_translit;
	private $_font_file;
	private $_font_size;
	private $_font_color;
	private $_background_color;

	private $_letter_spacing;
	private $_transparent;


	private $_font_underline;
	private $_font_underline_color;
	private $_font_underline_spacing;

	private $_hover;
	private $_hover_font_file;
	private $_hover_font_size;
	private $_hover_font_color;
	private $_hover_background_color;
	private $_hover_underline;
	private $_hover_underline_color;
	private $_hover_underline_spacing;


	private $_test_string;

	private $_cache_dir;
	private $_cache_url;


	public function __construct($cache_dir)
	{
		jimport('core.filesystem.file');
		jimport('core.filesystem.folder');


		$this->_cache_dir = $cache_dir;
		$this->_cache_url = str_replace(JPATH_ROOT.DS, '', $this->_cache_dir);
		if (!JFolder::exists($this->_cache_dir))
		{
			JFolder::create($this->_cache_dir, 0777);
		}
		$this->_test_string = 'ÁIJQÇjgx|/#()ასდ';
		mb_internal_encoding("UTF-8");
	}


	public function setParam($name, $value)
	{
		$name = '_'.$name;
		if ($name == '_font_file')
		{
			if (substr($value, 0, 1) == '/')
			{
				$this->_font_file = $value;
			}
			else
			{
				if (substr($value, -4) == '.ttf')
				{
					$this->_font_file = JPATH_ROOT.DS.'includes'.DS.'wsmedia'.DS.'fonts'.DS.$value;
				}
				else
				{
					$this->_font_file = JPATH_ROOT.DS.'includes'.DS.'wsmedia'.DS.'fonts'.DS.$value.'.ttf';
				}
			}
		}
		else
		{
			$this->$name = $value;
		}
	}



	public function createIMG($text, $active = 0, $level = 0)
	{
		$alt = $text;


		if (empty($text))
		{
			return false;
		}
		if ($this->_translit)
		{
			$text = $this->_translitText($text);
		}


		if (!JFile::exists($this->_font_file))
		{
			return false;
		}

		$ext = '.png';
		$img_hash = md5($this->_font_file.$this->_font_size.$this->_font_color.$this->_background_color.$this->_font_underline.$this->_font_underline_color.$this->_letter_spacing.$this->_font_underline_spacing.$this->_transparent.$text);
		$hover_hash = md5($this->_hover_font_file.$this->_hover_font_size.$this->_hover_font_color.$this->_hover_background_color.$this->_hover_underline.$this->_hover_underline_color.$this->_letter_spacing.$this->_hover_underline_spacing.$this->_transparent.$text);
		$cache_img = $this->_cache_dir.DS.$img_hash.$ext;
		$cache_hover = $this->_cache_dir.DS.$hover_hash.$ext;
		$return = '';

		$return = $this->_cache_url.'/'.$img_hash.$ext;

		if ( JFile::exists($cache_img) && !$this->_hover )
		{
			return $return;
		}
		else if (JFile::exists($cache_img) && ($this->_hover && JFile::exists($cache_hover)))
		{
			return $return;
		}

		$background_rgb = $this->_hexToRgb($this->_background_color);
		$font_rgb = $this->_hexToRgb($this->_font_color);
		$dip = $this->_getDip($this->_font_file, $this->_font_size);
		$box = imagettfbbox($this->_font_size, 0, $this->_font_file, $text);
		$kerning = (mb_strlen($text) + 1) * $this->_letter_spacing;
		$width = abs($box[2]-$box[0])+2+$kerning;
		$maxhbox = imagettfbbox($this->_font_size, 0, $this->_font_file, $this->_test_string);
		$height = abs($maxhbox[5])+abs($maxhbox[3]);
		$image = imagecreate($width, $height);
		if ( $this->_hover )
		{
			$hover_background_rgb = $this->_hexToRgb($this->_hover_background_color );
			$hover_font_rgb = $this->_hexToRgb( $this->_hover_font_color );
			$hover_dip = $this->_getDip( $this->_hover_font_file, $this->_hover_font_size );
			$hover_box = imagettfbbox( $this->_hover_font_size, 0, $this->_hover_font_file, $text );
			$hover_width = abs( $hover_box[2] - $hover_box[0] ) + 2;
			if( $width>$hover_width )
			{
				$hover_width = $width;
			}
			$hover_height = $height;
			$hover_image = imagecreate($hover_width, $hover_height);
		}
		if (!$image || !$box || ($this->_hover && !$hover_image))
		{
			return false ;
		}
		// allocate colors and draw text
		$background_color = imagecolorallocate( $image, $background_rgb['red'], $background_rgb['green'], $background_rgb['blue'] );
		$font_color = imagecolorallocate( $image, $font_rgb['red'], $font_rgb['green'] ,$font_rgb['blue'] ) ;
		$int_x = abs( $maxhbox[5] - $maxhbox[3] ) - $maxhbox[1];
		imagettftext( $image, $this->_font_size, 0, -$box[0], $int_x, $font_color, $this->_font_file, $text);

		if ($this->_hover)
		{
			$hover_background_color = imagecolorallocate( $hover_image,  $hover_background_rgb['red'], $hover_background_rgb['green'], $hover_background_rgb['blue'] );
			$hover_font_color = imagecolorallocate( $hover_image, $hover_font_rgb['red'], $hover_font_rgb['green'], $hover_font_rgb['blue']);
			imagettftext( $hover_image, $this->_hover_font_size, 0, -$hover_box[0], $int_x, $hover_font_color, $this->_hover_font_file, $text);
			// hover underline
			if ($this->_hover_underline)
			{
				$hover_underline_y = ( $hover_height - $this->_hover_underline_spacing ) - ( $hover_dip / 2 );
				$h_u_c = $this->_hexToRgb( $this->_hover_underline_color );
				$color = imagecolorallocate($hover_image, $h_u_c['red'], $h_u_c['green'],$h_u_c['blue']);
				imageline ( $hover_image, 0, $hover_underline_y, $hover_width, $hover_underline_y, $color );
			}
		}

		// underline
		if ($this->_font_underline)
		{
			$underline_y = ( $height - $this->_font_underline_spacing) - ( $dip / 2 );
			$f_u_c = $this->_hexToRgb($this->_font_underline_color);
			$color = imagecolorallocate($image, $f_u_c['red'], $f_u_c['green'],$f_u_c['blue']);
			imageline ( $image, 0, $underline_y, $width, $underline_y, $color);
		}
		// set transparency
		if ($this->_transparent)
		{
			imagecolortransparent( $image, $this->_background_color );
			if ($this->_hover)
			{
				imagecolortransparent( $hover_image, $this->_hover_background_color );
			}
		}
		if ( $this->_hover )
		{
			imagepng( $hover_image, $cache_hover );
			imagedestroy( $hover_image );
		}
		imagepng( $image, $cache_img );
		imagedestroy( $image );
		return $return;

	}

	private function _hexToRgb( $hex )
	{
		// remove '#'
		if (substr($hex,0,1) == '#')
		{
			$hex = substr($hex,1) ;
		}
		// expand short form ('fff') color
		if (strlen($hex) == 3)
		{
			$hex = substr($hex,0,1) . substr($hex,0,1) .
				   substr($hex,1,1) . substr($hex,1,1) .
				   substr($hex,2,1) . substr($hex,2,1) ;
		}
		if (strlen($hex) != 6)
		{
			return false;
		}
		// convert
		$rgb['red'] = hexdec(substr($hex,0,2)) ;
		$rgb['green'] = hexdec(substr($hex,2,2)) ;
		$rgb['blue'] = hexdec(substr($hex,4,2)) ;
		return $rgb ;
	}

	private function _getDip($font, $size)
	{
		$test_chars = 'abcdefghijklmnopqrstuvwxyz'.
								  'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
								  '1234567890' .
								  '!#$%^&*()\'"\\/;.,`~<>[]{}-+_-=' ;
		$box = imagettfbbox( $size, 0, $font, $test_chars );
		return $box[3] ;
	}

	private function _translitText($text)
	{
		 $geo = array("ა","ბ","გ","დ","ე","ვ","ზ","თ","ი","კ","ლ","მ","ნ","ო","პ","ჟ","რ","ს","ტ","უ","ფ","ქ","ღ","ყ","შ","ჩ","ც","ძ","წ","ჭ","ხ","ჯ","ჰ");
		 $lat = array('a','b','g','d','e','v','z','T','i','k','l','m','n','o','p','J','r','s','t','u','f','q','R','y','S','C','c','Z','w','W','x','j','h');
		 $text = str_replace($geo, $lat, $text);
		 return $text;
	}
}

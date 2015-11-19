<?php
/**
* @version		$Id: multipic.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('_LEXEC') or die('Direct Access to this location is not allowed.');

jimport('core.filesystem.file');
jimport('core.filesystem.folder');
jimport('core.media.image');

class JMultiPicOld
{
	private $sizes;
	private $cacheDir;
	private $_gcFreq = 10;
	private $_expiration = 86400;
	private $_config;
	private $_watermark;

	public function __construct($sizes = array(), $cacheDir = 'pictures')
	{
		$this->cacheDir = $cacheDir;

		if (empty($this->_config))
		{
			$this->_config = JComponentHelper::getParams('com_selectfile');
			$watermark = new stdClass;
			$watermark->status = $this->_config->get('watermark_status', 0);
			$watermark->transparency = $this->_config->get('watermark_transparency', 0);
			$watermark->font = $this->_config->get('watermark_font', 0);
			$watermark->fontsize = $this->_config->get('watermark_fontsize', 0);
			$watermark->textangle = $this->_config->get('watermark_textangle', 0);
			$watermark->rotate = $this->_config->get('watermark_rotate', 0);
			$watermark->position = $this->_config->get('watermark_position', 0);
			$watermark->texttransparency = $this->_config->get('watermark_texttransparency', 0);
			$watermark->text = $this->_config->get('watermark_text', 0);
			$watermark->img = $this->_config->get('watermark_img', 0);
			$watermark->position_offset_x = $this->_config->get('position_offset_x', 0);
			$watermark->position_offset_y = $this->_config->get('position_offset_y', 0);
			$watermark->addtype = $this->_config->get('watermark_addtype', 0);
			$this->_watermark = $watermark;
		}


		if (!empty($sizes))
		{
			$this->sizes = $sizes;
		}
		else
		{
			$this->loadSizes();
		}

/*
		if (mt_rand(1, $this->getGcFreq()) == 1)
		{
			$this->_gc();
		}

*/
	}

	public function setCacheDir($dir)
	{
		$this->cacheDir = $dir;
		return $this;
	}

	public function getCacheDir()
	{
		return $this->cacheDir;
	}

	public function setGcFreq($freq)
	{
		$this->_gcFreq = $freq;
		return $this;
	}

	public function getGcFreq()
	{
		return $this->_gcFreq;
	}

	public function setExpiration($exp)
	{
		$this->_expiration = $exp;
		return $this;
	}

	public function getExpiration()
	{
		return $this->_expiration;
	}


	public function loadSizes()
	{
		$cache = JFactory::getCache( '_selectfile_sizes');
		$cache->setCaching(1);
		$lifeTime = $cache->getLifeTime();
		$cache->setLifeTime(86400);

		$method = array( 'JMultiPic', '_loadSizes' );
		$this->sizes = $cache->call($method);

		$cache->setLifeTime($lifeTime);
		$cache->setCaching(0);
   }

	public function getSizes($size = false)
	{
		if ($size)
		{
			if (isset($this->sizes[$size]))
			{
				return $this->sizes[$size];
			}
		}
		return $this->sizes;
   }

	public function getImage($size, $img, $setShadow = false, $setwatermark = null)
	{
		$img = $this->_cleanImage($img);
		if (empty($size))
		{
			return false;
		}
		if (empty($img))
		{
			return false;
		}
		$s = !empty($this->sizes[$size]) ? $this->sizes[$size] : '';
		if (empty($s))
		{
			return false;
		}
		if (!is_object($s))
		{
			$s = (object)$s;
		}
		$s->width = (int)$s->width;
		$s->height = (int)$s->height;

		$shadow = !empty($s->shadow) && $setShadow ? $s->shadow : '';
		$shadowsize = !empty($s->shadowsize) && $setShadow ? $s->shadowsize : 1;

		$setwatermark = $setwatermark !== null ? $setwatermark : (isset($s->watermark) ? $s->watermark : -1);



		$watermark = !empty($this->_watermark) ? $this->_watermark : '';
		if ($setwatermark == 0)
		{
			$watermark = null;
		}
		else if ($setwatermark == 1 && is_object($watermark))
		{
			$watermark->status = 1;
		}

		$ext = '.'.JFile::getExt($img);
		$cache_url = $this->getCacheDir().'/'.$size;
		$cache_dir = $this->getCacheDir().DS.$size;

		$hash = $img.'|'.$s->width.'|'.$s->height.'|'.$s->quality.'|'.$s->imgedit.'|'.$shadow.'|'.$shadowsize;
		if (!empty($watermark))
		{
			foreach($watermark as $k=>$v)
			{
				$hash .= '|'.$v;
			}
		}
		$img_hash = md5($hash);

		if (JFile::exists(JPATH_SITE.DS.$cache_dir.DS.$img_hash.$ext))
		{
			return $cache_url.'/'.$img_hash.$ext;
		}
		if (!JFolder::exists(JPATH_SITE.DS.$cache_dir))
		{
			JFolder::create(JPATH_SITE.DS.$cache_dir, 0777);
		}

		$wsimg = new JImage();





		$isRemote = $this->_isRemote($img);
		if ($isRemote)
		{
			$wsimg->setImage($img, $isRemote);
		}
		else
		{
			$wsimg->setImage(JPATH_SITE.DS.$img, $isRemote);
		}


		// watermark
		if (!empty($watermark) && !empty($watermark->status) && $watermark->addtype == 1)
		{
			if (!empty($watermark->img) || !empty($watermark->text))
			{
				$textangle = $watermark->textangle;
				$position = $watermark->position;
				$transparency = $watermark->transparency;
				$rotate = $watermark->rotate;

				if (empty($watermark->img))
				{
					$font = JPATH_FONTS.DS.$watermark->font;
					$wmimage = $wsimg->createWatermark($font, $watermark->text, $watermark->fontsize, '#FFFFFF', '#000000', true);
					$watermark_img = JPath::clean($wmimage);
				}
				else
				{
					$watermark_img = JPath::clean($watermark->img);
				}
				if (isset($watermark->padding) && is_array($watermark->padding))
				{
					$watermark_padding = $watermark->padding;
				}
				else if (isset($watermark->position_offset_x) && isset($watermark->position_offset_y))
				{
					$watermark_padding = array($watermark->position_offset_x, $watermark->position_offset_y);
				}
				else
				{
					$watermark_padding = array(0, 0);
				}
				$wsimg->addWatermark($watermark_img, $position, $rotate, $transparency, $watermark_padding);
			}
		}


		switch($s->imgedit)
		{
			case 1:
				$wsimg->resize($s->width, $s->height, false);
				break;
			case 2:
				$wsimg->resize($s->width, $s->height, true);
				break;
			case 3:
				if (empty($s->height))
				{
					$s->height = $s->width;
				}
				$wsimg->crop($s->width, $s->height, true);
				break;
			case 4:
				if (empty($s->height))
				{
					$s->height = $s->width;
				}
				$wsimg->crop($s->width, $s->height, false);
				break;
		}

		// watermark
		if (!empty($watermark) && !empty($watermark->status) && $watermark->addtype == 2)
		{
			if (!empty($watermark->img) || !empty($watermark->text))
			{
				$textangle = $watermark->textangle;
				$position = $watermark->position;
				$transparency = $watermark->transparency;
				$rotate = $watermark->rotate;

				if (empty($watermark->img))
				{
					$font = JPATH_FONTS.DS.$watermark->font;
					$wmimage = $wsimg->createWatermark($font, $watermark->text, $watermark->fontsize, '#FFFFFF', '#000000', true);
					$watermark_img = JPath::clean($wmimage);
				}
				else
				{
					$watermark_img = JPath::clean($watermark->img);
				}
				if (isset($watermark->padding) && is_array($watermark->padding))
				{
					$watermark_padding = $watermark->padding;
				}
				else if (isset($watermark->position_offset_x) && isset($watermark->position_offset_y))
				{
					$watermark_padding = array($watermark->position_offset_x, $watermark->position_offset_y);
				}
				else
				{
					$watermark_padding = array(0, 0);
				}
				$wsimg->addWatermark($watermark_img, $position, $rotate, $transparency, $watermark_padding);
			}
		}

		// shadow
		if ($shadow)
		{
			$wsimg->addShadow($shadowsize);
		}

		if ($wsimg->save(JPATH_SITE.DS.$cache_dir.DS.$img_hash.$ext, $s->quality))
		{
			$wsimg->free();
			return $cache_url.'/'.$img_hash.$ext;
		}
		$wsimg->free();
		return false;

   }

	private function _isRemote(&$img)
	{
		$isRemote = JURI::isInternal($img) ? false : true;

		$host = JURI::root();
		if (!$isRemote && strpos($img, $host) !== false)
		{
			$img = str_replace($host, '', $img);
		}
		return $isRemote;
	}

	private function _cleanImage($img)
	{
		$img = str_replace(JPATH_SITE.DS, '', $img);
		return $img;
	}



	public static function _loadSizes()
	{
		$db = JFactory::getDBO();
		$query = ' SELECT * '
						.' FROM `#__selectfile_size` ';
		$db->setQuery($query);
		$obj = $db->loadObjectList();
		$sizes = array();
		foreach($obj as $o)
		{
			$sizes[$o->codename] = $o;
		}
		return $sizes;
   }


	private function _gc()
	{
		$expire = time() - $this->getExpiration();

		$cacheDir = $this->getCacheDir();
		foreach($this->sizes as $size=>$obj)
		{
			$imgdir = JPATH_SITE.DS.$cacheDir.DS.$size;
			if (!$imgdir || strlen($imgdir) < 2)
			{
				return false;
			}

			foreach(new DirectoryIterator($imgdir) as $file)
			{
				if (!$file->isDot() && !$file->isDir())
				{
					if ($file->getMTime() < $expire)
					{
						JFile::delete($file->getPathname());
					}
				}
			}
		}
	}





}
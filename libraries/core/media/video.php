<?php
/**
* @version		$Id: video.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * LongCMS WS Lib Video class
 *
 * @package		LongCMS.WSLib
 * @subpackage	Media
 * @since	1.5
 */
class JVideo
{
	private $_ffmpeg_path;
	private $_video_path;
	private $_starttime;
	private $_startmemory;
	private $_libtype;
	private $_ffmpeg;


	public function __construct()
	{
		$this->starttime = microtime(TRUE);
		$this->startmemory = memory_get_usage();

	}

	public function setVideo($video_path, $persistent = false)
	{
		if (!file_exists($video_path))
		{
			throw new Exception('Video file not found!');
		}
		$this->_video_path = $video_path;
		if (!class_exists('ffmpeg_movie'))
		{
			throw new Exception('FFMPEG library not installed!');
		}
		$this->_ffmpeg = new ffmpeg_movie($video_path, $persistent);
	}



	public function getDuration()
	{
		$duration = $this->_ffmpeg->getDuration();
		return $duration;
	}

	public function getFrameCount()
	{
		$framecount = $this->_ffmpeg->getFrameCount();
		return $framecount;
	}


	public function getFrameRate()
	{
		$framerate = $this->_ffmpeg->getFrameRate();
		return $framerate;
	}


	public function getComment()
	{
		$comment = $this->_ffmpeg->getComment();
		return $comment;
	}


	public function getTitle()
	{
		$title = $this->_ffmpeg->getTitle();
		return $title;
	}

	public function getArtist()
	{
		$artist = $this->_ffmpeg->getArtist();
		return $artist;
	}

	public function getCopyright()
	{
		$copyright = $this->_ffmpeg->getCopyright();
		return $copyright;
	}

	public function getGenre()
	{
		$genre = $this->_ffmpeg->getGenre();
		return $genre;
	}


	public function getTrackNumber()
	{
		$tracknumber = $this->_ffmpeg->getTrackNumber();
		return $tracknumber;
	}


	public function getYear()
	{
		$year = $this->_ffmpeg->getYear();
		return $year;
	}


	public function getFrameHeight()
	{
		$frameheight = $this->_ffmpeg->getFrameHeight();
		return $frameheight;
	}


	public function getFrameWidth()
	{
		$framewidth = $this->_ffmpeg->getFrameWidth();
		return $framewidth;
	}


	public function getPixelFormat()
	{
		$pixelformat = $this->_ffmpeg->getPixelFormat();
		return $pixelformat;
	}


	public function getBitRate()
	{
		$bitrate = $this->_ffmpeg->getBitRate();
		return $bitrate;
	}


	public function getVideoBitRate()
	{
		$videobitrate = $this->_ffmpeg->getVideoBitRate();
		return $videobitrate;
	}

	public function getAudioBitRate()
	{
		$audiobitrate = $this->_ffmpeg->getAudioBitRate();
		return $audiobitrate;
	}



	public function getAudioSampleRate()
	{
		$audiosamplerate = $this->_ffmpeg->getAudioSampleRate();
		return $audiosamplerate;
	}


	public function getVideoCodec()
	{
		$videocodec = $this->_ffmpeg->getVideoCodec();
		return $videocodec;
	}

	public function getAudioCodec()
	{
		$audiocodec = $this->_ffmpeg->getAudioCodec();
		return $audiocodec;
	}


	public function getAudioChannels()
	{
		$audiochannels = $this->_ffmpeg->getAudioChannels();
		return $audiochannels;
	}


	public function hasAudio()
	{
		$hasaudio = $this->_ffmpeg->hasAudio();
		return $hasaudio;
	}



	public function getFrame($framenumber)
	{
		$frame = $this->_ffmpeg->getFrame($framenumber);
		return $frame->toGDImage();
	}


	public function getFrameAsSec($sec)
	{
		$framecount = $this->getFrameCount();
		$framerate = $this->getFrameRate();
		//$duration = $this->getDuration();

		$framenumber = $sec * $framerate;
		if ($framenumber >= $framecount)
		{
			$framenumber = $framerate * 5;
		}
		if ($framenumber < 1)
		{
			$framenumber = $framerate * 5;
		}
		$framenumber = (int)$framenumber;

		$frame = $this->_ffmpeg->getFrame($framenumber);
		return $frame->toGDImage();
	}







	// DEPRECATED
	public function VideoScr($video, $image = 'scr.jpg', $sec = 10, $width = 0, $mode = 'default', $ffmpeg = '')
	{
		if (!$ffmpeg) $ffmpeg = 'C:/LocalServer/php5.2.11/PECL/ffmpeg/ffmpeg.exe';


		$disabled_functions = explode(',', ini_get('disabled_functions'));
		foreach($disabled_functions as $disabled_function)
		{
			if (trim($disabled_function) == 'exec')
			{
				self::$errstr = 'Function "exec" is disabled!';
				return false;
			}
		}

		exec($ffmpeg.' -i '.$video.' 2>&1', $array_buffer);

		$buffer = implode("\r\n", $array_buffer);
		$matches = array();

		preg_match('#(\d{2,3})x(\d{2,3})+,#i', $buffer, $matches);

		if (is_array($matches))
		{
			$old_width = (int) $matches[1];
			$old_height = (int) $matches[2];
		}


		exec($ffmpeg.' -i "'.$video.'" -s '.$width.'x'.$height.' -f image2 -ss '.$sec.'.00 -vframes 1 -pix_fmt '.$mime.' '.$image, $buffer);

		// or this method
		//exec($ffmpeg.' -i "'.$video.'" -an -ss 00:00:'.$sec.' -an -r 1 -s '.$width.'x'.$height.' -vframes 1 -y -pix_fmt rgb24 '.$image, $buffer);

	}



}
<?php
/**
* @version		$Id: test.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');


abstract class JTest
{


	public static function ffmpeg($file)
	{

		$extension = 'ffmpeg';
		$extension_soname = $extension . '.' . PHP_SHLIB_SUFFIX;
		$extension_fullname = PHP_EXTENSION_DIR . '/' . $extension_soname;
		if (!extension_loaded($extension))
		{
			dl($extension_soname) or die("Can't load extension $extension_fullname\n");
		}


		$frame = 30;
		$time  = time();
		$movie = new ffmpeg_movie($file, true);
		//var_dump($movie); die;

		$duration = $movie->getDuration();

		if ($duration > 6)
		{
			$minutes = floor($movie->getDuration() / 60);
			$seconds = $movie -> getDuration() % 60 ;
		}

		echo 'File: <font color="red">'.$file.'</font> longs '.$duration.' seconds<br />' ;

		$img = $_SERVER['DOCUMENT_ROOT'].'/'.$time.'_'.$frame.'.jpg' ;

		$ff_frame =  $movie->getFrame ( $frame );
		if ($ff_frame)
		{
			$gd_image  =  $ff_frame->toGDImage ();
			if ($gd_image)
			{
				imagepng($gd_image,  $img);
				imagedestroy($gd_image);
			}
		}

		echo  $minutes.' min '.$seconds.' sec.<br />' ;

		echo  '<img src="'.$time.'_'.$frame.'.jpg" />' ;

		$franecount = $movie->getFrameCount();
		$framerate = $movie->getFrameRate();
		$filename = $movie->getFilename();
		$comment = $movie->getComment();

		echo "<br /><br />number of frames in a movie or audio file: ".$franecount."<br />";
		echo "frame rate of a movie in fps: ".$framerate."<br />";
		echo "path and name of the movie file or audio file: ".$filename."<br />";
		echo "comment field from the movie or audio file: ".$comment."<br />";
	}







}

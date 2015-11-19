<?php
/**
* @version		$Id: shortURL.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');

class JShortURL
{
	static 	$api = 'http://urls.ge/api.php';
	static 	$url = '';
	static 	$surl = '';
	static 	$keyword = '';
	static 	$format = 'simple';
	static 	$username = 'sportall';
	static 	$password = 'trdvsnm5cbnnttr';

	public function __construct($keyword = '', $format = 'simple', $username = 'sportall', $password = 'trdvsnm5cbnnttr' )
	{
		self::$keyword = $keyword;
		self::$format = $format;
		self::$username = $username;
		self::$password = $password;
		return true;
	}

	public function get($url)
	{
		if (empty($url))
		{
			return false;
		}
		self::$url = $url;
		$cache = JFactory::getCache( 'ShortURL_'.md5($url));
		$cache->setCaching( 1 );
		$lifeTime = $cache->_options['lifetime'];
		$cacheTime = 2592000; //30 days
		$cache->setLifeTime( $cacheTime );
		$method = array( 'JShortURL', 'getURL' );
		$url = $cache->call( $method, 'ShortURL' );
		$cache->setLifeTime( $lifeTime );
		$cache->setCaching( 0 );
		return $url;
   }

	public function getURL($data)
	{
		// Init the CURL session
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$api);
		curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
		curl_setopt($ch, CURLOPT_POST, 1);              // This is a POST request
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(     // Data to POST
				'url'      => self::$url,
				'keyword'  => self::$keyword,
				'format'   => self::$format,
				'action'   => 'shorturl',
				'username' => self::$username,
				'password' => self::$password
			));
		// Fetch and return content
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function _getURL()
	{
		return true;
	}
}
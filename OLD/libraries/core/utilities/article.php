<?php
/**
* @version		$Id: article.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * LongCMS WS Lib Article class
 *
 * @package		LongCMS.WSLib
 * @subpackage	Media
 * @since	1.5
 */
class JArticle
{
	static $data = array();
	static $dataCat = array();
	/**
	 * Get Article(s) Content(s)
	 *
	 * @param 	string	$id ID or IDs
	 */
	public static function getArticle($id)
	{
		if (empty($id))
		{
			return array();
		}

		if (is_array($id))
		{
			$idx = implode(',', $id);
		}
		else
		{
			$idx = $id;
		}
		if (empty($idx))
		{
			return self::$data;
		}
		require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
		$db = JFactory::getDBO();
		$query = ' SELECT `id`, `title`, `alias`, `introtext`, `fulltext`, `catid`, `attribs`, `sectionid` '
						.' FROM `#__content` '
						.' WHERE `id` IN ('.$idx.') '
						;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach($rows as $row)
		{
			self::$data[$row->id] = $row;
		}
		return self::$data;
	}
	/**
	 * Get Article(s) Url(s)
	 *
	 * @param 	string	$id ID or IDs
	 */
	public static function getArticleURL($id)
	{
		if (empty($id))
		{
			return array();
		}

		if (is_array($id))
		{
			$idx = implode(',', $id);
		}
		else
		{
			$idx = $id;
			if (isset(self::$data[$idx]))
			{
				return self::$data[$idx];
			}
		}
		require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
		$db = JFactory::getDBO();
		$query = ' SELECT `id`, `alias`, `catid`, `sectionid` '
						.' FROM `#__content` '
						.' WHERE `id` IN ('.$idx.')'
						;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$articles = array();
		foreach($rows as $row)
		{
			$articles[$row->id] =JRoute::_(ContentHelperRoute::getArticleRoute($row->id.':'.$row->alias, $row->catid, $row->sectionid));
		}
		return $articles;
	}

	/**
	 * Get Category Url(s)
	 *
	 * @param 	string	$id ID or IDs
	 */
	public static function getCategoryURL($id)
	{
		if (empty($id))
		{
			return array();
		}

		if (is_array($id))
		{
			$idx = implode(',', $id);
		}
		else
		{
			$idx = $id;
			if (isset(self::$dataCat[$idx]))
			{
				return self::$dataCat[$idx];
			}
		}
		require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
		$db = JFactory::getDBO();
		$query = ' SELECT `id`, `alias`, `section` '
						.' FROM `#__categories` '
						.' WHERE `id` IN ('.$idx.') '
						;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$dataCat = array();
		foreach($rows as $row)
		{
			$dataCat[$row->id] =JRoute::_(ContentHelperRoute::getCategoryRoute($row->id.':'.$row->alias, $row->section));
		}
		return $dataCat;
	}
}

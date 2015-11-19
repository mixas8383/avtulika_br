<?php
/**
* @version		$Id: article.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

/**
 * LongCMS WS Lib content class
 *
 * @package		LongCMS.WSLib
 * @subpackage	Media
 * @since	1.5
 */
abstract class JContent
{
	private static $data = array();
	private static $_cat_url = array();


	/**
	 * Get Category Url(s)
	 *
	 * @param 	string	$id ID or IDs
	 */
	public static function getCategoryURL($id)
	{
		if (empty($id))
		{
			return false;
		}

		if (isset(self::$_cat_url[$id]))
		{
			return self::$_cat_url[$id];
		}

		$db = JFactory::getDBO();
		$query = ' SELECT `id`, `alias`, `section` '
						.' FROM `#__categories` '
						.' WHERE `id`='.(int)$id.' '
						.' LIMIT 1 '
						;
		$db->setQuery($query);
		$row = $db->loadObject();
		if (empty($row->id))
		{
			return false;
		}
		self::$_cat_url[$id] = JRoute::_(ContentHelperRoute::getCategoryRoute($row->id.':'.$row->alias, $row->section));

		return self::$_cat_url[$id];
	}
}

<?php
/**
* @version		$Id: countries.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * LongCMS Countries class
 *
 * @static
 * @package     	LongCMS.Platform
 * @subpackage  Utilities
 * @since       11.1
 */
abstract class JCountries
{

	/**
	 * Get limited words
	 *
	 * @param string $text Input text.
	 * @param int $num Word count.
	 * @param mixed $saveTags Tags for strip_tags function or null.
	 * @param string $more string for end text
	 *
	 * @return string
	 */
	public static function getCountries()
	{
		static $countries;

		if (!empty($countries))
		{
			return $countries;
		}
		$db = JFactory::getDBO();
		$sql = ' SELECT * '
					.' FROM `#__system_countries` '
					.' ORDER BY `name` '
					;
		$db->setQuery($sql);
		$countries = $db->loadObjectList();
		return $countries;
	}

}


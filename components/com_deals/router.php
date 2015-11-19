<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Function to build a Users URL route.
 *
 * @param	array	The array of query string values for which to build a route.
 * @return	array	The URL route with segments represented as an array.
 * @since	1.5
 */
function DealsBuildRoute(&$query)
{
   $segments = array();
   if (isset($query['view']) && $query['view'] == 'deal' && isset($query['id'])) {
      $segments[] = 'shetavazeba-'.$query['id'];
      unset($query['view']);
      unset($query['id']);
   }

   return $segments;
}
/**
 * Function to parse a Users URL route.
 *
 * @param	array	The URL route with segments represented as an array.
 * @return	array	The array of variables to set in the request.
 * @since	1.5
 */
function DealsParseRoute($segments)
{
	$vars = array();
	if (isset($segments[0])) {
		$ex = explode(':', $segments[0]);
		if (!empty($ex[0]) && $ex[0] == 'shetavazeba' && !empty($ex[1])) {
			$vars['view'] = 'deal';
			$vars['id'] = $ex[1];
		}
	}
	return $vars;
}

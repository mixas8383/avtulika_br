<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Global definitions.
// LongCMS framework path definitions.
$parts = explode(DIRECTORY_SEPARATOR, JPATH_BASE);
array_pop($parts);

//Defines.
define('IS_ADMIN', true);
define('IS_SITE', false);
define('JPATH_ROOT', implode(DIRECTORY_SEPARATOR, $parts));
define('JPATH_SITE', JPATH_ROOT);
define('JPATH_CONFIGURATION', JPATH_ROOT);
define('JPATH_ADMINISTRATOR', JPATH_ROOT . '/'.JFOLDER_ADMINISTRATOR);
define('JPATH_LIBRARIES', JPATH_ROOT . '/libraries');
define('JPATH_PLUGINS', JPATH_ROOT . '/plugins');
define('JPATH_INSTALLATION', JPATH_ROOT . '/installation');
define('JPATH_THEMES', JPATH_BASE . '/templates');
define('JPATH_CACHE', JPATH_ROOT . '/cache');
define('JPATH_MANIFESTS', JPATH_ADMINISTRATOR . '/manifests');
define('JPATH_ERRORLOG', JPATH_ROOT.DS.'logs'.DS.'error.log');



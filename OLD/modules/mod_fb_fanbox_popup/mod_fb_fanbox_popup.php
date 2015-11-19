<?php
/**
 * @package	LongCMS.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$app = JFactory::getApplication();
$popup = $app->input->cookie->getInt('fb_popup_viewed');
//$popup = JDEBUG ? 0 : 1;

if (!$popup) {
	setcookie('fb_popup_viewed', '1');
	require JModuleHelper::getLayoutPath('mod_fb_fanbox_popup', $params->get('layout', 'default'));
}


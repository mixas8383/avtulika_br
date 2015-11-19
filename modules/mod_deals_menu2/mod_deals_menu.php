<?php
/**
 * @package	LongCMS.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
if (!JDEBUG) {
	return false;
}
require_once dirname(__FILE__).'/helper.php';

$moduleclass_sfx = JFilterOutput::clean($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_deals_related', $params->get('layout', 'default'));

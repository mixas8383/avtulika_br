<?php
/**
 * @package	LongCMS.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once dirname(__FILE__).'/helper.php';

$moduleclass_sfx = JFilterOutput::clean($params->get('moduleclass_sfx'));
$user = JFactory::getUser();
$type	= modUserHelper::getType();
$return	= modUserHelper::getReturnURL($params, $type);

require JModuleHelper::getLayoutPath('mod_user', $params->get('layout', 'default'));

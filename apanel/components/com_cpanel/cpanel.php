<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	com_cpanel
 * @copyright		Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// No access check.

$controller	= JControllerLegacy::getInstance('Cpanel');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();


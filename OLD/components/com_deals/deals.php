<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('project.deals.deals');

require_once JPATH_COMPONENT.'/helpers/route.php';


$controller = JControllerLegacy::getInstance('Deals');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

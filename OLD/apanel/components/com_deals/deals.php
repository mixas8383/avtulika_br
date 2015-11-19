<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('project.deals.deals');

require_once JPATH_COMPONENT.'/helpers/deals.php';


$lang	= JFactory::getLanguage();
$lang->load('com_users');


require_once JPATH_ADMINISTRATOR.'/components/com_users/helpers/users.php';




// Access check.
if (!JFactory::getUser()->authorise('core.manage', JCOMPONENT)) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JHtml::_('stylesheet', JFOLDER_ADMINISTRATOR.'/components/'.JCOMPONENT.'/assets/css/deals.css');

// Execute the task.
$controller = JControllerLegacy::getInstance('Deals');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
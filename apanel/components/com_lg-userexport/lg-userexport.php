<?php

/**
 * @version		$Id: lg-userexport.php 15 2013-12-26 18:37:15Z Logigroup $
 * @package     Joomla16.Tutorials
 * @subpackage  Components
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author      Logigroup
 * @license http://www.gnu.org/licenses GNU/GPL
 */



// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_lg-userexport')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
 

 
// import joomla controller library
jimport('joomla.application.component.controller');
jimport('joomla.application.component.helper');

 
// Get an instance of the controller prefixed by Ola
$controller = JControllerLegacy::getInstance('Userexport');
 
// Perform the Request task
$controller->execute(JRequest::getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();




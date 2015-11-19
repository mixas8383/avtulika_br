<?php

/**
 * @version		$Id: controller.php 15 2013-12-26 18:37:15Z Logigroup $
 * @package     Joomla16.Tutorials
 * @subpackage  Components
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author      Logigroup
 * @license http://www.gnu.org/licenses GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * Site form Component Controller
 */
class UserexportController extends JControllerLegacy
{
	function Export($tpl=null)
	{
	$view=& $this->getView('Userexport','html','UserexportView');
        $view->display($tpl);
	}
    

     
         
        
}

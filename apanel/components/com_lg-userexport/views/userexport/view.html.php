<?php
/**
 * @version     $Id: view.html.php.php 15 2013-12-26 18:37:15Z Logigroup $
 * @package     Joomla16.Tutorials
 * @subpackage  Components
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author      Logigroup
 * @license http://www.gnu.org/licenses GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
jimport('joomla.html.pagination');


 

class userexportViewUserexport extends JViewLegacy
{
        /**
         * view display method
         * @return void
         */

        function display($tpl = null) 
        {
            JToolBarHelper::title(JText::_('COM_LG_USEREXPORT_TITLE'));
            $document = JFactory::getDocument();
           parent::display($tpl);
       }

      
}



        



<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Deals master display controller.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class DealsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Load the submenu.
		DealsHelper::addSubmenu(JRequest::getCmd('view', 'deals'));

		$view	= JRequest::getCmd('view', 'deals');
		$layout = JRequest::getCmd('layout', 'default');
		$id		= JRequest::getInt('id');
		$user = JFactory::getUser();

		$views = array('deals', 'deal', 'company', 'category');

		if (!in_array($view, $views)) {
			if (!$user->authorise('deals.'.$view.'.manage', JCOMPONENT)) {
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}




		// Check for edit form.
		if ($view == 'deal' && $layout == 'edit' && !$this->checkEditId(JCOMPONENT.'.edit.deal', $id)) {
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.JCOMPONENT.'&view=deals', false));
			return false;
		} elseif ($view == 'category' && $layout == 'edit' && !$this->checkEditId(JCOMPONENT.'.edit.category', $id)) {
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.JCOMPONENT.'&view=categories', false));
			return false;
		}

		parent::display();

		return $this;
	}
}

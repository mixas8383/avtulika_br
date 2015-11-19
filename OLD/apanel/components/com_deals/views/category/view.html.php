<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('DealsHelper', JPATH_COMPONENT.'/helpers/deals.php');

/**
 * View to edit a banner.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.5
 */
class DealsViewCategory extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user			= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= DealsHelper::getActions();

		JToolBarHelper::title($isNew ? JText::_('COM_DEALS_MANAGER_CATEGORY_NEW') : JText::_('COM_DEALS_MANAGER_CATEGORY_EDIT'), 'deals.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories(JCOMPONENT, 'core.create')) > 0)) {
			JToolBarHelper::apply('category.apply');
			JToolBarHelper::save('category.save');

			if ($canDo->get('core.create')) {
				JToolBarHelper::save2new('category.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('category.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('category.cancel');
		}
		else {
			JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
		}

	}
}

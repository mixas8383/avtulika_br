<?php
/**
 * @package     	LongCMS.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of banners.
 *
 * @package     	LongCMS.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class DealsViewCategories extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.6
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');


		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();

		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{

		$canDo = DealsHelper::getActions();
		$user = JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_DEALS_MANAGER_CATEGORIES'), 'deals.png');
		if (count($user->getAuthorisedCategories(JCOMPONENT, 'core.create')) > 0)
		{
			JToolBarHelper::addNew('category.add');
		}

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories(JCOMPONENT, 'core.create'))) > 0 ) {
			 JToolBarHelper::addNew('category.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('category.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				JToolBarHelper::divider();
				JToolBarHelper::publish('categories.publish', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::unpublish('categories.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::checkin('categories.checkin');
		}

		if ($canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}

	}
}

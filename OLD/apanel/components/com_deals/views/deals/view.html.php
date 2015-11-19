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
class DealsViewDeals extends JViewLegacy
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
		JToolBarHelper::title(JText::_('COM_DEALS_MANAGER_DEALS'), 'deals.png');

		if ($canDo->get('core.create')) {
			 JToolBarHelper::custom('deals.soldplus', 'upload', 'upload', 'COM_DEALS_DEALS_TOOLBAR_SOLDPLUS', true);
		}

		if (count($user->getAuthorisedCategories(JCOMPONENT, 'core.create')) > 0)
		{
			JToolBarHelper::addNew('deal.add');
		}

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories(JCOMPONENT, 'core.create'))) > 0 ) {
			 JToolBarHelper::addNew('deal.add');
		}

		if ($canDo->get('core.edit'))
		{
			JToolBarHelper::editList('deal.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				JToolBarHelper::divider();
				JToolBarHelper::publish('deals.publish', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::unpublish('deals.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}
			JToolBarHelper::divider();

				JToolBarHelper::custom('deals.sendmail', 'move', 'move', 'COM_DEALS_DEALS_TOOLBAR_SENDMAIL', true);

			//if (JDEBUG) {
				//JToolBarHelper::custom('', 'move', 'move', 'COM_DEALS_DEALS_TOOLBAR_SENDMAIL', false);
			//}






			if ($this->state->get('filter.state') != -1)
			{
				JToolBarHelper::divider();
				if ($this->state->get('filter.state') != 2)
				{
					JToolBarHelper::archiveList('deals.archive');
				}
				elseif ($this->state->get('filter.state') == 2)
				{
					JToolBarHelper::unarchiveList('deals.publish');
				}
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::checkin('deals.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList(JText::_('COM_DEALS_DEALS_DELETE_CONFIRM'), 'deals.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('deals.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences(JCOMPONENT);
			JToolBarHelper::divider();
		}
	}
}

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
class DealsViewLiberty extends JViewLegacy
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
		//$this->totalAmount = $this->get('TotalAmount');


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
		JToolBarHelper::title(JText::_('COM_DEALS_MANAGER_LIBERTY'), 'deals.png');


		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				// JToolBarHelper::divider();
				// JToolBarHelper::publish('transactions.success', 'COM_DEALS_TRANSACTIONS_SETSUCCESS', true);
				// JToolBarHelper::unpublish('transactions.failed', 'COM_DEALS_TRANSACTIONS_SETFAILED', true);
			}
		}


	}
}

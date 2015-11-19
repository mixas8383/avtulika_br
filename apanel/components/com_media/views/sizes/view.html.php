<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Media component
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_media
 * @since 1.0
 */
class MediaViewSizes extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

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
		$edit_types = MediaHelper::getEditTypes();

		$this->assign('edit_types', $edit_types);

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
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		$user = JFactory::getUser();

		// Set the titlebar text
		JToolBarHelper::title(JText::_('COM_MEDIA_SIZES'), 'mediamanager.png');


		if ($user->authorise('core.create')) {
			 JToolBarHelper::addNew('size.add');
		}

		if ($user->authorise('core.edit'))
		{
			JToolBarHelper::editList('size.edit');
		}


		if ($user->authorise('core.edit.state'))
		{
			JToolBarHelper::checkin('sizes.checkin');
		}

		if ($user->authorise('core.delete'))
		{
			JToolBarHelper::deleteList(JText::_('COM_MEDIA_SIZES_DELETE_CONFIRM'), 'sizes.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}

	}

}

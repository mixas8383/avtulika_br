<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('MediaHelper', JPATH_COMPONENT.'/helpers/media.php');

/**
 * View to edit a banner.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.5
 */
class MediaViewSize extends JViewLegacy
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


		JToolBarHelper::title($isNew ? JText::_('COM_MEDIA_MANAGER_SIZE_NEW') : JText::_('COM_MEDIA_MANAGER_SIZE_EDIT'), 'mediamanager.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($user->authorise('core.edit'))) {
			JToolBarHelper::apply('size.apply');
			JToolBarHelper::save('size.save');

			if ($user->authorise('core.create')) {
				JToolBarHelper::save2new('size.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $user->authorise('core.create')) {
			JToolBarHelper::save2copy('size.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('size.cancel');
		}
		else {
			JToolBarHelper::cancel('size.cancel', 'JTOOLBAR_CLOSE');
		}

	}
}

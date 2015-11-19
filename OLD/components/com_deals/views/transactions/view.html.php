<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package	LongCMS.Site
 * @subpackage	com_wrapper
 */
class DealsViewTransactions extends JViewLegacy
{
	protected $state = null;
	protected $item = null;
	protected $items = null;

	public function display($tpl = null)
	{
		// Initialise variables
		$state		= $this->get('State');
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$layout = $jinput->get->getCmd('layout');

		if ($layout == 'transaction') {
			$model = $this->getModel();
			$items = $model->getTransaction();
		} else {
			$items = $this->get('Items');
		}





		$pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$app = JFactory::getApplication();
		$document = JFactory::getDocument();


		$menus = $app->getMenu();
		$menu	= $menus->getActive();

		$params = $app->getParams();

		$title = $params->get('page_title', '');

		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if (empty($title)) {
			$title = $this->item->title;
		}
		$document->setTitle($title);
		$this->pagination = $pagination;
		$this->assign('items', $items);
		$this->assign('params', $params);
		parent::display($tpl);
	}
}

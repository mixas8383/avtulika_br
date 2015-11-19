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
class DealsViewInstallment extends JViewLegacy
{
	protected $data;
	protected $form;
	protected $params;
	protected $state;


	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$items = $this->get('Items');

		$menus = $app->getMenu();
		$menu	= $menus->getActive();
		$this->form = $this->get('Form');
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


		$deals = $this->_getDeals();
		foreach($deals as $deal) {
			if (!$deal->isInstallment()) {
				JError::raiseWarning(0, JText::_('COM_DEALS_BUY_NOT_INSTALLMENT'));
				$app->redirect(JRoute::_('index.php?option=com_users&view=login&Itemid='.$loginItemid, false));
				return false;
			}
		}




		$this->assign('items', $items);
		$this->assign('params', $params);
		$this->assign('document', $document);
		parent::display($tpl);
	}


	private function _getDeals()
	{
		$app			= JFactory::getApplication();
		$jinput			= $app->input;
		$user			= JFactory::getUser();
		$id 			= $jinput->post->getUint('id', 0);
		$session 		= JFactory::getSession();

		if (!$user->id) {
			$session->set('deals', array());
			$dealItemid = JMenu::getItemid('com_deals', 'deal');
			$dealurl = JRoute::_('index.php?option=com_deals&view=deal&id='.$dealurl.'Itemid='.$dealItemid, false);
			$app->setUserState('users.login.form.return', $dealurl);
			$loginItemid = JMenu::getItemid('com_users', 'login');
			JError::raiseWarning(0, JText::_('COM_DEALS_BUY_MUSTLOGIN'));
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&Itemid='.$loginItemid, false));
			return false;
		}

		$sesdeals = (array)$session->get('deals', array());

		$deals = array();
		foreach($sesdeals as $id) {
			$deal = new Deal((int)$id);
			if (!$deal->allowForBuy()) {
				continue;
			}
			$deals[] = $deal;
		}

		if (!count($deals)) {
			JError::raiseWarning(0, JText::_('COM_DEALS_BUY_DEALS_EMPTY'));
			$app->redirect(JRoute::_('index.php', false));
			return false;
		}

		return $deals;
	}







}

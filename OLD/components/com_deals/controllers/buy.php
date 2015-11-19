<?php
/**
 * @package	LongCMS.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.controllerform');

class DealsControllerBuy extends JControllerForm
{


	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}


	public function visa()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$user = PDeals::getUser();
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$deals = $this->_getDeals();
		$session->set('deals', array());

  		$paymethod = new PayMethod('visa');
		$transaction = new Transaction();
		try {
			$transaction->setUser($user);
			$transaction->setDeals($deals);
			$transaction->setPayMethod($paymethod);
			$transaction->setStatus(1); // pending
			$transaction->setType(1); // buy
			$status = $transaction->insert();

			$url = $paymethod->getRedirectUrl();
			$app->redirect($url);

		} catch (PException $e) {
			JError::raiseWarning(0, $e->getMessage());
			$app->redirect(JRoute::_('index.php', false));
		}

		$this->setMessage(JText::_('COM_DEALS_BUY_TRANSACTION_SUCCESSFUL'), 'message');
		$this->setRedirect(JRoute::_('index.php', false));
	}



	public function brao()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$user = PDeals::getUser();
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$deals = $this->_getDeals();
		$session->set('deals', array());

  		$paymethod = new PayMethod('brao');
		$transaction = new Transaction();
		try {
			$transaction->setUser($user);
			$transaction->setDeals($deals);
			$transaction->setPayMethod($paymethod);
			$transaction->setStatus(1); // pending
			$transaction->setType(1); // buy
			$status = $transaction->insert();
		} catch (PException $e) {
			JError::raiseWarning(0, $e->getMessage());
			$app->redirect(JRoute::_('index.php', false));
		}


		$this->setMessage(JText::_('COM_DEALS_BUY_TRANSACTION_SUCCESSFUL'), 'message');
		$this->setRedirect(JRoute::_('index.php', false));
	}


	public function installment()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$user = PDeals::getUser();
		$session = JFactory::getSession();
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$deals = $this->_getDeals();
		foreach($deals as $deal) {
			if (!$deal->isInstallment()) {
				JError::raiseWarning(0, JText::_('COM_DEALS_BUY_NOT_INSTALLMENT'));
				$app->redirect(JRoute::_('index.php?option=com_users&view=login&Itemid='.$loginItemid, false));
				return false;
			}
		}
		$Itemid = JMenu::getItemid('com_deals', 'installment');
		$app->redirect(JRoute::_('index.php?option=com_deals&view=installment&Itemid='.$Itemid, false));
	}


	private function _getDeals()
	{
		$app			= JFactory::getApplication();
		$jinput			= $app->input;
		$user			= PDeals::getUser();
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

		if (!$user->hasProfile()) {
			$profileItemid = JMenu::getItemid('com_users', 'profile');
			$profileurl = JRoute::_('index.php?option=com_users&view=profile&Itemid='.$profileItemid, false);
			$this->setMessage(JText::_('COM_DEALS_BUY_MUSTPROFILE'), 'error');
			$this->setRedirect($profileurl);
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

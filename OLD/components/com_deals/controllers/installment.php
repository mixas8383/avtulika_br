<?php
/**
 * @package	LongCMS.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.controllerform');

class DealsControllerInstallment extends JControllerForm
{


	public function submit()
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

		$model = $this->getModel();
		$requestData = $jinput->post->get('jform', array(), 'array');

		$form	= $model->getForm();
		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}
		$data = $model->validate($form, $requestData);

		if ($data === false) {
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				} else {
					$app->enqueueMessage($errors[$i], 'error');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_deals.installment.data', $requestData);


			$Itemid = JMenu::getItemid('com_deals', 'installment');
			$app->redirect(JRoute::_('index.php?option=com_deals&view=installment&Itemid='.$Itemid, false));
		}
  		$session->set('deals', array());

		$model->sendMail($data, $deals);


		// Flush the data from the session.
		$app->setUserState('com_deals.installment.data', null);


		$this->setMessage(JText::_('COM_DEALS_INSTALLMENT_SEND_SUCCESSFUL'), 'message');
		$this->setRedirect(JRoute::_('index.php', false));
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

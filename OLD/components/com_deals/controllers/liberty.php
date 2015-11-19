<?php
/**
 * @package	LongCMS.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.controllerform');
require_once(JPATH_PLATFORM.'/project/liberty.php');


class DealsControllerLiberty extends JControllerForm
{



	public function check()
	{
		$session = JFactory::getSession();

		$sesdeals = (array)$session->get('deals', array());

		$deals = array();
		foreach($sesdeals as $id) {
			$deal = new Deal((int)$id);
			if (!$deal->allowForBuy()) {
				continue;
			}
			$deals[] = $deal;
		}


		$deal = $deals[0];

		if (empty($deal)) {
			die(json_encode(array('status'=>'error', 'msg'=>'Deal not found')));
		}




		$firstname = isset($_POST['shipping_firstname']) ? $_POST['shipping_firstname'] : '';
		if (empty($firstname)) {
			die(json_encode(array('status'=>'error', 'msg'=>'Firstname not found')));
		}


		$lastname = isset($_POST['shipping_lastname']) ? $_POST['shipping_lastname'] : '';
		if (empty($firstname)) {
			die(json_encode(array('status'=>'error', 'msg'=>'Lastname not found')));
		}


		$phone = isset($_POST['shipping_phone']) ? $_POST['shipping_phone'] : '';
		if (empty($firstname)) {
			die(json_encode(array('status'=>'error', 'msg'=>'Phone not found')));
		}






		$address = isset($_POST['shipping_address']) ? $_POST['shipping_address'] : '';
		if (empty($address)) {
			die(json_encode(array('status'=>'error', 'msg'=>'Address not found')));
		}






		$liberty = new Liberty($deal);
		$liberty->setFirstname($firstname);
		$liberty->setLastname($lastname);
		$liberty->setPhone($phone);
		$liberty->setShippingAddress($address);


		$status = $liberty->initialize();
		if (!$status) {
			die(json_encode(array('status'=>'error', 'msg'=>'Initialize error')));
		}


		$merchant = $liberty->getMerchant();
		$ordercode = $liberty->getOrderCode();
		$callid = $liberty->getCallId();
		$hash = $liberty->getCheckHash();
		$testmode = $liberty->getTestMode();


		die(json_encode(array('status'=>'success', 'msg'=>'', 'ordercode'=>$ordercode,
			'callid'=>$callid, 'check'=>$hash, 'testmode'=>$testmode, 'merchant'=>$merchant)));
	}


	public function callback()
	{
		Transaction::log('LIBERTY REQUEST', true, 'liberty');

		$liberty = new Liberty();
		$liberty->response();
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

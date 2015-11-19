<?php
/**
 * @package    LongCMS.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die('Restricted access');

/**
 * LongCMS Platform Factory class
 *
 * @package  LongCMS.Platform
 * @since    11.1
 */
class Brao
{
	protected $_db;
	protected $_date;
	protected $_app;
	protected $_user;
	protected $_mailer;
	protected $_type = 2;
	protected $_method;
	protected $_transaction;
	protected $_message;

	public function __construct()
	{
		$this->_db = JFactory::getDBO();
		$this->_date = JFactory::getDate();
		$this->_app = JFactory::getApplication();
		$this->_user = PDeals::getUser();
		$this->_mailer = JFactory::getMailer();

	}


	public function completePayment()
	{
		$amount = $this->_transaction->getTotal(true);
		$user_id = $this->_user->get('id');

		$query = "UPDATE #__users_balance "
					." SET balance = balance - ".$amount." "
					." WHERE user_id=".$user_id." "
					." LIMIT 1 "
					;
		$this->_db->setQuery($query);
		$status = $this->_db->query();
		if (!$status) {
			throw new PException(JText::_('COM_DEALS_BUY_PAYMENT_ERROR'));
		}

		$deals = $this->_transaction->getDeals();
		foreach($deals as $deal) {
			$deal_id = $deal->id;

			$query = "UPDATE #__deals_deals "
						." SET sold = sold + 1 "
						." WHERE id=".$deal_id." "
						." LIMIT 1 "
						;
			$this->_db->setQuery($query);
			$status = $this->_db->query();
			if (!$status) {
				throw new PException(JText::_('COM_DEALS_BUY_PAYMENT_ERROR'));
			}
		}

		$status = $this->_transaction->updateStatus(2);
		if (!$status) {
			throw new PException(JText::_('COM_DEALS_BUY_PAYMENT_ERROR'));
		}

		// send success mails
		$this->sendMails();

		return true;
	}

	public function sendMails()
	{
		$transaction = $this->_transaction;

		PDeals::sendMailToAdmin($transaction);

		PDeals::sendMailToUser($transaction);

		PDeals::sendMailToCompany($transaction, null);

		return true;
	}

	public function checkPayment()
	{
		$balance = $this->_user->getBalance(true);
		$amount = $this->_transaction->getTotal(true);
		if (!$balance || !$amount || $balance < $amount) {
			throw new PException(JText::_('COM_DEALS_BUY_INSUFFICIENT_AMOUNT'));
		}




		return true;
	}

	public function setTransaction($transaction)
	{
		$this->_transaction = $transaction;
	}

	public function getType()
	{
		return $this->_type;
	}

	public function getMessage()
	{
		return $this->_message;
	}


	protected function _setMessage($msg)
	{
		$this->_message = $msg;
	}


}

<?php
/**
 * @package    LongCMS.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die('Restricted access');
jimport('core.filesystem.file');
/**
 * LongCMS Platform Factory class
 *
 * @package  LongCMS.Platform
 * @since    11.1
 */
class Transaction
{
	private $_db;
	private $_date;
	private $_app;
	private $_user;
	private $_deals;
	private $_company;
	private $_paymethod;
	private $_transaction_id;
	private $_transaction_number;
	private $_transaction_number_nova;
	private $_status;
	private $_type;
	private $_total;
	private $_message;
	const TRANSACTION_PREFIX = 'BR';


	private static $_trans_types = array(1=>'buy', 2=>'deposit');
	private static $_trans_paymethods = array(1=>'visa', 2=>'brao', 3=>'installment', 4=>'nova');
	private static $_trans_statuses = array(0=>'declined', 1=>'pending', 2=>'success');

	/*
	type -- 1 - buy, 2 - deposit
	payment_method -- 1 - visa, 2 - brao, 3 - installment, 4 - nova
	status -- 0 - declined, 1 - pending, 2 - success
	 */

	public function __construct()
	{
		$this->_db = JFactory::getDBO();
		$this->_date = JFactory::getDate();
		$this->_app = JFactory::getApplication();
	}

	public function load($transaction_id)
	{
		$query	= $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__deals_transactions');
		$query->where('transaction_number = '.$query->quote($transaction_id));
		$query->limit(1);
		$this->_db->setQuery($query);
		$transaction = $this->_db->loadObject();
		if (empty($transaction->id)) {
			return false;
		}


		$query	= $this->_db->getQuery(true);
		$query->select('td.*');
		$query->from('#__deals_transactions_data AS td');

		$query->select('d.*');
		$query->join('INNER', '#__deals_deals AS d ON td.deal_id = d.id');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

		// Join over the cities.
		$query->select('ci.title AS city_title');
		$query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

		// Join over the companies.
		$query->select('co.title AS company_title, co.description AS company_description');
		$query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

		$query->where('td.transaction_id = '.$transaction->id);
		$this->_db->setQuery($query);
		$deals_list = $this->_db->loadAssocList();
		if (empty($deals_list[0])) {
			return false;
		}
		$deals = array();
		foreach($deals_list as $dl) {
			$deals[] = new Deal($dl);
		}

		$this->_transaction_id = $transaction->id;
		$this->_transaction_number = $transaction->transaction_number;
		$this->_status = $transaction->status;
		$this->_date = $transaction->date;
		$this->_type = $transaction->type;
		$this->_total = $transaction->amount;
		$this->_deals = $deals;
		$this->_user = JFactory::getUser($transaction->user_id);
		$this->_company = $this->_loadCompany($deals[0]->company_id);
	}

	public static function getTypeName($value)
	{
		return isset(self::$_trans_types[$value]) ? self::$_trans_types[$value] : null;
	}

	public static function getPayMethodName($value)
	{
		return isset(self::$_trans_paymethods[$value]) ? self::$_trans_paymethods[$value] : null;
	}


	public static function getStatusName($value)
	{
		return isset(self::$_trans_statuses[$value]) ? self::$_trans_statuses[$value] : null;
	}

	public function getStatus()
	{
		return $this->_status;
	}

	public function getUser()
	{
		return $this->_user;
	}


	public function setUser($user)
	{
		if (is_numeric($user)) {
			$user = JFactory::getUser((int)$user);
		}
		$this->_user = $user;
	}

	public function setStatus($status)
	{
		$this->_status = $status;
	}

	public function setTotal($total)
	{
		$this->_total = $total;
	}

	public function setType($type)
	{
		$this->_type = $type;
	}

	public function setTransactionNumberNova($number)
	{
		$this->_transaction_number_nova = $number;
	}


	public function getCompany()
	{
		return $this->_company;
	}



	public function setDeals($deals)
	{
		$deal = !empty($deals[0]) ? $deals[0] : false;
		if (!$deal) {
			return false;
		}

		$company_id = $deal->company_id;
		if (!empty($company_id)) {
			$this->_company = $this->_loadCompany($company_id);
		}
		$this->_deals = $deals;
	}

	public function setPayMethod(PayMethod $paymethod)
	{
		$this->_paymethod = $paymethod;
		$this->_paymethod->setTransaction($this);
	}

	private function _loadCompany($company_id)
	{
		$query	 = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__deals_companies');
		$query->where('id='.(int)$company_id);
		$query->limit(1);
  		$this->_db->setQuery($query);
  		$company = $this->_db->loadObject();
  		return $company;
	}

	public function getTransactionId()
	{
		return $this->_transaction_id;
	}

	public function getDeals()
	{
		return $this->_deals;
	}

	public function getDate($format = 'Y-m-d H:i')
	{
		$jdate = JFactory::getDate($this->_date);
		$jdate->setTimeZone(new DateTimeZone('Asia/Tbilisi'));
		$date = $jdate->format($format, true);
		return $date;
	}

	public function getTransactionNumber()
	{
		return $this->_transaction_number;
	}


	public function getTotal($as_minor = false)
	{
		$balance = $this->_total;
		if (!$as_minor) {
			$balance = Balance::convertAsMajor($balance);
		}
		return $balance;
	}


	public function insert()
	{
		$query	 = $this->_db->getQuery(true);
		$jinput = $this->_app->input;
		$transaction_number = md5($this->_user->id.microtime(true));

		$amount = $this->_getAmountTotal();
		$date = $this->_date->toSql();
		$status = $this->_status;
		$type = $this->_type;
		$payment_method = $this->_paymethod->getType();
		$user_agent = $jinput->server->getString('HTTP_USER_AGENT', 'unknown');
		$remote_addr = $jinput->server->getString('REMOTE_ADDR');
		$transaction_number_nova = $this->_transaction_number_nova;



		try {
			$this->_paymethod->checkPayment();
		} catch (PException $e) {
			throw new PException($e->getMessage());
		}

		$data = new stdClass;
		$data->id = null;
		$data->transaction_number = $transaction_number;
		$data->transaction_number_nova = $transaction_number_nova;
		$data->user_id = $this->_user->id;
		$data->amount = $amount;
		$data->date = $date;
		$data->status = $status;
		$data->type = $type;
		$data->payment_method = $payment_method;
		$data->user_agent = $user_agent;
		$data->remote_addr = $remote_addr;


		$this->_db->transactionStart();

		$status = $this->_db->insertObject('#__deals_transactions', $data, 'id');

		if (!$status) {
			$this->_db->transactionRollback();
			throw new PException(JText::_('COM_DEALS_BUY_TRANSACTION_ERROR'));
		}
		$this->_transaction_id = $data->id;
		$data->transaction_number = $this->_generateTransactionNumber();
		$this->_transaction_number = $data->transaction_number;

		$status = $this->_db->updateObject('#__deals_transactions', $data, 'id');
		if (!$status) {
			$this->_db->transactionRollback();
			throw new PException(JText::_('COM_DEALS_BUY_TRANSACTION_ERROR'));
		}

		$status = $this->_insertData();
		if (!$status) {
			$this->_db->transactionRollback();
			throw new PException(JText::_('COM_DEALS_BUY_TRANSACTION_ERROR'));
		}

		try {
			$this->_paymethod->completePayment();
		} catch (PException $e) {
			$this->_db->transactionRollback();
			throw new PException($e->getMessage());
		}


		$this->_db->transactionCommit();


		return true;
	}


	public function updateStatus($status, $extra_data = false)
	{
		$data = new stdClass;
		$data->id = $this->_transaction_id;
		$data->status = $status;
		if ($extra_data) {
			$data->extra_data = $extra_data;
		}
		$status = $this->_db->updateObject('#__deals_transactions', $data, 'id');
		return $status;
	}


	private function _setMessage($msg)
	{
		$this->_message = $msg;
	}

	public function getMessage()
	{
		return $this->_message;
	}

	public function _insertData()
	{
		if (empty($this->_deals)) {
			if ($this->_type == 1) { // if not deposit
				return false;
			} else {
				return true;
			}
		}
		foreach($this->_deals as $deal) {
			$data = new stdClass;
			$data->transaction_id = $this->_transaction_id;
			$data->deal_id = $deal->id;
			$data->price = $deal->price;
			$data->quantity = 1;
			$status = $this->_db->insertObject('#__deals_transactions_data', $data, 'id');
			if (!$status) {
				return false;
			}
		}
		return true;
	}

	private function _getAmountTotal()
	{
		$amount = 0;
		if ($this->_type == 1) {
			$amount = 0;
			foreach($this->_deals as $deal) {
				$amount += $deal->getPrice(true);
			}
			$this->_total = $amount;
		} else if ($this->_type == 2) {
			$amount = $this->_paymethod->geTotalAmount();
		}
		return $amount;
	}

	private function _generateTransactionNumber()
	{
		$transaction_number = self::TRANSACTION_PREFIX.str_repeat(0, 10-strlen($this->_transaction_id)).$this->_transaction_id;
		return $transaction_number;
	}



	public static function log($message, $request = false, $type = '', $file_name = null, $folder_name = 'transactions')
	{
		$jdate = JFactory::getDate(null, 'Asia/Tbilisi');
		$date = $jdate->format('Y-m-d', true);
		$folder = $type ? JPATH_BASE.'/logs/'.$folder_name.'/'.$type.'/' : JPATH_BASE.'/logs/'.$folder_name.'/';

		if (!JFolder::exists($folder)) {
			$status = JFolder::create($folder, 0777);
			if (!$status) {
				trigger_error('Cant create log folder "'.$folder.'"', E_USER_NOTICE);
				return false;
			}
		}
		$name = $file_name ? $file_name : $date;
		$file = $folder.$name.'.log';
		if (!JFile::exists($file)) {
			$status = file_put_contents($file, '');
			if ($status === false) {
				trigger_error('Cant create log file "'.$file.'"', E_USER_NOTICE);
				return false;
			}
			chmod($file, 0777);
		}

		if ($request) {
			$request_data = array();
			foreach($_REQUEST as $key=>$value) {
				$request_data[] = ''.$key.'='.$value.'';
			}
			$request = 'REQUEST: '.implode('&', $request_data).'';
		}


		$data = '['.$jdate->format('Y-m-d H:i:s', true).'] ';
		$data .= '['.$_SERVER['REMOTE_ADDR'].'] ';

		$data .= '['.$message.'] ';
		if ($request) {
			$data .= '['.$request.'] ';
		}
		$data .= "\n";
		$bytes = file_put_contents($file, $data, FILE_APPEND);
		return $bytes;
	}


	public function __call($name, $args = array())
	{
		if ($this->_paymethod instanceof PayMethod && is_callable(array($this->_paymethod, $name))) {
			return call_user_func_array(array($this->_paymethod, $name), $args);
		}
		return false;
	}

}

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
class Liberty
{
	private $_db;
	private $_app;
	private $_date;

	private $_deal;



	private $_merchant = 'BRAOGE';
	private $_secretkey = 'hasdfj#1216sdas56gltbr';
	private $_ordercode = '';
	private $_callid = '';
	private $_testmode = '1';
	private $_address = '';
	private $_firstname = '';
	private $_lastname = '';
	private $_phone = '';


	private $_check = '';


	public function __construct(Deal $deal = null)
	{
		$this->_db = JFactory::getDBO();
		$this->_app = JFactory::getApplication();
		$this->_date = JFactory::getDate();
		$this->_deal = $deal;
	}

	public function setDeal(Deal $deal)
	{
		$this->_deal = $deal;
	}

	public function setShippingAddress($address)
	{
		$this->_address = $address;
	}

	public function setFirstname($name)
	{
		$this->_firstname = $name;
	}

	public function setLastname($name)
	{
		$this->_lastname = $name;
	}

	public function setPhone($phone)
	{
		$this->_phone = $phone;
	}


	public function initialize()
	{
		// insert order etc.
		$jinput = $this->_app->input;
		$user_agent = $jinput->server->getString('HTTP_USER_AGENT', 'unknown');
		$remote_addr = $jinput->server->getString('REMOTE_ADDR');

		$data = new stdClass;
		$data->id = null;
		$data->ordercode = md5($user_agent.uniqid().microtime(true));
		$data->callid = microtime(true);
		$data->deal_id = $this->_deal->id;
		$data->amount = $this->_deal->getPrice();
		$data->shipping_address = $this->_address;
		$data->shipping_firstname = $this->_firstname ;
		$data->shipping_lastname = $this->_lastname;
		$data->shipping_phone = $this->_phone;
		$data->date = $this->_date->toSQL();
		$data->status = 0;
		$data->user_agent = $user_agent;
		$data->remote_addr = $remote_addr;

		$this->_db->transactionStart();

		$status = $this->_db->insertObject('#__deals_liberty', $data, 'id');

		$this->_db->transactionCommit();

		$this->_ordercode = $data->ordercode;
		$this->_callid = $data->callid;

		return $status;
	}


	public function getCheckHash()
	{
		$check = array();
		$check[] = $this->_secretkey;
		$check[] = $this->_merchant;
		$check[] = $this->_ordercode;
		$check[] = $this->_callid;
		$check[] = $this->_address;
		$check[] = $this->_testmode;

		// product
		$check[] = $this->_deal->getId();
		$check[] = $this->_deal->getTitle();
		$check[] = '1';
		$check[] = $this->_deal->getPrice();
		$check[] = '0';
		$check[] = '0';

		$check = implode('', $check);

		$this->_check = strtoupper(hash('sha256', $check));




		return $this->_check;
	}

	public function getOrderCode()
	{



		return $this->_ordercode;
	}


	public function getMerchant()
	{



		return $this->_merchant;
	}

	public function getTestMode()
	{



		return $this->_testmode;
	}

	public function getCallId()
	{



		return $this->_callid;
	}



	public function response()
	{
		$jinput = $this->_app->input;
		$status = $jinput->get->getString('status', '');
		$installmentid = $jinput->get->getString('installmentid', '');
		$ordercode = $jinput->get->getString('ordercode', '');
		$callid = $jinput->get->getString('callid', '');
		$check = $jinput->get->getString('check', '');
		if (!$status || !$installmentid || !$ordercode || !$callid || !$check) {
			$this->_response(-3, 'შეცდომა პარამეტრებში: -3');
		}




		$query	= $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__deals_liberty');
		$query->where('ordercode = '.$query->quote($ordercode));
		$query->limit(1);
		$this->_db->setQuery($query);
		$transaction = $this->_db->loadObject();
		if (empty($transaction->id)) {
			$this->_response(-2, 'ორდერი არ მოიძებნა: -2');
		}

		if ($transaction->callid != $callid) {
			$this->_response(-2, 'ორდერი არ მოიძებნა: -2');
		}


  		$mcheck = array();
		$mcheck[] = $status;
		$mcheck[] = $installmentid;
		$mcheck[] = $ordercode;
		$mcheck[] = $callid;
		$mcheck[] = $this->_secretkey;
		$mcheck = implode('', $mcheck);
		$mcheck = strtoupper(hash('sha256', $mcheck));


		if ($mcheck != strtoupper($check)) {
			$this->_response(-2, 'ორდერი არ მოიძებნა: -2');
		}

		$query = "UPDATE `#__deals_liberty` "
					." SET `status`=".$query->quote($status).", `installmentid`=".$query->quote($installmentid)." "
					." WHERE id=".$transaction->id." "
					." LIMIT 1 "
					;
		$this->_db->setQuery($query);
		$status = $this->_db->query();

		if (!$status) {
			$this->_response(-1, 'ტექნიკური შეცდომა: -1');
		}

		$this->_response(0, 'ტრანზაქცია წარმატებით განხორციელდა: 0');
	}


	public function _response($resultcode, $resultdesc, $check = '', $data = '')
	{
		JResponse::setHeader('Content-Type', 'text/xml');
		JResponse::sendHeaders();
  		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';

  		$check = array();
		$check[] = $resultcode;
		$check[] = $resultdesc;
		$check[] = $data;
		$check[] = $this->_secretkey;

		$check = implode('', $check);

		$check = strtoupper(hash('sha256', $check));

		ob_start();
		?>
		<result>
			<resultcode><?php echo $resultcode ?></resultcode>
			<resultdesc><?php echo $resultdesc ?></resultdesc>
			<check><?php echo $check ?></check>
			<data><?php echo $data ?></data>
		</result>
		<?php
		$response = ob_get_clean();

		$log_response = str_replace(array("\n", "\r", "\t"), '', $response);

		Transaction::log('BRAO RESPONSE: '.$log_response, false, 'liberty');

		echo $response;

		die;
	}
}

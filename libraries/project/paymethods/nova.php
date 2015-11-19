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
class Nova
{
	protected $_db;
	protected $_date;
	protected $_app;
	protected $_user;
	protected $_mailer;
	protected $_type = 4;
	protected $_method;
	protected $_transaction;
	protected $_message;
	private $_extraData;
	const USERNAME = 'Nova';
	const PASSWORD = 'somesecret';
	const PRIVATE_KEY = 'testsecret';


	public function __construct()
	{
		$this->_db = JFactory::getDBO();
		$this->_date = JFactory::getDate();
		$this->_app = JFactory::getApplication();
		$this->_user = PDeals::getUser();
		$this->_mailer = JFactory::getMailer();

	}

	public function sendMails()
	{
		$transaction = $this->_transaction;

		PDeals::sendBalanceAddMailToUser($transaction);

		return true;
	}

	public function checkPayment()
	{
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


	public function setExtraData($data)
	{
		$this->_extraData = $data;
	}

	public function getExtraData()
	{
		return $this->_extraData;
	}


	public function geTotalAmount()
	{
		return $this->_extraData['PAY_AMOUNT'];
	}

	public function payMethodCheck()
	{
		if ($this->_extraData['USERNAME'] != self::USERNAME || $this->_extraData['PASSWORD'] != self::PASSWORD) {
			$this->displayPayMethodResponse(2, 'Username or password incorrect!');
		}
		$vars = $this->_extraData['OP']
					.$this->_extraData['USERNAME']
					.$this->_extraData['PASSWORD']
					.$this->_extraData['CUSTOMER_ID']
					.$this->_extraData['SERVICE_ID']
					.$this->_extraData['PAY_AMOUNT']
					.$this->_extraData['PAY_SRC']
					.$this->_extraData['PAYMENT_ID']
					.self::PRIVATE_KEY;
		$myHash = strtoupper(md5($vars));
		if ($this->_extraData['HASH_CODE'] != $myHash && !JDEBUG) {
			$this->displayPayMethodResponse(3, 'Hash code is invalid!');
		}



		switch($this->_extraData['OP']) {
			case 'debt': // displaying debt
				$query	= $this->_db->getQuery(true);
				$query->select('u.id, u.name, u.surname');
				$query->from('#__users as u');
				$query->select('b.balance');
				$query->join('INNER', '#__users_balance as b ON u.id=b.user_id');
				$query->where('u.id = '.$this->_db->quote($this->_extraData['CUSTOMER_ID']));
				$query->limit(1);
		  		$this->_db->setQuery($query);
		  		$user = $this->_db->loadObject();

		  		if (empty($user->id)) {
					$this->displayPayMethodResponse(6, 'Customer does not exist');
		  		} else {
		  			$balance = Balance::convertAsMajor($user->balance);
		  			$this->displayPayMethodResponse(0, 'OK', $balance, array('first-name'=>$user->name, 'last-name'=>$user->surname));
		  		}
				break;

			case 'verify': // verifying availability of payment
				$query	= $this->_db->getQuery(true);
				$query->select('id, name, surname');
				$query->from('#__users');
				$query->where('id = '.$this->_db->quote($this->_extraData['CUSTOMER_ID']));
				$query->limit(1);
		  		$this->_db->setQuery($query);
		  		$user = $this->_db->loadObject();
		  		if (empty($user->id)) {
					$this->displayPayMethodResponse(6, 'Customer does not exist');
		  		} else {
		  			$this->displayPayMethodResponse(0, 'OK', null, array('first-name'=>$user->name, 'last-name'=>$user->surname));
		  		}
				break;


			case 'pay': // payment
				$query	= $this->_db->getQuery(true);
				$query->select('id');
				$query->from('#__deals_transactions');
				$query->where('transaction_number_nova = '.$this->_db->quote($this->_extraData['PAYMENT_ID']));
				$query->limit(1);
		  		$this->_db->setQuery($query);
		  		$order = $this->_db->loadResult();
		  		if ($order) {
					$this->displayPayMethodResponse(8, 'Duplicate entry for transaction '.$this->_extraData['PAYMENT_ID']);
		  		}

				$query	= $this->_db->getQuery(true);
				$query->select('id, name, surname');
				$query->from('#__users');
				$query->where('id = '.$this->_db->quote($this->_extraData['CUSTOMER_ID']));
				$query->limit(1);
		  		$this->_db->setQuery($query);
		  		$user = $this->_db->loadObject();
		  		if (empty($user->id)) {
					$this->displayPayMethodResponse(6, 'Customer does not exist');
		  		}


				$sum = (int)$this->_extraData['PAY_AMOUNT'];
				if ($sum < 1) {
					$this->displayPayMethodResponse(7, 'Incorrect amount');
				}


				break;


			case 'ping': // inspecting the service
				// check product availability, check user availability
				$this->displayPayMethodResponse(0, 'OK');
				break;

		}
	}


	public function completePayment()
	{
		$sum = (int)$this->_extraData['PAY_AMOUNT'];
		$user_id = (int)$this->_extraData['CUSTOMER_ID'];





		$query = "UPDATE `#__users_balance` "
					." SET `balance`=`balance`+ ".$sum." "
					." WHERE user_id=".$user_id." "
					." LIMIT 1 "
					;
		$this->_db->setQuery($query);
		$status = $this->_db->query();
		if (!$status) {
			throw new PException('Transaction error');
		}
		return $status;
	}

	public function displayPayMethodResponse($code = 1, $msg = 'Access denied!', $debt = null, array $additional_info = array(), $receipt = null)
	{
		JResponse::setHeader('Content-Type', 'text/xml');
		JResponse::sendHeaders();
  		echo '<?xml version="1.0" encoding="UTF-8"?>';
  		?>
		<pay-response>
			<status code="<?php echo $code ?>"><?php echo $msg ?></status>
			<timestamp><?php echo time() ?></timestamp>
			<?php
			if (!is_null($debt)) {
				?>
				<debt><?php echo $debt ?></debt>
				<?php
			}
			if (!empty($additional_info)) {
				foreach($additional_info as $key=>$value) {
					?>
					<parameter name="<?php echo $key ?>"><?php echo $value ?></parameter>
					<?php
				}

			}
			?>
		</pay-response>
		<?php
		jexit();
	}




}

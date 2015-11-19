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
class MailJob
{
	private $_data;
	private $_db;
	private $_app;
	private $_date;
	private $_mailer;
	private $_limit = 100;

	public function __construct()
	{
		$this->_db = JFactory::getDBO();
		$this->_app = JFactory::getApplication();
		$this->_date = JFactory::getDate();
		$this->_mailer = JFactory::getMailer();


	}

	public function create(array $idx = array())
	{
		$now = $this->_date->toSql();
		$nullDate = $this->_db->quote($this->_db->getNullDate());
		$query	= $this->_db->getQuery(true);


		$query->select('*');
		$query->from('#__deals_mailjobs');
		$query->where('finished = 0');
  		$this->_db->setQuery($query);
  		$job = $this->_db->loadObject();

		if (!empty($job->id)) {
			throw new Exception('Unfinished job exists');
		}



		$query->clear();
		$query->select('d.*');
		$query->from('#__deals_deals AS d');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

		// Join over the cities.
		$query->select('ci.title AS city_title');
		$query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

		// Join over the companies.
		$query->select('co.title AS company_title');
		$query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

		$query->where('d.id IN('.implode(',', $idx).')');
		$query->where('d.state = 1');
		$query->where('(d.publish_up = '.$nullDate.' OR d.publish_up <= '.$query->quote($now).')');
		$query->where('(d.publish_down = '.$nullDate.' OR d.publish_down >= '.$query->quote($now).')');

		$query->order('d.publish_up DESC, d.id DESC');
  		$this->_db->setQuery($query);
  		$data = $this->_db->loadAssocList();

  		$deals = array();
  		foreach($data as $arr) {
  			$deal = new Deal($arr);
			$deals[] = $deal;
  		}

		if (empty($deals)) {
			throw new Exception('Deals not found');
		}

		$body = PDeals::_parseBatchTemplate($deals);
		if (!$body) {
			throw new Exception('Error parsing template');
		}


		$query->clear();
		$query->select('id');
		$query->from('#__users');
		$query->where('block != 1');
		$query->where('activation = ""');
		$query->where('email != ""');
		$query->where('mail_subscribed = "1"');
		$query->order('id');
  		$this->_db->setQuery($query);
  		$users = $this->_db->loadObjectList();

  		if (empty($users)) {
  			throw new Exception('Users not found');
  		}
		$remaining = count($users);

		$job = new stdClass;
		$job->create_date = $now;
		$job->total = $remaining;
		$job->in_progress = 0;
		$job->remaining = $remaining;
		$job->subject = 'დღის ფასდაკლებები';
		$job->body = $body;


		$status = $this->_db->insertObject('#__deals_mailjobs', $job, 'id');
		if (!$status || empty($job->id)) {
			throw new Exception('Error insert job');
		}

		foreach($users as $user) {
			$sql = ' INSERT INTO `#__deals_mailusers` '
					.' SET `job_id`="'.$job->id.'", `user_id`= "'.$user->id.'" '
					;
			$this->_db->setQuery($sql);
			$status = $this->_db->query();
		}
		return true;
	}


	public function load()
	{
		$query	= $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__deals_mailjobs');
		$query->where('finished = 0');
		$query->where('remaining > 0');
		$query->order('id DESC');
  		$this->_db->setQuery($query, 0, 1);
  		$job = $this->_db->loadObject();


		if (empty($job->id)) {
			throw new Exception('Job not exists');
		}

		if (!empty($job->in_progress)) {
			throw new Exception('In progress job exists');
		}

		// set job as in progress
		$sql = ' UPDATE `#__deals_mailjobs` '
				.' SET `in_progress`=1 '
				.' WHERE `id`= "'.$job->id.'" '
				.' LIMIT 1 '
				;
		$this->_db->setQuery($sql);
		$status = $this->_db->query();
		if (!$status) {
			throw new Exception('Cant set in_progress flag of job!');
		}

 		$sql = ' SELECT `u`.`id`, `u`.`email` '
 				.' FROM `#__deals_mailusers` AS `m` '
 				.' LEFT JOIN `#__users` AS `u` ON `m`.`user_id`=`u`.`id` '
				.' WHERE `m`.`job_id`= "'.$job->id.'" '
				;
		$this->_db->setQuery($sql, 0, $this->_limit);
		$users = $this->_db->loadObjectList();

  		if (empty($users)) {
	 		$sql = ' UPDATE `#__deals_mailjobs` '
					.' SET `in_progress`=0 '
					.' WHERE `id`= "'.$job->id.'" '
					.' LIMIT 1 '
					;
			$this->_db->setQuery($sql);
			$status = $this->_db->query();
  			throw new Exception('Users for this job not found');
  		}

  		$count = count($users);

		$mailfrom = $this->_app->getCfg('mailfrom');
		$fromname = $this->_app->getCfg('fromname');
  		$subject = $job->subject;
  		$body = $job->body;

  		$success = 0;
  		$mails_log = array();
  		$errors_log = array();
  		foreach($users as $user) {
  			// send mail
  			$email = $user->email;

  			$key = JCrypt::encryptString($email);
			$unsubscribe_link = 'http://www.brao.ge/index.php?option=com_users&task=user.unsubscribe&key='.$key;
			$sent_body = str_replace('{UNSUBSCRIBE_LINK}', $unsubscribe_link, $body);

			$mails_log[] = $email;

			$send = $this->_mailer->sendMail($mailfrom, $fromname, $email, $subject, $sent_body, true);

			if ($send) {
				$success++;
			} else {
				$errors_log[] = $email." - Error: not set";
			}
			$this->_mailer->clearAllRecipients();

			$sql = ' DELETE FROM `#__deals_mailusers` '
					.' WHERE `job_id`="'.$job->id.'" AND `user_id`= "'.$user->id.'" '
					.' LIMIT 1 '
					;
			$this->_db->setQuery($sql);
			$status = $this->_db->query();
			usleep(400000);
  		}


  		$mails_log_text = implode("\n", $mails_log);
  		Transaction::log($mails_log_text, false, '', 'job_'.$job->id.'_mails', 'mailjobs');

  		$log_text = 'Summary: '.$count.' | Success: '.$success.' | Remaining: '.($job->remaining - $count);
  		Transaction::log($log_text, false, '', 'job_'.$job->id, 'mailjobs');

  		if (!empty($errors_log)) {
	  		$errors_log_text = implode("\n", $errors_log);
			Transaction::log($errors_log_text, false, '', 'job_'.$job->id.'_errors', 'mailjobs');
  		}

		$finished = ($job->remaining - $count < 1) ? 1 : 0;

		if ($finished) {
			$sql = ' UPDATE `#__deals_mailjobs` '
					.' SET `last_run_date`='.$this->_db->quote($this->_date->toSql()).', `in_progress`=0, `remaining`=`remaining` - '.(int)$count.', `finished`=1, `finish_date`='.$this->_db->quote($this->_date->toSql()).' '
					.' WHERE `id`= "'.$job->id.'" '
					.' LIMIT 1 '
					;
			$this->_db->setQuery($sql);
			$status = $this->_db->query();
		} else {
			$sql = ' UPDATE `#__deals_mailjobs` '
					.' SET `last_run_date`='.$this->_db->quote($this->_date->toSql()).', `in_progress`=0, `remaining`=`remaining` - '.(int)$count.' '
					.' WHERE `id`= "'.$job->id.'" '
					.' LIMIT 1 '
					;
			$this->_db->setQuery($sql);
			$status = $this->_db->query();
		}
		return true;
	}







	public function __isset($name)
	{
		if (!is_array($this->_data)) {
			return false;
		}
		return array_key_exists($name, $this->_data) && !empty($this->_data[$name]) ? true : false;
	}

}

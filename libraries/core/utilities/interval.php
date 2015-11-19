<?php
/**
* @version		$Id: interval.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/
defined('JPATH_PLATFORM') or die('Restricted access');



class JInterval
{
	private $_db;
	private $_session;
	private $_status;
	private $_interval;
	private $_now;
	private $_time;
	private $_scope;

	public function __construct($scope = 'transactions')
	{
		$this->_scope = $scope;
		$this->_session = JFactory::getSession();
		$this->_now = time();
		$this->_time = $this->_session->get('time', 0, $this->_scope);
	}

	public function setInterval($interval)
	{
		$this->_interval = $interval;
		return $this;
	}


	public function getStatus()
	{
		if ($this->_now < $this->_time)
		{
			$this->_status = false;
		}
		else
		{
			$this->_status = true;
			$this->_session->set('time', $this->_now + $this->_interval, $this->_scope);
		}
		return $this->_status;
	}

	public function getRemaining()
	{
		$remaining = $this->_time - $this->_now;
		return $remaining;
	}


}

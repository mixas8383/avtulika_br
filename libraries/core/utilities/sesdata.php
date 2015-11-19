<?php
/**
* @version		$Id: sesdata.php 262 2012-01-16 17:52:00Z a.kikabidze $
* @package	LongCMS.Framework.WSLib
* @copyright	Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
* @license		GNU General Public License version 2 or later
*/

defined('JPATH_PLATFORM') or die('Restricted access');


class JSesData
{
	private $_session;
	private $_name;
	private $_id;
	private $_namespace;
	private $_constData = array();
	private $_dataList = array();
	private $_tmList = array();
	private $_lifetime;
	private $_savedData = array();

	private $_returnData;


	public function __construct($name, $id = 0, $namespace = 'sesdata_default', $lifetime = null)
	{
		$this->_session = JFactory::getSession();
		$this->_name = $name;
		$this->_lifetime = $lifetime;
		$this->_namespace = $namespace;
		$this->_id = (int)$id;

		$this->_dataList = $this->_getParam('data');
		$this->_constData = $this->_getParam('constData');
		$this->_tmList = $this->_getParam('tm');
	}

	public function setData(array $data)
	{
		$this->_dataList[$this->_id] = $data;
		$this->_constData = $data;
		$this->_tmList[$this->_id] = time();

		$this->_setParam('data', $this->_dataList);
		$this->_setParam('constData', $this->_constData);
		$this->_setParam('tm', $this->_tmList);
	}

	public function setSavedData($savedData)
	{
		$merge = array();
		if (is_object($savedData) || is_array($savedData))
		{
			$merge = (array)$savedData;
		}
		$this->_savedData = $merge;
	}

	public function getData()
	{
		if (is_null($this->_returnData))
		{
			$this->_dataList = $this->_getParam('data');
			$this->_tmList = $this->_getParam('tm');

			$time = !empty($this->_tmList[$this->_id]) ? $this->_tmList[$this->_id] : 0;
			$data = !empty($this->_dataList[$this->_id]) ? $this->_dataList[$this->_id] : array();

			$merge = $this->_savedData;

			if (empty($time) || empty($data))
			{
				$this->_returnData = $merge;
				return $this->_returnData;
			}

			if (!empty($this->_lifetime) && ((time() - $this->_lifetime) > $time))
			{
				$this->_returnData = $merge;
				return $this->_returnData;
			}

			$this->_returnData = array_merge($merge, $data);
		}
		return $this->_returnData;
	}

	public function get($key, $default = null)
	{
		$data = $this->getData();
		return isset($data[$key]) ? $data[$key] : $default;
	}


	public function getConstData(array $keys = array())
	{
		$this->_constData = $this->_getParam('constData');

		if (empty($this->_constData))
		{
			return array();
		}


		if (empty($keys))
		{
			$data = array();
		}
		else
		{
			foreach($this->_constData as $key=>$value)
			{
				if (!in_array($key, $keys))
				{
					$this->_constData[$key] = '';
				}

			}
			$data = $this->_constData;
		}
		return $data;
	}


	public function clearData()
	{
		$this->_dataList = $this->_getParam('data');
		$this->_tmList = $this->_getParam('tm');
		$this->_dataList[$this->_id] = array();
		$this->_tmList[$this->_id] = array();

		$this->_setParam('data', $this->_dataList);
		$this->_setParam('tm', $this->_tmList);
	}

	public function isEmpty()
	{
		$data = $this->getData();
		return empty($data);
	}


	private function _setParam($suffix, $data)
	{
		$this->_session->set($this->_name.'_'.$suffix, (array)$data, $this->_namespace);
	}

	private function _getParam($suffix)
	{
		$data = (array)$this->_session->get($this->_name.'_'.$suffix, array(), $this->_namespace);
		return $data;
	}

}
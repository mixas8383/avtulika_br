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
class PayMethod
{
	protected $_method;

	public function __construct($type = 'brao')
	{
		$filename = JPATH_LIBRARIES.'/project/paymethods/'.$type.'.php';
		$exists = file_exists($filename);

		if ($exists) {
			require_once($filename);
			if (class_exists($type)) {
				$this->_method = new $type();
			}
		}
	}


	public function __call($name, $args)
	{
		if (is_null($this->_method)) {
			return null;
		}

		if (method_exists($this->_method, $name)) {
			return call_user_func_array(array($this->_method, $name), $args);
		}
	}

	public function __get($name)
	{
		if (is_null($this->_method)) {
			return null;
		}

		return $this->_method->get($name);
	}

}

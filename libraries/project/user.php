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
class User
{

    private $_user;
    private $_db;
    private $_balance = 0;
    private static $_instances;

    public function __construct($identifier = 0, $field = 'id')
    {
        $this->_user = JFactory::getUser($identifier, $field);
        $this->_db = JFactory::getDBO();

        // load balances and more
        if (!empty($this->_user->id))
        {
            $query = $this->_db->getQuery(true);
            $query->select('balance');
            $query->from('#__users_balance');
            $query->where('user_id = ' . $this->_user->id);
            $query->limit(1);
            $this->_db->setQuery($query);
            $balance = $this->_db->loadResult();
            if (is_null($balance))
            {
                $query_str = "INSERT INTO #__users_balance SET user_id='" . $this->_user->id . "'";
                $this->_db->setQuery($query_str);
                $this->_db->query();
                $balance = 0;
            }
            $this->_balance = $balance;
        }
    }

    public static function getInstance($identifier = null, $field = 'id')
    {
        if (empty(self::$_instances[$field][$identifier]))
        {
            self::$_instances[$field][$identifier] = new self($identifier, $field);
        }
        return self::$_instances[$field][$identifier];
    }

    public function getBalance($as_minor = false)
    {
        $balance = $this->_balance;
        if (!$as_minor)
        {
            $balance = Balance::convertAsMajor($balance);
        }
        return $balance;
    }

    public function hasProfile()
    {
        $can = !empty($this->email) && !empty($this->persNumber);
        return $can;
    }

    public function isSubscribed()
    {
        $query = ' SELECT `mail_subscribed` '
                . ' FROM`#__users` '
                . ' WHERE `id`=' . $this->id . ' '
        ;
        $this->_db->setQuery($query);
        $mail_subscribed = $this->_db->loadResult();

        return $mail_subscribed == 1;
    }

    public function unsubscribe()
    {
        if (empty($this->id))
        {
            return false;
        }

        $query = 'UPDATE `#__users` '
                . ' SET `mail_subscribed`=0'
                . ' WHERE `id`=' . $this->id . ' '
        ;
        $this->_db->setQuery($query);
        $status = $this->_db->query();
        return $status;
    }

    public function __call($name, $args)
    {
        if (method_exists($this->_user, $name))
        {
            return call_user_func_array(array($this->_user, $name), $args);
        }
    }

    public function __get($name)
    {
        if (!($this->_user instanceof JUser))
        {
            return null;
        }
        return $this->_user->get($name);
    }

    public function __isset($name)
    {
        $val = $this->_user->get($name);
        return !empty($val);
    }

    public function getUserBids()
    {
        $this->_db->setQuery(''
                . ''
                . ' SELECT * FROM #__users WHERE id=' . $this->_user->id
                . ''
                . '');
        $us = $this->_db->loadObject();
        if (!empty($us))
        {
            return $us->bids;
        }
        return 0;
    }

}

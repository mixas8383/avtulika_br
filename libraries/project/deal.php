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
class Deal
{

    private $_data;
    private $_db;
    private $_app;
    private $_multipic;

    public function __construct($data)
    {
        $this->_db = JFactory::getDBO();
        $this->_app = JFactory::getApplication();
        $this->_multipic = JFactory::getMultipic();
        $this->_data = is_array($data) ? $data : $this->load($data);
    }

    public function load($id)
    {
        $query = $this->_db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $this->_db->quote($this->_db->getNullDate());


        $query->select('d.*');
        $query->from('#__deals_deals AS d');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

        // Join over the cities.
        $query->select('ci.title AS city_title');
        $query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

        // Join over the companies.
        $query->select('co.title AS company_title, co.mail AS company_mail, co.map AS company_map, co.description AS company_description, co.address AS company_address, co.phone AS company_phone, co.hours AS company_hours, co.fb_url AS company_fb_url, co.url AS company_url');
        $query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

        $query->where('d.id = ' . (int) $id);
        $query->where('d.state = 1');
        //$query->where('(d.publish_up = '.$nullDate.' OR d.publish_up <= '.$query->quote($now).')');
        //$query->where('(d.publish_down = '.$nullDate.' OR d.publish_down >= '.$query->quote($now).')');
        $query->limit(1);
        $this->_db->setQuery($query);
        $data = $this->_db->loadAssoc();
        return $data;
    }

    public function isSoldOut()
    {
        return $this->quantity > 0 && $this->sold >= $this->quantity;
    }

    public function isFinished()
    {
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $now_unix = $jdate->toUnix();

        $nullDate = $this->_db->getNullDate();

        $publish_up = 0;
        if ($this->publish_up != $nullDate)
        {
            $jdate = JFactory::getDate($this->publish_up);
            $publish_up = $jdate->toUnix();
        }

        $publish_down = 0;
        if ($this->publish_down != $nullDate)
        {
            $jdate = JFactory::getDate($this->publish_down);
            $publish_down = $jdate->toUnix();
        }

        if (($publish_up && $publish_up > $now_unix) || ($publish_down && $publish_down < $now_unix))
        {
            return true;
        }
        return false;
    }

    public function allowForBuy()
    {
        if ($this->isSoldOut())
        {
            return false;
        }

        if ($this->isFinished())
        {
            return false;
        }
        return true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {

        return JFilterOutput::clean($this->title);
    }

    public function getDescription()
    {

        return $this->description;
    }

    public function getVideo()
    {

        return $this->video;
    }

    public function getText()
    {

        return $this->text;
    }

    public function getSold()
    {
        return intval($this->sold);
    }

    public function getCompanyName()
    {
        return JFilterOutput::clean($this->company_title);
    }

    public function getCompanyMap()
    {
        return $this->company_map;
    }

    public function getCompanyDescription()
    {
        return $this->company_description;
    }

    public function getCompanyAddress()
    {
        return JFilterOutput::clean($this->company_address);
    }

    public function getCompanyPhone()
    {
        return JFilterOutput::clean($this->company_phone);
    }

    public function getCompanyFbUrl()
    {
        return JFilterOutput::clean($this->company_fb_url);
    }

    public function getCompanyHours()
    {
        return JFilterOutput::clean($this->company_hours);
    }

    public function getCompanyMail()
    {
        return JFilterOutput::clean($this->company_mail);
    }

    public function getCompanyUrl()
    {
        return JFilterOutput::clean($this->company_url);
    }

    public function getUrl($suffix = null)
    {
        if (!$this->id)
        {
            return false;
        }
        $Itemid = JMenu::getItemid('com_deals', 'deal');
        $link = 'index.php?option=com_deals&view=deal&id=' . $this->id . '&Itemid=' . $Itemid;
        if ($suffix)
        {
            $link .= $suffix;
        }
        if ($this->_app->isAdmin())
        {
            jimport('core.application.router');
            require_once (JPATH_ROOT . DS . 'includes' . DS . 'router.php');
            require_once (JPATH_ROOT . DS . 'includes' . DS . 'application.php');
            $router = new JRouterSite(array('mode' => JROUTER_MODE_SEF));
            $url = $router->build($link)->toString(array('path', 'query', 'fragment'));
            $url = str_replace('/' . JFOLDER_ADMINISTRATOR . '/', '', $url);
        } else
        {
            $url = JRoute::_($link, false);
        }
        return $url;
    }

    public function hit()
    {
        $id = $this->id;
        if (!$id)
        {
            return false;
        }
        $browser = JFactory::getBrowser();
        if ($browser->isRobot())
        {
            return false;
        }


        $query = "UPDATE `#__deals_deals` "
                . " SET `hits`=`hits`+1 "
                . " WHERE `id`='" . $id . "' "
                . " LIMIT 1"
        ;
        $this->_db->setQuery($query);
        $status = $this->_db->query();
        return $status;
    }

    public function isInstallment()
    {
        $installment = $this->installment;
        return $installment;
    }

    public function getPrice($as_minor = false)
    {
        $price = $this->price;
        return $as_minor ? $price : Balance::convertAsMajor($price);
    }

    public function getMonthly($as_minor = false)
    {
        $price = $this->monthly;
        return $as_minor ? $price : Balance::convertAsMajor($price);
    }

    public function getSaving($as_minor = false)
    {
        $saving = $this->saving;
        if (!$saving)
        {
            $saving = $this->old_price - $this->price;
        }
        return $as_minor ? $saving : Balance::convertAsMajor($saving);
    }

    public function getImage($num = 1, $size = 'image1')
    {
        $name = 'image' . $num;
        if (!$this->$name)
        {
            return false;
        }
        $img = $this->$name;


        $image = $this->_multipic->getImage($size, $img);

        return $image;
    }

    public function getOldPrice()
    {
        $price = $this->old_price;
        return Balance::convertAsMajor($price);
    }

    public function getFinishDate()
    {

        $now = JFactory::getDate();
        $bidDate = JFactory::getDate($this->bid_date);




        $date = $this->publish_down;
        $jdate = JFactory::getDate($date);




        if (($jdate->toUnix() - $now->toUnix()) > (60 * 60 * 24))
        {
            $jdate->setTimeZone('Asia/Tbilisi');




            $day = $jdate->format('j', true);
            $month = PDeals::getMonthName($jdate->format('n', true));
            $hour = $jdate->format('H:i', true);
            return JText::sprintf('COM_DEALS_DATE_FORMAT2', $day, $month, $hour);
        } else
        {
            
        }
    }

    public function getDiscount()
    {
        $discount = $this->discount;
        if (!$discount)
        {
            $price = $this->price;
            $old_price = $this->old_price;
            $diff = $old_price - $price;

            $percent = ($diff / $old_price) * 100;
            $discount = round($percent);
        }
        return $discount;
    }

    public function get($name)
    {
        return $this->$name;
    }

    public function __get($name)
    {
        if (!is_array($this->_data))
        {
            return null;
        }
        return array_key_exists($name, $this->_data) ? $this->_data[$name] : null;
    }

    public function __isset($name)
    {
        if (!is_array($this->_data))
        {
            return false;
        }
        return array_key_exists($name, $this->_data) && !empty($this->_data[$name]) ? true : false;
    }

}

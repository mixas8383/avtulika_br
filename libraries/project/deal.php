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
    private $_autobidTime;
    private $_autobidIncrement;

    public function __construct($data)
    {
        $this->_autobidTime = 2;
        $this->_autobidIncrement = 10;
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
            $finish = JText::sprintf('COM_DEALS_DATE_FORMAT2', $day, $month, $hour);
            $finish = JText::sprintf('COM_DEALS_DEALS_FINISH', $finish);

            return $finish;
        } else if (($jdate->toUnix() - $now->toUnix()) < (60 * 60 * 24) && ($jdate->toUnix() - $now->toUnix()) > 0)
        {// active 1 day counter bidding
            $houers = ($jdate->toUnix() - $now->toUnix());

            $timer = $this->_getTime($houers);

            ob_start();
            ?>
            <span class="timerCounter timerCounter_<?php echo $this->id; ?>">
                <span class="timer_counter_h">
                    <?php echo $timer->h; ?> 
                </span>:
                <span class="timer_counter_m">
                    <?php echo $timer->m; ?> 
                </span>:
                <span class="timer_counter_s">
                    <?php echo $timer->s; ?> 
                </span>

            </span>


            <?php
            $html = ob_get_clean();
            return $html;
        } else if (($jdate->toUnix() - $now->toUnix()) < 0 && ($bidDate->toUnix() - $now->toUnix()) > 0)
        {
            //if active bid
            $houers = ($bidDate->toUnix() - $now->toUnix());

            $timer = $this->_getTime($houers);

            ob_start();
            ?>
            <span class="timerCounter timerCounter_<?php echo $this->id; ?>">
                <span class="timer_counter_h">
                    <?php echo $timer->h; ?> 
                </span>:
                <span class="timer_counter_m">
                    <?php echo $timer->m; ?> 
                </span>:
                <span class="timer_counter_s">
                    <?php echo $timer->s; ?> 
                </span>
            </span>


            <?php
            $html = ob_get_clean();
            return $html;
        }
    }

    private function _getTime($time)
    {
        $ret = new stdClass();

        $ret->h = (int) ($time / 3600);
        $ret->m = (int) (($time - $ret->h * 3600) / 60);
        $ret->s = $time - $ret->h * 3600 - $ret->m * 60;
        if (strlen($ret->h) < 2)
        {
            $ret->h = '0' . $ret->h;
        }
        if (strlen($ret->m) < 2)
        {
            $ret->m = '0' . $ret->m;
        }
        if (strlen($ret->s) < 2)
        {
            $ret->s = '0' . $ret->s;
        }

        return $ret;
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

    public function doAutoDeal()
    {
        $autobid = $this->isTimeToAutobid();
        
         
        if ($autobid)
        {
            $autodealers = $this->getAutoDealers();
 

            if (!empty($autodealers))
            {
                $nextBidder = $this->getNextAutobider();

                $this->doNextBid($nextBidder->user_id, $nextBidder->bot, 1);
            }
        }
    }

    public function getAutoDealers()
    {
        if (!empty($this->_autobids))
        {
            return $this->_autobilds;
        }

        $addWhere = '';
        if ($this->real_bids >= $this->bot_max_bit)
        {
            $addWhere = ' and a.bot!=1';
        }

        $this->_db->setQuery(''
                . ''
                . 'SELECT * from #__deals_autobit AS a'
                . ' LEFT JOIN #__users as u on u.id=a.user_id'
                . ''
                . ' WHERE a.deal_id=' . $this->id
                . ' AND u.bids > 0 '
                . $addWhere
                . ' order by a.id asc'
                . '');
        $this->_autobilds = $this->_db->loadObjectList();


        return $this->_autobilds;
    }

    public function getNextAutobider()
    {
        $dillers = $this->getAutoDealers();

        if (count($dillers) > 1)
        {
            $markedDiller = 0;
            $findMarker = false;
            for ($i = 0; $i < count($dillers); $i++)
            {
                if ($dillers[$i]->marker == 1)
                {
                    $findMarker = true;
                    $markedDiller = $i;
                    break;
                }
            }
            if ($findMarker)
            {
                if (isset($dillers[$markedDiller + 1]))
                {
                    return $dillers[$markedDiller + 1];
                }
            }
        }
        return $dillers[0];
    }

    public function isTimeToAutobid()
    {
        $nowDate = JFactory::getDate();
        $lastTime = JFactory::getDate($this->bid_date);

        if (($lastTime->toUnix() - $nowDate->toUnix()) < $this->_autobidTime)
        {
            return true;
        }
        return false;
    }

    public function getLastBid()
    {
        $this->_db->setQuery(''
                . ''
                . ' Select * from #__deals_bids where deal_id=' . $this->id
                . ' order by id desc limit 1'
                . ''
                . '');
        $lastBid = $this->_db->loadObject();


        return $lastBid;
    }

    public function doNextBid($userId, $bot = 0, $autobid = 0)
    {
         if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'Debug')
         {
             echo '<pre>'.__FILE__.' -->>| <b> Line </b>'.__LINE__.'</pre><pre>';
             print_r($userId);
             die;
             
         }
        
        
        $date = JFactory::getDate();
        $this->_db->setQuery(''
                . 'INSERT INTO #__deals_bids (`user_id`,`deal_id`,`bot`,`date`,`autobid`) VALUES'
                . '(' . $userId . ''
                . ',' . $this->id
                . ',' . $bot
                . ',' . $this->_db->quote($date->toSql())
                . ',' . $autobid
                . ')'
                . '');
        $this->_db->execute();
        $lastTime = JFactory::getDate($this->bid_date);
        $newTime = JFactory::getDate($lastTime->toUnix() + $this->_autobidIncrement);

        $dealSets = array();
        if ($bot)
        {
            $dealSets[] = 'bid_date=' . $this->_db->quote($newTime->toSql());
            $dealSets[] = 'total_bids=total_bids+1';
        } else
        {
            $dealSets[] = 'bid_date=' . $this->_db->quote($newTime->toSql());
            $dealSets[] = 'total_bids=total_bids+1';
            $dealSets[] = 'real_bids=real_bids+1';
        }
        $this->_db->setQuery(''
                . ''
                . 'UPDATE #__deals_deals SET ' . implode(',', $dealSets)
                . ' where id=' . $this->id
                . '');
        $this->_db->execute();
        if (!$bot)
        {
            $this->_db->setQuery(''
                    . ''
                    . 'UPDATE #__users SET bids=bids-1' . $this->_db->quote($newTime->toSql())
                    . ' WHERE id=' . $userId
                    . '');
            $this->_db->execute();
        }
        if ($autobid)
        {
            $this->placeAutobidderMarker($userId);
        }
        return;
    }

    private function placeAutobidderMarker($userId)
    {
        $this->_db->setQuery(''
                . ''
                . 'update #__deals_autobit set marker=0'
                . ' where deal_id=' . $this->id
                . '');
        $this->_db->execute();
        $this->_db->setQuery(''
                . ''
                . 'update #__deals_autobit set marker=1'
                . ' where deal_id=' . $this->id
                . ' and user_id=' . $userId
                . '');
        $this->_db->execute();
        returns;
    }

}

<?php

/**
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * This models supports retrieving lists of contact categories.
 *
 * @package    LongCMS.Site
 * @subpackage    com_contact
 * @since        1.6
 */
class DealsModelBids extends JModelLegacy
{

    public $_context = 'com_deals.bids';
    protected $_extension = 'com_deals';

    public function getItems()
    {
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $category = $jinput->get->getUint('cat');

        $items = PDeals::getDeals($category, null, null, true);
        return $items;
    }

    public function getCategories()
    {
        $items = PDeals::getCategories(true);
        return $items;
    }

    public function makeUserBid()
    {
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $id = $app->input->getInt('id', 0);

        if (empty($id))
        {
            $this->setState('bids.error', JText::_('LOT_NOT_ACTIVE'));
            $this->setState('bids.show_error', 1);
            return false;
        }
        if (empty($user->id))
        {
            $this->setState('bids.error', JText::_('PLEAS_LOGIN_FIRST'));
            $this->setState('bids.show_error', 1);
            return false;
        }
        jimport('project.user');



        $myUser = new User($user->id);



        $bids = $myUser->getUserBids();




        if (empty($bids))
        {
            $this->setState('bids.error', JText::_('BIBS_EMPTY'));
            $this->setState('bids.show_message', 1);
            return false;
        } else
        {
            $lot = PDeals::loadActiveLot($id);
            
            
            
            if (empty($lot))
            {
                $this->setState('bids.error', JText::_('LOT_NOT_ACTIVE'));
                $this->setState('bids.show_error', 1);
                return false;
            }

            //$lot->publish_down
            //$lot->bid_date

            $publish_down = JFactory::getDate($lot->publish_down);
            $bid_date = JFactory::getDate($lot->bid_date);
            $now = JFactory::getDate();

            if ($publish_down->toUnix() < $now->toUnix())
            {
                $lot->doNextBid($user->id);
            } else
            {
                $lot->doNextBid($user->id,0,0,0);
            }

            return $bids;
        }
    }

}

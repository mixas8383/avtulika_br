<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('project.deals.deals');

/**
 * This models supports retrieving lists of contact categories.
 *
 * @package	LongCMS.Site
 * @subpackage	com_contact
 * @since		1.6
 */
class DealsModelDeal extends JModelLegacy
{
	public $_context = 'com_deals.deal';
	protected $_extension = 'com_deals';
	protected $_deal;


	public function getItem()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$id = $jinput->get->getUint('id', 0);
		$this->_deal = new Deal($id);
		return $this->_deal;
	}

	public function getRelated()
	{
		if (empty($this->_deal->category_id)) {
			return false;
		}
		$deals = PDeals::getDeals($this->_deal->category_id, 5, 'RAND()');
		return $deals;
	}



}

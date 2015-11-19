<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This models supports retrieving lists of contact categories.
 *
 * @package	LongCMS.Site
 * @subpackage	com_contact
 * @since		1.6
 */
class DealsModelExDeals extends JModelLegacy
{
	public $_context = 'com_deals.exdeals';
	protected $_extension = 'com_deals';
	protected $_pagination;


	public function getItems()
	{

		$data = PDeals::getExDeals();
		$items = $data->data;
		$this->_pagination = $data->pagination;


		return $items;
	}

	public function getPagination()
	{
		return $this->_pagination;
	}

}

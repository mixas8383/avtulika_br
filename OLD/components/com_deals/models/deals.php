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
class DealsModelDeals extends JModelLegacy
{
	public $_context = 'com_deals.deals';
	protected $_extension = 'com_deals';


	public function getItems()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$category = $jinput->get->getUint('cat');

		$items = PDeals::getDeals($category);
		return $items;
	}


	public function getCategories()
	{
		$items = PDeals::getCategories(true);
		return $items;
	}
}

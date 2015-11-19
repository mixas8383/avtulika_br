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
class DealsModelBuy extends JModelLegacy
{
	public $_context = 'com_deals.buy';
	protected $_extension = 'com_deals';


	public function getItems()
	{
		$app = JFactory::getApplication();
		$session = JFactory::getSession();
		$sesdeals = (array)$session->get('deals');
		$user = JFactory::getUser();

		if (!$user->id) {
			//$dealItemid = JMenu::getItemid('com_deals', 'deal');
			//$dealurl = JRoute::_('index.php?option=com_deals&view=deal&id='.$dealurl.'Itemid='.$dealItemid, false);
			//$app->setUserState('users.login.form.return', $dealurl);
			$loginItemid = JMenu::getItemid('com_users', 'login');
			$app->enqueueMessage(JText::_('COM_DEALS_BUY_MUSTLOGIN'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&Itemid='.$loginItemid, false));
			return false;
		}

		$deals = array();
		foreach($sesdeals as $id) {
			$deal = new Deal((int)$id);
			if (!$deal->allowForBuy()) {
				continue;
			}
			$deals[] = $deal;
		}

		if (!count($deals)) {
			$app->enqueueMessage(JText::_('COM_DEALS_BUY_DEALS_EMPTY'), 'error');
			$app->redirect(JRoute::_('index.php', false));
			return false;
		}
		return $deals;
	}


}

<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.modellist');

/**
 * This models supports retrieving lists of contact categories.
 *
 * @package	LongCMS.Site
 * @subpackage	com_contact
 * @since		1.6
 */
class DealsModelTransactions extends JModelList
{
	public $_context = 'com_deals.transactions';
	protected $_extension = 'com_deals';


	protected function getListQuery()
	{
		$db		= $this->getDbo();
		$query		= $db->getQuery(true);
		$nullDate	= $db->quote($db->getNullDate());
		$user		= JFactory::getUser();
		$app		= JFactory::getApplication();
		$jinput		= $app->input;

 		// set limit
 		$this->setState('list.limit', 10);
		$limitstart = $jinput->get->getUInt('start', 0);
		$this->setState('list.start', $limitstart);


		// check user
		if (!$user->id) {
			$app->enqueueMessage(JText::_('COM_DEALS_MUSTLOGIN'), 'error');
			$app->redirect(JRoute::_('index.php', false));
			return false;
		}

		$query->select(
				't.*,'.
				'td.deal_id as deal_id'
			);
		$query->from('#__deals_transactions as t');
		$query->join('LEFT', '#__deals_transactions_data AS td ON t.id = td.transaction_id');

		$query->where('t.user_id='.$user->id);
		$query->where('t.status!=1');

		$query->order('t.date DESC');
		return $query;
	}

	public function getTransaction()
	{
		$db		= $this->getDbo();
		$query		= $db->getQuery(true);
		$nullDate	= $db->quote($db->getNullDate());
		$user		= JFactory::getUser();
		$app		= JFactory::getApplication();
		$jinput		= $app->input;

		$tid = $jinput->get->getUint('tid');


		$query->select(
				't.*, '.
				'td.deal_id as deal_id, '.
				'd.*, '.
				'CONCAT(u.name, " ", u.surname) AS user_fullname, u.persNumber AS user_persNumber, '.
				'c.title AS company_name, c.description AS company_description, c.mail AS company_mail, c.address AS company_address, c.phone AS company_phone, c.hours AS company_hours, c.fb_url AS company_fb_url, c.url AS company_url'
			);
		$query->from('#__deals_transactions as t');
		$query->join('INNER', '#__deals_transactions_data AS td ON t.id = td.transaction_id');
		$query->join('INNER', '#__deals_deals AS d ON td.deal_id = d.id');
		$query->join('INNER', '#__deals_companies AS c ON d.company_id = c.id');
		$query->join('INNER', '#__users AS u ON t.user_id = u.id');

		$query->where('t.user_id='.$user->id);
		$query->where('t.status!=1');
		$query->where('t.id='.$tid);

		$db->setQuery($query);
		$data = $db->loadObject();


		return $data;
	}


}

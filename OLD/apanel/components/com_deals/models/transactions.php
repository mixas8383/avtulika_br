<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.modellist');

/**
 * Methods supporting a list of banner records.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class DealsModelTransactions extends JModelList
{

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				't.*'
			)
		);

		//$query->select('SUM(t.amount) AS total_amount');


		$query->from($db->quoteName('#__deals_transactions').' AS t');


		$query->select('CONCAT(uc.name, " ", uc.surname) AS user_name, uc.email as user_mail, uc.id as user_id, uc.persNumber as user_persNumber, uc.phone as user_phone, uc.mobile as user_mobile');
		$query->join('LEFT', '#__users AS uc ON uc.id=t.user_id');


		$query->join('LEFT', '#__deals_transactions_data AS td ON t.id=td.transaction_id');


		$query->select('d.title AS deal_title, d.image1 AS deal_image, d.price AS deal_price, d.id AS deal_id');
		$query->join('LEFT', '#__deals_deals AS d ON td.deal_id=d.id');


		$published = $this->getState('filter.state');
		if (!empty($published)) {
			if ($published == -1) {
				$published = 0;
			}
			$query->where('t.status = '.(int) $published);
		}


		$datefrom = $this->getState('filter.datefrom');
		if (!empty($datefrom)) {
			$datefrom .= ' 00:00:00';
			$jdate = JFactory::getDate($datefrom, 'Asia/Tbilisi');
			$date = $jdate->toSql();
			$query->where('t.date >= "'.$date.'"');
		}

		$datetill = $this->getState('filter.datetill');
		if (!empty($datetill)) {
			$datetill .= ' 23:59:59';
			$jdate = JFactory::getDate($datetill, 'Asia/Tbilisi');
			$date = $jdate->toSql();
			$query->where('t.date <= "'.$date.'"');
		}

		$type = $this->getState('filter.type');
		if (!empty($type)) {
			$query->where('t.type = '.(int) $type);
		}

		$paymethod = $this->getState('filter.paymethod');
		if (!empty($paymethod)) {
			$query->where('t.payment_method = '.(int) $paymethod);
		}

		$pid = $this->getState('filter.pid');
		if (!empty($pid)) {
			$query->where('td.deal_id = '.(int) $pid);
		}

		$uid = $this->getState('filter.uid');
		if (!empty($uid)) {
			$query->where('t.user_id = '.(int) $uid);
		}


		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$escaped_search = $db->Quote('%'.$db->escape($search, true).'%');
			$srch_where = array();
			$srch_where[] = 'd.title LIKE '.$escaped_search;
			$srch_where[] = 'uc.name LIKE '.$escaped_search;
			$srch_where[] = 'uc.surname LIKE '.$escaped_search;
			$srch_where[] = 't.transaction_number = '.$db->quote($search);
			$where = '('.implode(') OR (', $srch_where).')';
			$query->where($where);
		}

		//$query->group_by('t.id');


		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 't.date');
		$orderDirn	= $this->state->get('list.direction', 'DESC');

		$query->order($db->escape($orderCol.' '.$orderDirn));
		return $query;
	}

	public function getTotalAmount()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('SUM(t.amount) AS total_amount');


		$query->from($db->quoteName('#__deals_transactions').' AS t');


		$query->select('CONCAT(uc.name, " ", uc.surname) AS user_name, uc.email as user_mail, uc.id as user_id, uc.persNumber as user_persNumber, uc.phone as user_phone, uc.mobile as user_mobile');
		$query->join('LEFT', '#__users AS uc ON uc.id=t.user_id');


		$query->join('LEFT', '#__deals_transactions_data AS td ON t.id=td.transaction_id');


		$query->select('d.title AS deal_title, d.image1 AS deal_image, d.price AS deal_price');
		$query->join('LEFT', '#__deals_deals AS d ON td.deal_id=d.id');


		$published = $this->getState('filter.state');
		if (!empty($published)) {
			if ($published == -1) {
				$published = 0;
			}
			$query->where('t.status = '.(int) $published);
		}


		$datefrom = $this->getState('filter.datefrom');
		if (!empty($datefrom)) {
			$datefrom .= ' 00:00:00';
			$jdate = JFactory::getDate($datefrom, 'Asia/Tbilisi');
			$date = $jdate->toSql();
			$query->where('t.date >= "'.$date.'"');
		}

		$datetill = $this->getState('filter.datetill');
		if (!empty($datetill)) {
			$datetill .= ' 23:59:59';
			$jdate = JFactory::getDate($datetill, 'Asia/Tbilisi');
			$date = $jdate->toSql();
			$query->where('t.date <= "'.$date.'"');
		}

		$type = $this->getState('filter.type');
		if (!empty($type)) {
			$query->where('t.type = '.(int) $type);
		}

		$paymethod = $this->getState('filter.paymethod');
		if (!empty($paymethod)) {
			$query->where('t.payment_method = '.(int) $paymethod);
		}

		$pid = $this->getState('filter.pid');
		if (!empty($pid)) {
			$query->where('td.deal_id = '.(int) $pid);
		}

		$uid = $this->getState('filter.uid');
		if (!empty($uid)) {
			$query->where('t.user_id = '.(int) $uid);
		}


		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$escaped_search = $db->Quote('%'.$db->escape($search, true).'%');
			$srch_where = array();
			$srch_where[] = 'd.title LIKE '.$escaped_search;
			$srch_where[] = 'uc.name LIKE '.$escaped_search;
			$srch_where[] = 'uc.surname LIKE '.$escaped_search;
			$srch_where[] = 't.transaction_number = '.$db->quote($search);
			$where = '('.implode(') OR (', $srch_where).')';
			$query->where($where);
		}

		$query->group_by('t.id');



		$db->setQuery($query);

		$total = $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		return $total;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Transaction', $prefix = 'DealsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$pid = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.pid', 'filter_pid');
		$this->setState('filter.pid', $pid);

		$uid = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.uid', 'filter_uid');
		$this->setState('filter.uid', $uid);

		$state = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$datefrom = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.datefrom', 'filter_datefrom', '', 'string');
		$this->setState('filter.datefrom', $datefrom);

		$datetill = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.datetill', 'filter_datetill', '', 'string');
		$this->setState('filter.datetill', $datetill);

		$type = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.type', 'filter_type', '', 'string');
		$this->setState('filter.type', $type);

		$paymethod = $this->getUserStateFromRequest(JCOMPONENT.'.'.JVIEW.'.filter.paymethod', 'filter_paymethod', '', 'string');
		$this->setState('filter.paymethod', $paymethod);

		// Load the parameters.
		$params = JComponentHelper::getParams(JCOMPONENT);
		$this->setState('params', $params);

		// List state information.
		parent::populateState('t.date', 'desc');
	}
}

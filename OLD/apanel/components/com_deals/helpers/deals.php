<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Deals component helper.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class DealsHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		$user = JFactory::getUser();


		JSubMenuHelper::addEntry(
			JText::_('COM_DEALS_SUBMENU_DEALS'),
			'index.php?option='.JCOMPONENT.'&view=deals',
			$vName == 'deals'
		);

		if ($user->authorise('deals.categories.manage', JCOMPONENT)) {
			JSubMenuHelper::addEntry(
				JText::_('COM_DEALS_SUBMENU_CATEGORIES'),
				'index.php?option='.JCOMPONENT.'&view=categories',
				$vName == 'categories'
			);
		}

		if ($user->authorise('deals.companies.manage', JCOMPONENT)) {
			JSubMenuHelper::addEntry(
				JText::_('COM_DEALS_SUBMENU_COMPANIES'),
				'index.php?option='.JCOMPONENT.'&view=companies',
				$vName == 'companies'
			);
		}

		if ($user->authorise('deals.cities.manage', JCOMPONENT)) {
			JSubMenuHelper::addEntry(
				JText::_('COM_DEALS_SUBMENU_CITIES'),
				'index.php?option='.JCOMPONENT.'&view=cities',
				$vName == 'cities'
			);
		}

		if ($user->authorise('deals.transactions.manage', JCOMPONENT)) {
			JSubMenuHelper::addEntry(
				JText::_('COM_DEALS_SUBMENU_TRANSACTIONS'),
				'index.php?option='.JCOMPONENT.'&view=transactions',
				$vName == 'transactions'
			);
		}

		if ($user->authorise('deals.liberty.manage', JCOMPONENT)) {
			JSubMenuHelper::addEntry(
				JText::_('COM_DEALS_SUBMENU_LIBERTY'),
				'index.php?option='.JCOMPONENT.'&view=liberty',
				$vName == 'liberty'
			);
		}

		if ($user->authorise('deals.users.manage', JCOMPONENT)) {
			JSubMenuHelper::addEntry(
				JText::_('COM_DEALS_SUBMENU_USERS'),
				'index.php?option='.JCOMPONENT.'&view=users',
				$vName == 'users'
			);
		}
		if ($user->authorise('deals.mailjobs.manage', JCOMPONENT)) {
			JSubMenuHelper::addEntry(
				JText::_('COM_DEALS_SUBMENU_MAILJOBS'),
				'index.php?option='.JCOMPONENT.'&view=mailjobs',
				$vName == 'mailjobs'
			);
		}


	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
		$assetName = JCOMPONENT;
		$level = 'component';
		$actions = JAccess::getActions(JCOMPONENT, $level);
		foreach ($actions as $action) {
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}
		return $result;
	}

	public static function getStateOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = JHtml::_('select.option', '1', JText::_('COM_DEALS_FILTER_ENABLED'));
		$options[] = JHtml::_('select.option', '0', JText::_('COM_DEALS_FILTER_DISABLED'));
		return $options;
	}

	public static function getCategoryOptions()
	{
		$data = PDeals::getCategories();
		$options = array();
		foreach($data as $row) {
			$options[] = JHtml::_('select.option', $row->id, $row->title);
		}
		return $options;
	}

	public static function getCompanyOptions()
	{
		$data = PDeals::getCompanies();
		$options = array();
		foreach($data as $row) {
			$options[] = JHtml::_('select.option', $row->id, $row->title);
		}
		return $options;
	}

	public static function getCityOptions()
	{
		$data = PDeals::getCities();
		$options = array();
		foreach($data as $row) {
			$options[] = JHtml::_('select.option', $row->id, $row->title);
		}
		return $options;
	}


}

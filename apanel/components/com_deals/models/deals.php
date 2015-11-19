<?php
/**
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.modellist');

/**
 * Methods supporting a list of banner records.
 *
 * @package    LongCMS.Administrator
 * @subpackage    com_banners
 * @since        1.6
 */
class DealsModelDeals extends JModelList
{
    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'cid', 'a.cid', 'client_name',
                'name', 'a.name',
                'alias', 'a.alias',
                'state', 'a.state',
                'ordering', 'a.ordering',
                'language', 'a.language',
                'catid', 'a.catid', 'category_title',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'created', 'a.created',
                'impmade', 'a.impmade',
                'imptotal', 'a.imptotal',
                'clicks', 'a.clicks',
                'publish_up', 'a.publish_up',
                'publish_down', 'a.publish_down',
                'state', 'sticky', 'a.sticky',
            );
        }

        parent::__construct($config);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    JDatabaseQuery
     * @since    1.6
     */
    protected function getListQuery()
    {
        // Initialise variables.
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.*'
            )
        );
        $query->from($db->quoteName('#__deals_deals') . ' AS a');

        // Join over the users for the checked out user.
        $query->select('CONCAT(uc.name, " ", uc.surname) AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__deals_categories AS c ON c.id = a.category_id');

        // Join over the cities.
        $query->select('ci.title AS city_title');
        $query->join('LEFT', '#__deals_cities AS ci ON ci.id = a.city_id');

        // Join over the companies.
        $query->select('co.title AS company_title');
        $query->join('LEFT', '#__deals_companies AS co ON co.id = a.company_id');

        // Filter by published state
        $published = $this->getState('filter.state');

        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } elseif ('' === $published) {
            $query->where('(a.state IN (0, 1))');
        }

        $market = $this->getState('filter.market');

        if (is_numeric($market)) {

            if (1 == $market) {
                $query->where('a.is_market = 1');
            } else if (2 == $market) {
                $query->where('a.is_market != 1');
            }
        }

        // Filter by category.
        $categoryId = $this->getState('filter.category_id');
        if (!empty($categoryId)) {
            $query->where('a.category_id = ' . (int) $categoryId);
        }

        // Filter by client.
        $city_id = $this->getState('filter.city_id');
        if (!empty($city_id)) {
            $query->where('a.city_id = ' . (int) $city_id);
        }

        // Filter by company.
        $company_id = $this->getState('filter.company_id');
        if (!empty($company_id)) {
            $query->where('a.company_id = ' . (int) $company_id);
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $escaped_search = $db->Quote('%' . $db->escape($search, true) . '%');
            $srch_where     = array();
            $srch_where[]   = 'a.title LIKE ' . $escaped_search . '';
            $srch_where[]   = 'a.text LIKE ' . $escaped_search . '';
            $srch_where[]   = 'a.id = ' . $db->quote($search);
            $where          = '(' . implode(') OR (', $srch_where) . ')';
            $query->where($where);
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        if ('ordering' == $orderCol || 'a.ordering' == $orderCol) {
            $orderCol = 'a.ordering';
        }

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

        //echo nl2br(str_replace('#__','jos_',$query));
        return $query;
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     * @return    JTable    A database object
     * @since    1.6
     */
    public function getTable($type = 'Deal', $prefix = 'DealsTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $this->getUserStateFromRequest(JCOMPONENT . '.' . JVIEW . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $state = $this->getUserStateFromRequest(JCOMPONENT . '.' . JVIEW . '.filter.state', 'filter_state', '', 'string');
        $this->setState('filter.state', $state);

        $market = $this->getUserStateFromRequest(JCOMPONENT . '.' . JVIEW . '.filter.market', 'filter_market', '', 'string');
        $this->setState('filter.market', $market);

        $category_id = $this->getUserStateFromRequest(JCOMPONENT . '.' . JVIEW . '.filter.category_id', 'filter_category_id', '');
        $this->setState('filter.category_id', $category_id);

        $company_id = $this->getUserStateFromRequest(JCOMPONENT . '.' . JVIEW . '.filter.company_id', 'filter_company_id', '');
        $this->setState('filter.company_id', $company_id);

        $city_id = $this->getUserStateFromRequest(JCOMPONENT . '.' . JVIEW . '.filter.city_id', 'filter_city_id', '');
        $this->setState('filter.city_id', $city_id);

        // Load the parameters.
        $params = JComponentHelper::getParams(JCOMPONENT);
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.ordering', 'asc');
    }

    public function soldplus($id)
    {
        // Create a new query object.
        $db    = $this->getDbo();
        $query = "UPDATE `#__deals_deals` "
        . " SET `sold`=`sold`+1 "
        . " WHERE id=" . $db->quote($id) . " "
        . "LIMIT 1 "
        ;
        $db->setQuery($query);
        $status = $db->query();
        return $status;
    }

    public function relaunch($idx, $date)
    {
        // Create a new query object.
        $db    = $this->getDbo();
        $query = "UPDATE `#__deals_deals` "
        . " SET `publish_down`=" . $db->quote($date) . " "
        . " WHERE `id` IN(" . implode(',', $idx) . ") "
        ;

        $db->setQuery($query);
        $status = $db->query();
        return $status;
    }

}

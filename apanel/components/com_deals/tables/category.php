<?php
/**
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Banner table
 *
 * @package    LongCMS.Administrator
 * @subpackage    com_banners
 * @since        1.5
 */
class DealsTableCategory extends JTable
{
    /**
     * Constructor
     *
     * @since    1.5
     */
    public function __construct($_db)
    {
        parent::__construct('#__deals_categories', 'id', $_db);
        $date          = JFactory::getDate();
        $this->created = $date->toSql();
    }

    /**
     * Overloaded check function
     *
     * @return    boolean
     * @see        JTable::check
     * @since    1.5
     */
    public function check()
    {
        // Set name
        if (empty($this->title)) {
            $this->setError(JText::_('COM_DEALS_WARNING_PROVIDE_VALID_TITLE'));
            return false;
        }

        return true;
    }

    public function store($updateNulls = false)
    {
        $this->level = 1;
        if ($this->parent) {
            $id = $this->parent;

            $table = JTable::getInstance('Category', 'DealsTable');
            $table->load($id);
            if ($table->id != $this->parent || $this->id == 0) {
                $this->setError(JText::_('COM_CONTACT_ERROR_UNIQUE_ALIAS'));
                return false;
            }
            $this->level = $table->level + 1;
        }

        $status = parent::store($updateNulls);
        return $status;
    }

}

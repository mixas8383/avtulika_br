<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Banner table
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.5
 */
class DealsTableDeal extends JTable
{

    /**
     * Constructor
     *
     * @since	1.5
     */
    public function __construct($_db)
    {
        parent::__construct('#__deals_deals', 'id', $_db);
    }

    public function load($keys = null, $reset = true)
    {
        $status = parent::load($keys, $reset);
        return $status;
    }

    /**
     * Overloaded check function
     *
     * @return	boolean
     * @see		JTable::check
     * @since	1.5
     */
    public function check()
    {
        // Set name
        if (empty($this->title))
        {
            $this->setError(JText::_('COM_DEALS_WARNING_PROVIDE_VALID_TITLE'));
            return false;
        }

        if (empty($this->text))
        {
            $this->setError(JText::_('COM_DEALS_WARNING_PROVIDE_VALID_TEXT'));
            return false;
        }



        if ($this->price > 0)
        {
            $this->price = Balance::convertAsMinor($this->price);
        }
        if ($this->old_price > 0)
        {
            $this->old_price = Balance::convertAsMinor($this->old_price);
        }
        if ($this->saving > 0)
        {
            $this->saving = Balance::convertAsMinor($this->saving);
        }
        if ($this->comission > 0)
        {
            $this->comission = Balance::convertAsMinor($this->comission);
        }
        if ($this->monthly > 0)
        {
            $this->monthly = Balance::convertAsMinor($this->monthly);
        }



        // Check the publish down date is not earlier than publish up.
        if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
        {
            $this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
            return false;
        }

        // Clean up keywords -- eliminate extra spaces between phrases
        // and cr (\r) and lf (\n) characters from string
        if (!empty($this->metakey))
        {
            // Only process if not empty
            $bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
            $after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
            $keys = explode(',', $after_clean); // create array using commas as delimiter
            $clean_keys = array();

            foreach ($keys as $key)
            {
                if (trim($key))
                {
                    // Ignore blank keywords
                    $clean_keys[] = trim($key);
                }
            }
            $this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
        }

        return true;
    }

    public function store($updateNulls = false)
    {
        $date = JFactory::getDate();
        $user = JFactory::getUser();
        $isNew = !$this->id;
        
        $date->add($interval);
        
        $publishDownDate = JFactory::getDate($this->publish_down);
        
        $bitDate = JFactory::getDate($publishDownDate->toUnix()+10);
        
        
 
 
 
 $this->bid_date = $bitDate->toSql();
        if ($this->id)
        {
            $this->modified = $date->toSql();
            $this->modified_by = $user->get('id');
        } else
        {
            if (!intval($this->created))
            {
                $this->created = $date->toSql();
            }

            if (empty($this->created_by))
            {
                $this->created_by = $user->get('id');
            }
        }



        $status = parent::store($updateNulls);

        $this->_doSomeJobAfterSave();

        if ($status && $isNew)
        {
            $query = $this->_db->getQuery(true);
            $query->update($this->_db->quoteName($this->_tbl));
            $query->set($this->_db->quoteName('ordering') . ' = ' . $this->_db->quoteName('ordering') . '+1');
            $this->_db->setQuery($query);
            $this->_db->execute();
        }

        return $status;
    }

    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                $this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
                return false;
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
        {
            $checkin = '';
        } else
        {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Get the JDatabaseQuery object
        $query = $this->_db->getQuery(true);

        // Update the publishing state for rows with the given primary keys.
        $query->update($this->_db->quoteName($this->_tbl));
        $query->set($this->_db->quoteName('state') . ' = ' . (int) $state);
        $query->where('(' . $where . ')' . $checkin);
        $this->_db->setQuery($query);
        $this->_db->execute();

        // Check for a database error.
        if ($this->_db->getErrorNum())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks))
        {
            $this->state = $state;
        }

        $this->setError('');

        return true;
    }

    private function _doSomeJobAfterSave()
    {
        if (empty($this->id))
        {
            return;
        }

        $this->_db->setQuery(''
                . 'DELETE  from #__users where deal_id=' . $this->id . ' AND bot=1 '
                . '');
        $this->_db->execute();

        if ($this->allow_bot)
        {
            // insert into autobit table some bot users
            $count = 5;

            if (!empty($this->bot_count))
            {
                $count = $this->bot_count;
            }
            $this->_db->setQuery(''
                    . 'SELECT * from #__users where bot=1 order by RAND() limit ' . $count
                    . '');
            $users = $this->_db->loadObjectList();
            if (!empty($users))
            {
                foreach ($users as $one)
                {
                    $this->_db->setQuery(''
                            . 'INSERT INTO #__deals_autobit (user_id,deal_id,bot) VALUES '
                            . '(' . $one->id . ',' . $this->id . ',1)'
                            . '');
                    $this->_db->execute();
                }
            }
        }
       
        
        return true;
    }

}

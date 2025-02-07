<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Adapter Instance Class
 *
 * @package     	LongCMS.Platform
 * @subpackage  Base
 * @since       11.1
 */
class JAdapterInstance extends JObject
{
	/**
	 * Parent
	 *
	 * @var    JInstaller
	 * @since  11.1
	 */
	protected $parent = null;

	/**
	 * Database
	 *
	 * @var    JDatabase
	 * @since  11.1
	 */
	protected $db = null;

	/**
	 * Constructor
	 *
	 * @param   JAdapter   &$parent  Parent object
	 * @param   JDatabase  &$db      Database object
	 * @param   array      $options  Configuration Options
	 *
	 * @since   11.1
	 */
	public function __construct(&$parent, &$db, $options = array())
	{
		// Set the properties from the options array that is passed in
		$this->setProperties($options);

		// Set the parent and db in case $options for some reason overrides it.
		$this->parent = &$parent;

		// Pull in the global dbo in case something happened to it.
		$this->db = $db ? $db : JFactory::getDBO();
	}

	/**
	 * Retrieves the parent object
	 *
	 * @return  object parent
	 *
	 * @since   11.1
	 */
	public function getParent()
	{
		return $this->parent;
	}
}

<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Installer
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Extension object
 *
 * @package     	LongCMS.Platform
 * @subpackage  Installer
 * @since       11.1
 */
class JExtension extends JObject
{
	/**
	 * Filename of the extension
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $filename = '';

	/**
	 * Type of the extension
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = '';

	/**
	 * Unique Identifier for the extension
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $id = '';

	/**
	 * The status of the extension
	 *
	 * @var    boolean
	 * @since  11.1
	 */
	public $published = false;

	/**
	 * String representation of client. Valid for modules, templates and languages.
	 * Set by default to site.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $client = 'site';

	/**
	 * The group name of the plugin. Not used for other known extension types (only plugins)
	 *
	 * @var string
	 * @since  11.1
	 */
	public $group = '';

	/**
	 * An object representation of the manifest file stored metadata
	 *
	 * @var object
	 * @since  11.1
	 */
	public $manifest_cache = null;

	/**
	 * An object representation of the extension params
	 *
	 * @var    object
	 * @since  11.1
	 */
	public $params = null;

	/**
	 * Constructor
	 *
	 * @param   SimpleXMLElement  $element  A SimpleXMLElement from which to load data from
	 *
	 * @since  11.1
	 */
	public function __construct(SimpleXMLElement $element = null)
	{
		if ($element)
		{
			$this->type = (string) $element->attributes()->type;
			$this->id = (string) $element->attributes()->id;

			switch ($this->type)
			{
				case 'component':
					// By default a component doesn't have anything
					break;

				case 'module':
				case 'template':
				case 'language':
					$this->client = (string) $element->attributes()->client;
					$tmp_client_id = JApplicationHelper::getClientInfo($this->client, 1);
					if ($tmp_client_id == null)
					{
						JError::raiseWarning(100, JText::_('JLIB_INSTALLER_ERROR_EXTENSION_INVALID_CLIENT_IDENTIFIER'));
					}
					else
					{
						$this->client_id = $tmp_client_id->id;
					}
					break;

				case 'plugin':
					$this->group = (string) $element->attributes()->group;
					break;

				default:
					// Catch all
					// Get and set client and group if we don't recognise the extension
					if ($client = (string) $element->attributes()->client)
					{
						$this->client_id = JApplicationHelper::getClientInfo($this->client, 1);
						$this->client_id = $this->client_id->id;
					}
					if ($group = (string) $element->attributes()->group)
					{
						$this->group = (string) $element->attributes()->group;
					}
					break;
			}
			$this->filename = (string) $element;
		}
	}
}

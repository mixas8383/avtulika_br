<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * DocumentXML class, provides an easy interface to parse and display XML output
 *
 * @package     	LongCMS.Platform
 * @subpackage  Document
 * @since       11.1
 */
class JDocumentXml extends JDocument
{
	/**
	 * Document name
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_name = 'LongCMS';

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		//set mime type
		$this->_mime = 'application/xml';

		//set document type
		$this->_type = 'xml';
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  The rendered data
	 *
	 * @since  11.1
	 */
	public function render($cache = false, $params = array())
	{
		parent::render();
		JResponse::setHeader('Content-disposition', 'inline; filename="' . $this->getName() . '.xml"', true);

		return $this->getBuffer();
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 *
	 * @since  11.1
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 *
	 * @return  JDocumentXml instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setName($name = 'LongCMS')
	{
		$this->_name = $name;

		return $this;
	}
}

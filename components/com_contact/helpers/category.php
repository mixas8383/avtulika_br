<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('core.application.component.helper');
jimport('core.application.categories');

/**
 * Contact Component Category Tree
 *
 * @static
 * @package	LongCMS.Site
 * @subpackage	com_contact
 * @since 1.6
 */
class ContactCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__contact_details';
		$options['extension'] = 'com_contact';
		$options['statefield'] = 'published';
		parent::__construct($options);
	}
}

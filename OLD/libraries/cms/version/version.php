<?php
/**
 * @package    LongCMS.Site
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Version information class for the LongCMS CMS.
 *
 * @package  LongCMS.Site
 * @since    1.0
 */
final class JVersion
{
	/** @var  string  Product name. */
	public $PRODUCT = 'LongCMS';

	/** @var  string  Release version. */
	public $RELEASE = '2.5';

	/** @var  string  Maintenance version. */
	public $DEV_LEVEL = '9';

	/** @var  string  Maintenance version. */
	public $LONGCMS_LEVEL = '1';

	/** @var  string  Development STATUS. */
	public $DEV_STATUS = 'Stable';

	/** @var  string  Build number. */
	public $BUILD = '';

	/** @var  string  Code name. */
	public $CODENAME = 'Longman';

	/** @var  string  Release date. */
	public $RELDATE = '8-Jenuary-2013';

	/** @var  string  Release time. */
	public $RELTIME = '14:00';

	/** @var  string  Release timezone. */
	public $RELTZ = 'GMT';

	/** @var  string  Copyright Notice. */
	public $COPYRIGHT = 'Copyright (C) 2005 - 2013 LongCMS Team. All rights reserved.';

	/** @var  string  Link text. */
	public $URL = '<a href="http://www.long.ge">LongCMS</a> is Free Software released under the GNU General Public License.';

	/** @var  string  Author. */
	public $AUTHOR = 'Avtandil Kikabidze - http://long.ge';


	/**
	 * Compares two a "PHP standardized" version number against the current LongCMS version.
	 *
	 * @param   string  $minimum  The minimum version of the LongCMS which is compatible.
	 *
	 * @return  bool    True if the version is compatible.
	 *
	 * @see     http://www.php.net/version_compare
	 * @since   1.0
	 */
	public function isCompatible($minimum)
	{
		return version_compare(JVERSION, $minimum, 'ge');
	}

	/**
	 * Method to get the help file version.
	 *
	 * @return  string  Version suffix for help files.
	 *
	 * @since   1.0
	 */
	public function getHelpVersion()
	{
		return '.' . str_replace('.', '', $this->RELEASE);
	}

	public function getAuthor()
	{
		return $this->AUTHOR;
	}

	/**
	 * Gets a "PHP standardized" version string for the current LongCMS.
	 *
	 * @return  string  Version string.
	 *
	 * @since   1.5
	 */
	public function getShortVersion()
	{
		return $this->RELEASE . '.' . $this->DEV_LEVEL . '.' . $this->LONGCMS_LEVEL;
	}

	/**
	 * Gets a version string for the current LongCMS with all release information.
	 *
	 * @return  string  Complete version string.
	 *
	 * @since   1.5
	 */
	public function getLongVersion()
	{
		return $this->PRODUCT . ' ' . $this->RELEASE . '.' . $this->DEV_LEVEL . '.' . $this->LONGCMS_LEVEL . ' '
				. $this->DEV_STATUS . ' [ ' . $this->CODENAME . ' ] ' . $this->RELDATE . ' '
				. $this->RELTIME . ' ' . $this->RELTZ;
	}

	/**
	 * Returns the user agent.
	 *
	 * @param   string  $component    Name of the component.
	 * @param   bool    $mask         Mask as Mozilla/5.0 or not.
	 * @param   bool    $add_version  Add version afterwards to component.
	 *
	 * @return  string  User Agent.
	 *
	 * @since   1.0
	 */
	public function getUserAgent($component = null, $mask = false, $add_version = true)
	{
		if ($component === null)
		{
			$component = 'Framework';
		}

		if ($add_version)
		{
			$component .= '/' . $this->RELEASE;
		}

		// If masked pretend to look like Mozilla 5.0 but still identify ourselves.
		if ($mask)
		{
			return 'Mozilla/5.0 ' . $this->PRODUCT . '/' . $this->RELEASE . '.' . $this->DEV_LEVEL . '.' . $this->LONGCMS_LEVEL . ($component ? ' ' . $component : '');
		}
		else
		{
			return $this->PRODUCT . '/' . $this->RELEASE . '.' . $this->DEV_LEVEL . '.' . $this->LONGCMS_LEVEL . ($component ? ' ' . $component : '');
		}
	}
}

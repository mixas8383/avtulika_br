<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Error
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// TODO: Wack this into a language file when this gets merged
if (JDEBUG)
{
	JError::raiseWarning(100, "JLog has moved to jimport('core.log.log'), please update your code.");
	JError::raiseWarning(100, "JLog has changed its behaviour; please update your code.");
}
require_once JPATH_LIBRARIES . '/core/log/log.php';

<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die('Restricted access');

JLog::add('JString has moved to jimport(\'core.string.string\'), please update your code.', JLog::WARNING, 'deprecated');

require_once JPATH_PLATFORM . '/core/string/string.php';

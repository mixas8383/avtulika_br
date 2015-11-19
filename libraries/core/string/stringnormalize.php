<?php
/**
 * @package     	LongCMS.Platform
 * @subpackage  String
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JLog::add('JStringNormalize has moved to jimport(\'core.string.normalise\'), please update your code.', JLog::WARNING, 'deprecated');

require_once JPATH_PLATFORM . '/core/string/normalise.php';

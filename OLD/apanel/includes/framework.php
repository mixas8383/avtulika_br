<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/*
 * LongCMS system checks.
 */

@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

//
// LongCMS system startup.
//

// System includes.
require_once JPATH_LIBRARIES.'/import.php';

// Force library to be in JError legacy mode
JError::$legacy = true;
JError::setErrorHandling(E_NOTICE, 'message');
JError::setErrorHandling(E_WARNING, 'message');
JError::setErrorHandling(E_ERROR, 'message', array('JError', 'customErrorPage'));

// Botstrap the CMS libraries.
require_once JPATH_LIBRARIES.'/cms.php';

// Pre-Load configuration.
ob_start();
require_once JPATH_CONFIGURATION.'/configuration.php';
ob_end_clean();

// System configuration.
$config = new JConfig();

// error_log path
ini_set('log_errors', 1);
ini_set('error_log', JPATH_ERRORLOG);

// Set the error_reporting
switch ($config->error_reporting)
{
	case '-1':
	case 'development':
		error_reporting(-1);
		break;

	case 'default':
	case 'production':
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		break;
}


// system debug
if ($config->debug)
{
	if (!empty($config->debug_ip_list))
	{
		$d_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'undefined';
		$ex = explode(',', $config->debug_ip_list);
		if (in_array($d_ip, $ex))
		{
			ini_set('display_errors', 1);
			define('JDEBUG', $config->debug);
		}
		else
		{
			ini_set('display_errors', 0);
			define('JDEBUG', 0);
		}
	}
	else
	{
		ini_set('display_errors', 1);
		define('JDEBUG', 1);
	}
}
else
{
	ini_set('display_errors', 0);
	define('JDEBUG', 0);
}

unset($config);

/*
 * LongCMS framework loading.
 */

// System profiler.
if (JDEBUG) {
	jimport('core.error.profiler');
	$_PROFILER = JProfiler::getInstance('Application');
}

// LongCMS library imports.
jimport('core.application.menu');
jimport('core.environment.uri');
jimport('core.html.parameter');
jimport('core.utilities.utility');
jimport('core.event.dispatcher');
jimport('core.utilities.arrayhelper');

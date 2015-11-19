<?php
/**
 * @package	LongCMS.Site
 * @copyright	Copyright (C) 2009 - 2012 LongCMS Team (http://unix.ge/longcms). All rights reserved.
 * @license	GNU General Public License version 2 or later;
 */

// Set flag that this is a parent file.
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

/*$ips = array('95.104.105.5', '94.43.206.163', '93.188.11.187');
if (!in_array($_SERVER['REMOTE_ADDR'], $ips)) {
	header('location: index2.html');
	die;
}*/


require_once dirname(__FILE__) . '/defines.php';


if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(__FILE__));
	require_once JPATH_BASE.'/includes/defines.php';
}

require_once JPATH_BASE.'/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

// Initialise the application.
$app->initialise();

// Mark afterIntialise in the profiler.
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;

// Route the application.
$app->route();

// Mark afterRoute in the profiler.
JDEBUG ? $_PROFILER->mark('afterRoute') : null;

// Dispatch the application.
$app->dispatch();


// Mark afterDispatch in the profiler.
JDEBUG ? $_PROFILER->mark('afterDispatch') : null;

// Render the application.
$app->render();

// Mark afterRender in the profiler.
JDEBUG ? $_PROFILER->mark('afterRender') : null;

// Return the response.
echo $app;

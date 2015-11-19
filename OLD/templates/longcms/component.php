<?php
/**
 * @package                	LongCMS.Site
 * @subpackage		Templates.longcms
 * @copyright        		Copyright (C) 2009 - 2012 LongCMS Team. All rights reserved.
 * @license                	GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// get params
$app				= JFactory::getApplication();
$doc				= JFactory::getDocument();
$lang				= JFactory::getLanguage();

$logo				= $this->params->get('print_logo');
$logo_title			= htmlspecialchars($app->getCfg('sitename'), ENT_QUOTES);

$rand = mt_rand();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/src/print.css<?php echo JDEBUG?'?'.$rand:''; ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/src/debug.css<?php echo JDEBUG?'?'.$rand:''; ?>" type="text/css" />
</head>
<body class="contentpane">
	<div id="all">
        <div class="logo">
            <img src="<?php echo $logo ?>" alt="<?php echo $logo_title ?>"/>
        </div>
		<div id="main">
			<jdoc:include type="message" />
			<jdoc:include type="component" />
		</div>
	</div>
	<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/script.js<?php echo JDEBUG?'?'.$rand:''; ?>" type="text/javascript"></script>
</body>
</html>

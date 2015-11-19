<?php
/**
 * @package	LongCMS.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.


$href = JFilterOutput::clean($params->get('href'));
$width = JFilterOutput::clean($params->get('width'));
$height = JFilterOutput::clean($params->get('height'));
$show_faces = JFilterOutput::clean($params->get('show_faces'));
$stream = JFilterOutput::clean($params->get('stream'));
$border_color = JFilterOutput::clean($params->get('border_color'));
$header = JFilterOutput::clean($params->get('header'));
$colorscheme = JFilterOutput::clean($params->get('colorscheme'));
?>

<div class="fb_fanbox<?php echo $moduleclass_sfx ?>">
	<fb:like-box href="<?php echo $href ?>" width="<?php echo $width ?>" height="<?php echo $height ?>" colorscheme="<?php echo $colorscheme ?>" show_faces="<?php echo $show_faces ?>" stream="<?php echo $stream ?>" border_color="<?php echo $border_color ?>" header="<?php echo $header ?>"></fb:like-box>
</div>

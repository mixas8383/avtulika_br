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

$doc = JFactory::getDocument();
$href = JFilterOutput::clean($params->get('href'));
$width = JFilterOutput::clean($params->get('width'));
$height = JFilterOutput::clean($params->get('height'));
$show_faces = JFilterOutput::clean($params->get('show_faces'));
$stream = JFilterOutput::clean($params->get('stream'));
$border_color = JFilterOutput::clean($params->get('border_color'));
$header = JFilterOutput::clean($params->get('header'));
$colorscheme = JFilterOutput::clean($params->get('colorscheme'));
$preload = (int)$params->get('preload', 5);
$preload *= 1000;
$header_title = JFilterOutput::clean($params->get('header_title'));
$header_text = JFilterOutput::clean($params->get('header_text'));


?>
<div id="mod_fb_fanbox_popup">
    <?php
	if (!empty($text)) {
		?>
		<div class="fbpopup_title">
			<?php echo $text ?>
		</div>
		<?php
	}
	if (!empty($header_title)) {
		?>
		<div class="fb_fanbox_popup_title">
			<?php echo $header_title ?>
		</div>
		<?php
	}
	if (!empty($header_text)) {
		?>
		<div class="fb_fanbox_popup_text">
			<?php echo $header_text ?>
		</div>
		<?php
	}
	?>
	<div class="mod_popup_body" style="margin: 4px;">
		<fb:like-box href="<?php echo $href ?>" width="<?php echo $width ?>" height="<?php echo $height ?>" colorscheme="<?php echo $colorscheme ?>" show_faces="<?php echo $show_faces ?>" stream="<?php echo $stream ?>" border_color="<?php echo $border_color ?>" header="<?php echo $header ?>"></fb:like-box>
	</div>
</div>
<a style="display:none" id="inline" href="#mod_fb_fanbox_popup"></a>
<div style="display:none">
	<div style="display: none;" class="fancybox-inline-tmp"></div>
</div>
<?php
$script = '
$(function() {
		$("a#inline").fancybox({
			"hideOnContentClick": false,
			"centerOnScroll": true,
			"autoDimensions": true,
			"autoSize": false,
			"autoCenter": true,
			"width": "330px",
			"height":"220px",
			"transitionIn": "fade",
			"transitionOut": "fade",
			"padding": 0
		});
		setTimeout(function(){
			$("a#inline").trigger("click");
		},'.$preload.')
});
';
$doc->addScriptDeclaration($script);


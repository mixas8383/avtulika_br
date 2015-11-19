<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('core.image.imageold');


//$multipic = JFactory::getMultipic();
//$image = $multipic->getImage('image11', $img);

$width = 582;
$height = 256;
$quality = 80;
$imgedit = 4;


$img = 'images/deals/dsc00246.jpg';
$mode = 1;

if ($mode == 1 || $mode == 3) {
	// new
	$new_image = JPATH_ROOT.'/images/pics/new.jpg';
	$new = new JImage($img);

	$width = 100;
	$height = 100;

	$new->exactly($width, $height, 'center');
	$new->toFile($new_image);
}

if ($mode == 2 || $mode == 3) {
	// old
	$old_image = JPATH_ROOT.'/images/pics/old.jpg';
	$old = new JImageOld();
	$old->setImage($img);
	$old->crop($width, $height, false);
	$old->save($old_image);
}


?>
<img src="<?php echo 'images/pics/new.jpg' ?>" />
<!-- <img src="<?php echo 'images/pics/old.jpg' ?>" /> -->
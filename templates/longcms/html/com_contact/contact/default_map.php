<?php

/**
 * @package	LongCMS.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<div class="contact-map">
	<iframe width="<?php echo $this->contact->map_width ?>" height="<?php echo $this->contact->map_height ?>" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<?php echo $this->contact->map_url ?>"></iframe>
</div>


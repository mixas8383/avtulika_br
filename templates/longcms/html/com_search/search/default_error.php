<?php
/**
 * @package    LongCMS.Site
 * @subpackage    com_search
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if($this->error): ?>
<div class="error">
    <?php echo $this->escape($this->error); ?>
</div>
<?php endif; ?>

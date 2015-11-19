<?php
/**
 * @package    LongCMS.Site
 * @subpackage    mod_search
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$post = JRequest::get('post');
$searchword = !empty($post['searchword']) ? $post['searchword'] : '';

?>
<form action="<?php echo JRoute::_('index.php');?>" method="post">
    <div class="search<?php echo $moduleclass_sfx ?>">
        <?php
            $output = '<input name="searchword" id="mod-search-searchword" maxlength="'.$maxlength.'"  class="inputbox'.$moduleclass_sfx.'" type="text" size="'.$width.'" value="'.$searchword.'" placeholder="'.$text.'" />';

            if ($button) :
                if ($imagebutton) :
                    $button = '<input type="image" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" src="'.$img.'" onclick="this.form.searchword.focus();"/>';
                else :
                    $button = '<input type="submit" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" onclick="this.form.searchword.focus();"/>';
                endif;
            endif;

            switch ($button_pos) :
                case 'top' :
                    $button = $button.'<br />';
                    $output = $button.$output;
                    break;

                case 'bottom' :
                    $button = '<br />'.$button;
                    $output = $output.$button;
                    break;

                case 'right' :
                    $output = $output.$button;
                    break;

                case 'left' :
                default :
                    $output = $button.$output;
                    break;
            endswitch;

            echo $output;


        ?>
    </div>
</form>

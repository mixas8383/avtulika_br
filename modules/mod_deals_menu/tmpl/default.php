<?php
/**
 * @package        LongCMS.Site
 * @subpackage    mod_example
 * @copyright        Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$jinput = $app->input;
$cat_id = $jinput->get->getUint('cat');
$Itemid = JMenu::getItemid('com_deals', 'deals');

?>
<div class="mod_deals_menu<?php echo $moduleclass_sfx ?>">
    <ul class="deals_menu_ul">
    <?php
    $uri = JFactory::getUri();
    $uri->delVar('cat');



    foreach($categories as $category) {


        ?>
        <li class="dcat_parent">
            <a href="javascript:void(0);"><?php echo $category->title ?></a>
            <?php
            if (!empty($category->children)) {
                $class = $category->id == 37 ? ' multidim' : '';
                ?>
                <ul class="deals_menu_ul_sub<?php echo $class ?>">
                    <?php
                    if (!empty($category->children)) {
                        $subs = count($category->children);


                        foreach ($category->children as $child) {
                            ?>
                            <li>

                                <?php
                                if (!empty($child->children)) {
                                    ?>
                                    <b class="deals_menu_ul_sub_title"><?php echo $child->title ?></b>
                                    <ul class="deals_menu_ul_sub_last">
                                        <?php
                                        foreach ($child->children as $child2) {
                                            $class = $cat_id == $child2->id ? 'dcat_child dcat_selected' : 'dcat_child';
                                            //$uri->setVar('cat', $child2->id);
                                            //$link = $uri->toString();
                                            $link = JRoute::_('index.php?cat='.$child2->id.'&Itemid='.$Itemid);

                                            ?>
                                            <li class="<?php echo $class ?>">
                                                <a href="<?php echo $link ?>"><?php echo $child2->title ?></a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                } else {
                                    $class = $cat_id == $child->id ? 'dcat_child dcat_selected' : 'dcat_child';
                                    //$uri->setVar('cat', $child->id);
                                    //$link = $uri->toString();
                                    $link = JRoute::_('index.php?cat='.$child->id.'&Itemid='.$Itemid);

                                    ?>
                                    <a href="<?php echo $link ?>"><?php echo $child->title ?></a>
                                    <?php
                                }
                                ?>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
                <?php
            }
            ?>

        </li>
        <?php

    }
    ?>
    </ul>
</div>
<?php

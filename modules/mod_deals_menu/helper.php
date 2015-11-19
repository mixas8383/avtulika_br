<?php
/**
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package    LongCMS.Site
 * @subpackage    mod_example
 * @since        1.5
 */
class modDealsMenuHelper
{

    public static function getCategories()
    {
        $categories = PDeals::getCategories(true);

        //$categories = self::sortCategories($categories);
        $categories = PDeals::sortCategories($categories, false, true);


        return $categories;
    }

    public static function sortCategories($categories)
    {
        $result = array();
        foreach ($categories as $category) {
            $id        = $category->id;
            $parent_id = $category->parent;

            if (0 == $parent_id) {
                if (!isset($result[$id])) {
                    $result[$id]['item']     = $category;
                    $result[$id]['children'] = array();
                }
            } else {
                $res2['item']                     = $category;
                $res2['children']                 = array();
                $result[$parent_id]['children'][] = $res2;
            }
        }

        return $result;
    }

}

<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package	LongCMS.Site
 * @subpackage	mod_menu_bottom
 * @since		1.5
 */
class modMenuBottomHelper
{


	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 * @since	1.5
	 */
	static function getList($params)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$user = JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		asort($levels);
		$key = 'menu_items'.$params.implode(',', $levels).'.'.$active->id;
		$cache = JFactory::getCache('mod_menu_bottom', '');
		if (!($items = $cache->get($key)))
		{
			// Initialise variables.
			$list		= array();
			$db		= JFactory::getDbo();

			$path		= $active->tree;

			$menu_id 	= $params->get('menu');


			$items 	= $menu->getMenu();



			$menu_items = array();
			if ($items) {

				foreach($items as $i => $item)
				{

					if (in_array($menu_id, $item->tree)) {
						if ($menu_id == $item->id) {
							continue;
						}
						$item->deeper = false;
						$item->shallower = false;
						$item->level_diff = 0;

						$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

						$lastitem			= $i;
						$item->active		= false;
						$item->flink = $item->link;

						// Reverted back for CMS version 2.5.6
						switch ($item->type)
						{
							case 'separator':
								// No further action needed.
								continue;

							case 'url':
								if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
									// If this is an internal LongCMS link, ensure the Itemid is set.
									$item->flink = $item->link.'&Itemid='.$item->id;
								}
								break;

							case 'alias':
								// If this is an alias use the item id stored in the parameters to make the link.
								$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
								break;

							default:
								$router = JSite::getRouter();
								if ($router->getMode() == JROUTER_MODE_SEF) {
									$item->flink = 'index.php?Itemid='.$item->id;
								}
								else {
									$item->flink .= '&Itemid='.$item->id;
								}
								break;
						}

						if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
							$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
						}
						else {
							$item->flink = JRoute::_($item->flink);
						}

						$item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
						$item->anchor_css   = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
						$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
						$item->menu_image   = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
						$menu_items[] = $item;


					}



				}

			}

			$cache->store($menu_items, $key);
		}
		return $menu_items;
	}










}

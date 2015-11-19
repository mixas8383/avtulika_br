<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package	LongCMS.Site
 * @subpackage	com_wrapper
 */
class DealsViewDeals extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$items = $this->get('Items');
		$categories = $this->get('Categories');

		$menus = $app->getMenu();
		$menu	= $menus->getActive();

		$params = $app->getParams();

		$title = $params->get('page_title', '');

		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if (empty($title)) {
			$title = $this->item->title;
		}
		$document->setTitle($title);

		$jinput = $app->input;
		$category = $jinput->get->getUint('cat');


		$obj = new stdClass;
		$obj->id = 0;
		$obj->title = 'ყველა კატეგორია';

		$categories = array(-1=>$obj) + $categories;
		$category_filter = JHtml::_('select.genericlist', $categories, 'cat', null, 'id', 'title', $category, 'cat_filter');
		$this->assign('category_filter', $category_filter);
		$this->assign('category', $category);
		$this->assign('items', $items);
		$this->assign('params', $params);
		parent::display($tpl);
	}
}

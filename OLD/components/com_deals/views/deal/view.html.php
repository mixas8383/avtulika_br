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
class DealsViewDeal extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$item = $this->get('Item');
		$related = $this->get('Related');

		$menus = $app->getMenu();
		$menu	= $menus->getActive();

		$params = $app->getParams();

		$title = $params->get('page_title', '');

		$session = JFactory::getSession();
		$session->set('deals', array());

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


		if (!empty($item->metadesc)) {
			$document->setDescription($item->metadesc);
		}

		if (!empty($item->metakey)) {
			$document->setMetadata('keywords', $item->metakey);
		}


		$this->assign('item', $item);
		$this->assign('related', $related);
		$this->assign('params', $params);
		$this->assign('document', $document);
		parent::display($tpl);
	}
}

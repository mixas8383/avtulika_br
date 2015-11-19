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
		$params = $app->getParams();
		$this->setLayout('xml');

		$this->assign('items', $items);
		$this->assign('params', $params);
		parent::display($tpl);
	}
}

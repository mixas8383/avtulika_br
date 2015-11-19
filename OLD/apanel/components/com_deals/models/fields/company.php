<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Clicks Field class for the LongCMS Framework.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class JFormFieldCompany extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'company';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */

	protected function getOptions()
	{
		$data = DealsHelper::getCompanyOptions();
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('COM_DEALS_FIELD_COMPANY'), 'value', 'text');
		foreach($data as $v) {
			$options[] = JHtml::_('select.option', $v->value, $v->text, 'value', 'text');
		}
		return $options;
	}



}

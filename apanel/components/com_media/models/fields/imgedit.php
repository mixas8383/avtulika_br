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
class JFormFieldImgEdit extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'imgedit';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		$data = MediaHelper::getEditTypes();
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('COM_MEDIA_SIZE_FIELD_IMGEDIT'), 'value', 'text');
		foreach($data as $k=>$v) {
			$options[] = JHtml::_('select.option', $k, $v, 'value', 'text');
		}
		return $options;
	}
}

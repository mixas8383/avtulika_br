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
class JFormFieldCodename extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'codename';


	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';


		if ($this->value == 'original') {
			$this->element['readonly'] = 'true';
			$attr .= ' disabled="disabled"';
		}


		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}


		return implode($html);
	}




	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		$id = 0;
		if (!empty($this->value))
		{
			// Get list of plugins
			$db     = JFactory::getDbo();
			$query  = $db->getQuery(true);
			$query->select('id');
			$query->from('#__media_sizes');
			$query->where('codename = ' . $db->q($this->value));
			$db->setQuery($query);
			$row = $db->loadObject();
			$id = !empty($row->id) ? $row->id : 0;
		}
		$data = MediaHelper::getFreeCodeNames($id);
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('COM_MEDIA_SIZE_FIELD_CODENAME'), 'value', 'text');
		foreach($data as $v) {
			$options[] = JHtml::_('select.option', $v, $v, 'value', 'text');
		}
		return $options;
	}
}

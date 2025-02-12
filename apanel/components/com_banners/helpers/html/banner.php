<?php
/**
 * @package     	LongCMS.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Banner HTML class.
 *
 * @package     	LongCMS.Administrator
 * @subpackage  com_banners
 * @since       2.5
 */
abstract class JHtmlBanner
{
	/**
	 * Display a batch widget for the client selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public static function clients()
	{
		// Create the batch selector to change the client on a selection list.
		$lines = array(
			'<label id="batch-client-lbl" for="batch-client" class="hasTip" title="'.JText::_('COM_BANNERS_BATCH_CLIENT_LABEL').'::'.JText::_('COM_BANNERS_BATCH_CLIENT_LABEL_DESC').'">',
			JText::_('COM_BANNERS_BATCH_CLIENT_LABEL'),
			'</label>',
			'<select name="batch[client_id]" class="inputbox" id="batch-client-id">',
			'<option value="">'.JText::_('COM_BANNERS_BATCH_CLIENT_NOCHANGE').'</option>',
			'<option value="0">'.JText::_('COM_BANNERS_NO_CLIENT').'</option>',
			JHtml::_('select.options', self::clientlist(), 'value', 'text'),
			'</select>'
		);

		return implode("\n", $lines);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	public static function clientlist()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('id As value, name As text');
		$query->from('#__banner_clients AS a');
		$query->order('a.name');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		return $options;
	}

	/**
	 * Returns a pinned state on a grid
	 *
	 * @param   integer       $value			The state value.
	 * @param   integer       $i				The row index
	 * @param   boolean       $enabled		An optional setting for access control on the action.
	 * @param   string        $checkbox		An optional prefix for checkboxes.
	 *
	 * @return  string        The Html code
	 *
	 * @see JHtmlJGrid::state
	 *
	 * @since   2.5.5
	 */
	public static function pinned($value, $i, $enabled = true, $checkbox = 'cb')
	{
		$states	= array(
			1	=> array(
				'sticky_unpublish',
				'COM_BANNERS_BANNERS_PINNED',
				'COM_BANNERS_BANNERS_HTML_PIN_BANNER',
				'COM_BANNERS_BANNERS_PINNED',
				false,
				'publish',
				'publish'
			),
			0	=> array(
				'sticky_publish',
				'COM_BANNERS_BANNERS_UNPINNED',
				'COM_BANNERS_BANNERS_HTML_UNPIN_BANNER',
				'COM_BANNERS_BANNERS_UNPINNED',
				false,
				'unpublish',
				'unpublish'
			),
		);

		return JHtml::_('jgrid.state', $states, $value, $i, 'banners.', $enabled, true, $checkbox);
	}

}

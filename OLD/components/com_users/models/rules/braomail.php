<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('core.form.formrule');
/**
 * Form Rule class for the Joomla Framework.
 *
 * @package        Joomla.Framework
 * @since          1.6
 */
class JFormRuleMobile extends JFormRule
{

	public function test($element, $value, $group = null, $input = null, $form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string)$element['required'] == 'true' || (string)$element['required'] == 'required');
		if (!$required && empty($value)) {

			return true;
		}


		return preg_match("/^\+{0,1}[0-9]{6,14}$/", $value);
	}
}

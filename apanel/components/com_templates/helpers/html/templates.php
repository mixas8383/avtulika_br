<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package	LongCMS.Administrator
 * @subpackage	com_templates
 */
class JHtmlTemplates
{
	/**
	 * Display the thumb for the template.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function thumb($template, $clientId = 0)
	{
		$client		= JApplicationHelper::getClientInfo($clientId);
		$basePath	= $client->path.'/templates/'.$template;
		$baseUrl	= ($clientId == 0) ? JUri::root(true) : JUri::root(true).'/'.JFOLDER_ADMINISTRATOR;
		$thumb		= $basePath.'/template_thumbnail.png';
		$preview	= $basePath.'/template_preview.png';
		$html		= '';

		if (file_exists($thumb))
		{
			$clientPath = ($clientId == 0) ? '' : JFOLDER_ADMINISTRATOR.'/';
			$thumb	= $clientPath.'templates/'.$template.'/template_thumbnail.png';
			$html	= JHtml::_('image', $thumb, JText::_('COM_TEMPLATES_PREVIEW'));
			if (file_exists($preview))
			{
				$preview	= $baseUrl.'/templates/'.$template.'/template_preview.png';
				$html		= '<a href="'.$preview.'" class="modal" title="'.JText::_('COM_TEMPLATES_CLICK_TO_ENLARGE').'">'.$html.'</a>';
			}
		}

		return $html;
	}
}

<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('core.application.component.modelform');
/**
 * This models supports retrieving lists of contact categories.
 *
 * @package	LongCMS.Site
 * @subpackage	com_contact
 * @since		1.6
 */
class DealsModelRequestDeal extends JModelForm
{
	public $_context = 'com_deals.requestdeal';
	protected $_extension = 'com_deals';

	/**
	 * Method to get the contact form.
	 *
	 * The base form is loaded from XML and then an event is fired
	 *
	 *
	 * @param	array	$data		An optional array of data for the form to interrogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_deals.requestdeal', 'requestdeal', array('control' => 'jform', 'load_data' => true));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = (array)JFactory::getApplication()->getUserState('com_deals.requestdeal.data', array());
		return $data;
	}

	public function sendMail($data = array())
	{
		$mailer = JFactory::getMailer();
 		$app = JFactory::getApplication();
		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$params = JComponentHelper::getParams('com_deals');
		$to_mail = $params->get('requestdeal_mail');


		$request_data = '';
		foreach($data as $key=>$value) {
			$lng_title = JText::_('COM_DEALS_REQUESTDEAL_FORM_'.strtoupper($key).'_LABEL');
			$request_data .= $lng_title.": ".$value."<br />";
		}



		$subject = JText::sprintf('COM_DEALS_REQUESTDEAL_MAIL_SUBJECT');
		$body = JText::sprintf('COM_DEALS_REQUESTDEAL_MAIL_BODY', $request_data);


		$send = $mailer->sendMail($mailfrom, $fromname, $to_mail, $subject, $body, true);

		return $send;
	}


}

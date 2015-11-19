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
class DealsModelInstallment extends JModelForm
{
	public $_context = 'com_deals.installment';
	protected $_extension = 'com_deals';


	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_deals.installment', 'installment', array('control' => 'jform', 'load_data' => true));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = (array)JFactory::getApplication()->getUserState('com_deals.installment.data', array());
		return $data;
	}



	public function sendMail($data = array(), $deals = array())
	{
		$mailer = JFactory::getMailer();
 		$app = JFactory::getApplication();
		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$params = JComponentHelper::getParams('com_deals');
		$to_mail = $params->get('installment_mail');

		$deal = $deals[0];
		if (empty($deal->id)) {
			return false;
		}

		$deal_title = $deal->getTitle();
		$deal_id = $deal->id;
		$deal_url = JURI::root(false, null, true).$deal->getUrl();


		$request_data = '';
		foreach($data as $key=>$value) {
			$lng_title = JText::_('COM_DEALS_INSTALLMENT_FORM_'.strtoupper($key).'_LABEL');
			$request_data .= $lng_title.": ".$value."<br />";
		}



		$subject = JText::sprintf('COM_DEALS_INSTALLMENT_MAIL_SUBJECT');
		$body = JText::sprintf('COM_DEALS_INSTALLMENT_MAIL_BODY', $deal_url, $deal_title, $request_data);


		$send = $mailer->sendMail($mailfrom, $fromname, $to_mail, $subject, $body, true);

		return $send;
	}


}

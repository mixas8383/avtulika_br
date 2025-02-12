<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('core.application.component.modeladmin');

/**
 * Users mail model.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_users
 * @since	1.6
 */
class UsersModelMail extends JModelAdmin
{
	/**
	 * Method to get the row form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_users.mail', 'mail', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_users.display.mail.data', array());

		return $data;
	}

	/**
	 * Override preprocessForm to load the user plugin group instead of content.
	 *
	 * @param	object	A form object.
	 * @param	mixed	The data expected for the form.
	 * @throws	Exception if there is an error in the form event.
	 * @since	1.6
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'user')
	{
		parent::preprocessForm($form, $data, $group);
	}

	public function send()
	{
		// Initialise variables.
		$data	= JRequest::getVar('jform', array(), 'post', 'array');
		$app	= JFactory::getApplication();
		$user	= JFactory::getUser();
		$acl	= JFactory::getACL();
		$db		= $this->getDbo();


		$mode		= array_key_exists('mode', $data) ? intval($data['mode']) : 0;
		$subject	= array_key_exists('subject', $data) ? $data['subject'] : '';
		$grp		= array_key_exists('group', $data) ? intval($data['group']) : 0;
		$recurse	= array_key_exists('recurse', $data) ? intval($data['recurse']) : 0;
		$bcc		= array_key_exists('bcc', $data) ? intval($data['bcc']) : 0;
		$disabled	= array_key_exists('disabled', $data) ? intval($data['disabled']) : 0;
		$message_body = array_key_exists('message', $data) ? $data['message'] : '';

		// automatically removes html formatting
		if (!$mode) {
			$message_body = JFilterInput::getInstance()->clean($message_body, 'string');
		}

		// Check for a message body and subject
		if (!$message_body || !$subject) {
			$app->setUserState('com_users.display.mail.data', $data);
			$this->setError(JText::_('COM_USERS_MAIL_PLEASE_FILL_IN_THE_FORM_CORRECTLY'));
			return false;
		}

		// get users in the group out of the acl
		$to = $acl->getUsersByGroup($grp, $recurse);

		// Get all users email and group except for senders
		$query	= $db->getQuery(true);
		$query->select('email');
		$query->from('#__users');
		$query->where('id != '.(int) $user->get('id'));
		if ($grp !== 0) {
			if (empty($to)) {
				$query->where('0');
			} else {
				$query->where('id IN (' . implode(',', $to) . ')');
			}
		}

		if ($disabled == 0){
			$query->where("block = 0");
		}

		$db->setQuery($query);
		$rows = $db->loadColumn();

		// Check to see if there are any users in this group before we continue
		if (!count($rows)) {
			$app->setUserState('com_users.display.mail.data', $data);
			if (in_array($user->id, $to))
			{
				$this->setError(JText::_('COM_USERS_MAIL_ONLY_YOU_COULD_BE_FOUND_IN_THIS_GROUP'));
			}
			else
			{
				$this->setError(JText::_('COM_USERS_MAIL_NO_USERS_COULD_BE_FOUND_IN_THIS_GROUP'));
			}
			return false;
		}

		// Get the Mailer
		$mailer = JFactory::getMailer();
		$params = JComponentHelper::getParams('com_users');

		// Build email message format.
		$mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
		$mailer->setSubject($params->get('mailSubjectPrefix') . stripslashes($subject));
		$mailer->setBody($message_body . $params->get('mailBodySuffix'));
		$mailer->IsHTML($mode);

		// Add recipients
		if ($bcc) {
			$mailer->addBCC($rows);
			$mailer->addRecipient($app->getCfg('mailfrom'));
		} else {
			$mailer->addRecipient($rows);
		}

		// Send the Mail
		$rs	= $mailer->Send();

		// Check for an error
		if ($rs instanceof Exception) {
			$app->setUserState('com_users.display.mail.data', $data);
			$this->setError($rs->getError());
			return false;
		} elseif (empty($rs)) {
			$app->setUserState('com_users.display.mail.data', $data);
			$this->setError(JText::_('COM_USERS_MAIL_THE_MAIL_COULD_NOT_BE_SENT'));
			return false;
		} else {
			// Fill the data (specially for the 'mode', 'group' and 'bcc': they could not exist in the array
			// when the box is not checked and in this case, the default value would be used instead of the '0'
			// one)
			$data['mode']=$mode;
			$data['subject']=$subject;
			$data['group']=$grp;
			$data['recurse']=$recurse;
			$data['bcc']=$bcc;
			$data['message']=$message_body;
			$app->setUserState('com_users.display.mail.data', array());
			$app->enqueueMessage(JText::plural('COM_USERS_MAIL_EMAIL_SENT_TO_N_USERS', count($rows)), 'message');
			return true;
		}
	}
}

<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Rest model class for Users.
 *
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersModelFacebook extends JModelLegacy
{
	public $data;

	public function getUser($data)
	{
		$db = $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__users');
		$query->where('email = '.$db->quote($data['email']));
  		$db->setQuery($query, 0, 1);
  		$user = $db->loadObject();
		return $user;
	}

	public function updateUser($user, $data)
	{
		$db = $this->getDbo();
		$sql = ' UPDATE `#__users` '
				.' SET `fb_id`='.$db->quote($data['id']).' '
				.' WHERE `id`='.$user->id.' '
				.' LIMIT 1'
				;
		$db->setQuery($sql);
  		$status = $db->query();
		return $status;
	}

	public function updateSession($user)
	{
		$db = JFactory::getDBO();
		$session = JFactory::getSession();

		// Check to see the the session already exists.
		$app = JFactory::getApplication();
		$app->checkSession();

		// Update the user related fields for the LongCMS sessions table.
		$db->setQuery(
			'UPDATE '.$db->quoteName('#__session') .
			' SET '.$db->quoteName('guest').' = '.$db->quote($user->get('guest')).',' .
			'	'.$db->quoteName('username').' = '.$db->quote($user->get('username')).',' .
			'	'.$db->quoteName('userid').' = '.(int) $user->get('id') .
			' WHERE '.$db->quoteName('session_id').' = '.$db->quote($session->getId())
		);
		$db->query();
		return true;
	}

	public function insertUser($fb_data)
	{
		$config = JFactory::getConfig();
		$db		= $this->getDbo();
		$params = JComponentHelper::getParams('com_users');


		// Initialise the table with JUser.
		$user = new JUser;
		$data = (array)$this->getData();

		$data['fb_id']		= $fb_data['id'];
		$data['email']		= $fb_data['email'];
		$data['username'] = $fb_data['id'];
		$data['name'] = $fb_data['first_name'];
		$data['surname'] = $fb_data['last_name'];


		// Prepare the data for the user object.
		$data['password']	= '';


		// Bind the data.
		if (!$user->bind($data)) {
			//die($user->getError());
			$this->setError(JText::sprintf('COM_USERS_FBREGISTRATION_BIND_FAILED', $user->getError()));
			return false;
		}


		// Store the data.
		if (!$user->save()) {
			//die($user->getError());
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			return false;
		}

		return $user;
	}


	public function getData()
	{
		if ($this->data === null) {

			$this->data	= new stdClass();
			$app	= JFactory::getApplication();
			$params	= JComponentHelper::getParams('com_users');

			// Get the groups the user should be added to after registration.
			$this->data->groups = array();

			// Get the default new user group, Registered if not specified.
			$system	= $params->get('new_usertype', 2);

			$this->data->groups[] = $system;

			// Unset the passwords.
			unset($this->data->password1);
			unset($this->data->password2);

		}

		return $this->data;
	}


}

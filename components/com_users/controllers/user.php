<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Registration controller class for Users.
 *
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerUser extends UsersController
{
	/**
	 * Method to log in a user.
	 *
	 * @since	1.6
	 */
	public function login()
	{
		JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));

		/*if (JDEBUG) {
			$db = JFactory::getDbo();
			$sql = ' UPDATE `#__users` '
					.' SET `mail_subscribed`=1 '
					;
			$db->setQuery($sql);
	  		$status = $db->query();
	  				  var_dump($status);
	  				  die;

		}*/

            
             

		$app = JFactory::getApplication();
		$params = $app->getParams();
 if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'Debug')
 {
     echo '<pre>'.__FILE__.' -->>| <b> Line </b>'.__LINE__.'</pre><pre>';
     print_r($params);
     die;
     
 }
		// Populate the data array:
		$data = array();
		$data['return'] = base64_decode(JRequest::getVar('return', '', 'POST', 'BASE64'));
		$data['username'] = JRequest::getVar('username', '', 'method', 'username');
		$data['password'] = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);

		// Set the return URL if empty.
		if (empty($data['return'])) {
			if ($params->get('return_on_login', 0)) {
				$data['return'] = 'index.php';
			} else {
				$data['return'] = 'index.php?option=com_users&view=profile';
			}
		}



		// Set the return URL in the user state to allow modification by plugins
		$app->setUserState('users.login.form.return', $data['return']);

		// Get the log in options.
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $data['return'];

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $data['username'];
		$credentials['password'] = $data['password'];

		// Perform the log in.
		if (true === $app->login($credentials, $options)) {
			// Success
			$app->setUserState('users.login.form.data', array());
			$app->redirect(JRoute::_($app->getUserState('users.login.form.return'), false));
		} else {
			// Login failed !
			$data['remember'] = (int)$options['remember'];
			$app->setUserState('users.login.form.data', $data);
			$app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
	}

	public function loginFB()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$params = $app->getParams();

		$allow_fb_authorization = $params->get('allow_fb_authorization');
		if (!$allow_fb_authorization) {
			JError::raiseWarning(0, JText::_('COM_USERS_FACEBOOK_LOGIN_DISABLED'));
			$app->redirect(JRoute::_('index.php', false));
			return false;
		}

		jimport('facebook-sdk.facebook');
		$config = JFactory::getConfig();
		$fb_sdk = new Facebook(array(
			'appId'  => $config->get('fb_og_appid'),
			'secret' => $config->get('fb_secred'),
		));


		$code = $jinput->get->getBool('code');
 		if (!$code) {
			$loginUrl = $fb_sdk->getLoginUrl(array(
					'redirect_uri' => JURI::root().'index.php?option=com_users&task=user.loginFB',
					'scope' => 'email',
				));
			if (!$loginUrl) {
				JError::raiseWarning(0, JText::_('COM_USERS_FACEBOOK_LOGIN_URLERROR'));
				$app->redirect(JRoute::_('index.php', false));
				return false;
			}
			$app->redirect($loginUrl);
			return false;
 		} else {
			// Get User ID
			$user = $fb_sdk->getUser();
			if (!$user) {
				JError::raiseWarning(0, JText::_('COM_USERS_FACEBOOK_LOGIN_ERROR'));
				$app->redirect(JRoute::_('index.php', false));
				return false;
			}




			try {
				$user_profile = $fb_sdk->api('/me?fields=email,first_name,last_name,username');
			} catch (FacebookApiException $e) {
				if (JDEBUG) {
					JError::raiseWarning(0, $e->getMessage());
					$app->redirect(JRoute::_('index.php', false));
					return false;
				}
				$user_profile = null;
			}

			if (empty($user_profile)) {
				JError::raiseWarning(0, JText::_('COM_USERS_FACEBOOK_LOGIN_ERROR2'));
				$app->redirect(JRoute::_('index.php', false));
				return false;
			}

			if (empty($user_profile['email'])) {
				JError::raiseWarning(0, JText::_('COM_USERS_FACEBOOK_LOGIN_ERROR3'));
				$app->redirect(JRoute::_('index.php', false));
				return false;
			}
			$model = $this->getModel('Facebook', 'UsersModel');
			$fb_user = $model->getUser($user_profile);



			if (empty($fb_user->id)) {
				$user_obj = $model->insertUser($user_profile);

				// Check for errors.
				if ($user_obj === false) {
					JError::raiseWarning(0, $model->getError());
					$app->redirect(JRoute::_('index.php', false));
					return false;
				}
			} else {
				if (empty($fb_user->fb_id)) {
					$update = $model->updateUser($fb_user, $user_profile);
					jimport('project.deals.deals');
					Transaction::log('Facebook authorization as user ID: '.$fb_user->id, true, false, 'facebook');
				}
				$user_obj = JFactory::getUser($fb_user->id);
			}

			if (empty($user_obj->id)) {
				JError::raiseWarning(0, $model->getError());
				$app->redirect(JRoute::_('index.php', false));
				return false;
			}

			if ($user_obj->get('block') == 1) {
				JError::raiseWarning(0, JText::_('JERROR_NOLOGIN_BLOCKED'));
				$app->redirect(JRoute::_('index.php', false));
				return false;
			}

			$user_obj->set('guest', 0);

			$model->updateSession($user_obj);

			$user_obj->setLastVisit();
			JFactory::getSession()->set('user', $user_obj);
			$app->redirect(JRoute::_('index.php', false));
			return false;
 		}
	}

	public function unsubscribe()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$key = $jinput->get->getString('key');
		$mail = JCrypt::decryptString($key);

		$user = PDeals::getUser($mail, 'email');
		if (empty($user->id)) {
			JError::raiseWarning(0, JText::_('COM_USERS_UNSUBSCRIBE_CODE_NOTFOUND'));
			$app->redirect(JRoute::_('index.php', false));
			return false;
		}

		if (!$user->isSubscribed()) {
			JError::raiseWarning(0, JText::_('COM_USERS_UNSUBSCRIBE_ALREADY_UNSUBSCRIBED'));
			$app->redirect(JRoute::_('index.php', false));
			return false;
		}
		$status = $user->unsubscribe();
		if (!$status) {
			JError::raiseWarning(0, JText::_('COM_USERS_UNSUBSCRIBE_ERROR'));
			$app->redirect(JRoute::_('index.php', false));
			return false;
		}

		$message = JText::_('COM_USERS_UNSUBSCRIBE_SUCCESS');
		$this->setRedirect(JRoute::_('index.php', false), $message);
		return false;
	}

	/**
	 * Method to log out a user.
	 *
	 * @since	1.6
	 */
	public function logout()
	{
		//JSession::checkToken('request') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();
		$params = $app->getParams();

		// Perform the log in.
		$error = $app->logout();

		// Check if the log out succeeded.
		if (!($error instanceof Exception)) {

			if (JDEBUG) {
				$allow_fb_authorization = $params->get('allow_fb_authorization');


				if ($allow_fb_authorization) {
					foreach($_SESSION as $k=>$v) {
						if (substr($k, 0, 3) == 'fb_') {
							unset($_SESSION[$k]);
						}
					}
				}

			}
			// Get the return url from the request and validate that it is internal.
			$return = JRequest::getVar('return', '', 'method', 'base64');
			$return = base64_decode($return);
			if (!JURI::isInternal($return)) {
				$return = '';
			}

			// Redirect the user.
			$app->redirect(JRoute::_($return, false));
		} else {
			$app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
	}

	/**
	 * Method to register a user.
	 *
	 * @since	1.6
	 */
	public function register()
	{
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		// Get the form data.
		$data	= JRequest::getVar('user', array(), 'post', 'array');

		// Get the model and validate the data.
		$model	= $this->getModel('Registration', 'UsersModel');
		$return	= $model->validate($data);

		// Check for errors.
		if ($return === false) {
			// Get the validation messages.
			$app	= &JFactory::getApplication();
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'notice');
				} else {
					$app->enqueueMessage($errors[$i], 'notice');
				}
			}

			// Save the data in the session.
			$app->setUserState('users.registration.form.data', $data);

			// Redirect back to the registration form.
			$this->setRedirect('index.php?option=com_users&view=registration');
			return false;
		}

		// Finish the registration.
		$return	= $model->register($data);

		// Check for errors.
		if ($return === false) {
			// Save the data in the session.
			$app->setUserState('users.registration.form.data', $data);

			// Redirect back to the registration form.
			$message = JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_users&view=registration', $message, 'error');
			return false;
		}

		// Flush the data from the session.
		$app->setUserState('users.registration.form.data', null);

		exit;
	}

	/**
	 * Method to login a user.
	 *
	 * @since	1.6
	 */
	public function remind()
	{
		// Check the request token.
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$model	= $this->getModel('User', 'UsersModel');
		$data	= JRequest::getVar('jform', array(), 'post', 'array');

		// Submit the username remind request.
		$return	= $model->processRemindRequest($data);

		// Check for a hard error.
		if ($return instanceof Exception) {
			// Get the error message to display.
			if ($app->getCfg('error_reporting')) {
				$message = $return->getMessage();
			} else {
				$message = JText::_('COM_USERS_REMIND_REQUEST_ERROR');
			}

			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route	= 'index.php?option=com_users&view=remind'.$itemid;

			// Go back to the complete form.
			$this->setRedirect(JRoute::_($route, false), $message, 'error');
			return false;
		} elseif ($return === false) {
			// Complete failed.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getRemindRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route	= 'index.php?option=com_users&view=remind'.$itemid;

			// Go back to the complete form.
			$message = JText::sprintf('COM_USERS_REMIND_REQUEST_FAILED', $model->getError());
			$this->setRedirect(JRoute::_($route, false), $message, 'notice');
			return false;
		} else {
			// Complete succeeded.
			// Get the route to the next page.
			$itemid = UsersHelperRoute::getLoginRoute();
			$itemid = $itemid !== null ? '&Itemid='.$itemid : '';
			$route	= 'index.php?option=com_users&view=login'.$itemid;

			// Proceed to the login form.
			$message = JText::_('COM_USERS_REMIND_REQUEST_SUCCESS');
			$this->setRedirect(JRoute::_($route, false), $message);
			return true;
		}
	}

	/**
	 * Method to login a user.
	 *
	 * @since	1.6
	 */
	public function resend()
	{
		// Check for request forgeries
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
	}
}

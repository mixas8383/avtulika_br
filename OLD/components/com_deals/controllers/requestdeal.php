<?php
/**
 * @package	LongCMS.Site
 * @subpackage	Contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.controllerform');

class DealsControllerRequestDeal extends JControllerForm
{
	public function submit()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model = $this->getModel();
		$params = $app->getParams();


		// Get the data from POST
		$data = JRequest::getVar('jform', array(), 'post', 'array');




		// Validate the posted data.
		$form = $model->getForm();

		if (!$form) {
			JError::raiseError(500, $model->getError());
			return false;
		}

		$validate = $model->validate($form, $data);


		if ($validate === false) {
			// Get the validation messages.
			$errors = $model->getErrors();
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'error');
				} else {
					$app->enqueueMessage($errors[$i], 'error');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_deals.requestdeal.data', $data);

			$Itemid = JMenu::getItemid('com_deals', 'requestdeal');
			// Redirect back to the contact form.
			$this->setRedirect(JRoute::_('index.php?option=com_deals&view=requestdeal&Itemid='.$Itemid, false));
			return false;
		}

		$model->sendMail($validate);


		// Flush the data from the session.
		$app->setUserState('com_deals.requestdeal.data', null);


		$this->setMessage(JText::_('COM_DEALS_REQUESTDEAL_SEND_SUCCESSFUL'), 'message');
		$this->setRedirect(JRoute::_('index.php', false));
	}



}

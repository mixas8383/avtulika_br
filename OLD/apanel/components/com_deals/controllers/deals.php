<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('core.application.component.controlleradmin');

/**
 * Banners list controller class.
 *
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class DealsControllerDeals extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_DEALS_DEALS';

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

	}

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Deal', $prefix = 'DealsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}


	public function relaunch()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$user	= JFactory::getUser();
		$jinput = $app->input;

		$cids = $jinput->post->get('cid', array(), 'array');

		JArrayHelper::toInteger($cids, null, true);

		$datetill = $jinput->post->get('datetill', array(), 'string');

		$jdate = JFactory::getDate($datetill);
		$jdate->setOffset(-4);
		$datetill = $jdate->toFormat('%Y-%m-%d %H:%M:%S', true);



		$canDo = DealsHelper::getActions();
		if (!$canDo->get('core.create')) {
			unset($cids);
			JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}

		if (empty($cids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
			$app->redirect('index.php?option=com_deals');
		}
		if (empty($datetill)) {
			JError::raiseWarning(500, JText::_('COM_DEALS_DEALS_NO_DATE_TYPED'));
			$app->redirect('index.php?option=com_deals');
		}



		// Get the model.
		$model = $this->getModel('Deals');

		// Publish the items.
		if (!$model->relaunch($cids, $datetill)) {
			JError::raiseWarning(500, $model->getError());
		}


		$this->setMessage(JText::_('COM_DEALS_DEALS_RELAUNCHED'));
		$this->setRedirect('index.php?option=com_deals&view=deals');
	}









	public function soldplus()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$user	= JFactory::getUser();
		$jinput = $app->input;

		$cids = $jinput->post->get('cid', array(), 'array');
		$value	= JArrayHelper::getValue($cids, 0, 0, 'int');

		$canDo = DealsHelper::getActions();
		if (!$canDo->get('core.create')) {
			unset($cids);
			JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}

		if (empty($value)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
			$app->redirect('index.php?option=com_deals');
		}
		// Get the model.
		$model = $this->getModel('Deals');

		// Publish the items.
		if (!$model->soldplus($value)) {
			JError::raiseWarning(500, $model->getError());
		}


		$this->setMessage(JText::_('COM_DEALS_DEALS_SOLDPLUS'));
		$this->setRedirect('index.php?option=com_deals&view=deals');
	}





	public function sendmail()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$user	= JFactory::getUser();
		$jinput = $app->input;

		$cids = $jinput->post->get('cid', array(), 'array');
		JArrayHelper::toInteger($cids, null, true);



		$canDo = DealsHelper::getActions();
		if (!$canDo->get('core.create')) {
			unset($cids);
			JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}

		if (empty($cids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
			$app->redirect('index.php?option=com_deals');
		}
		// Get the model.
		$model = $this->getModel('Deals');

		$result = PDeals::insertBatchMailJob($cids);

		if ($result->status) {
			$status = 'message';
			$msg = 'Mail job successfully added';
		} else {
			$status = 'error';
			$msg = $result->msg;
		}


		$this->setMessage($msg, $status);
		$this->setRedirect('index.php?option=com_deals&view=deals');
	}




}

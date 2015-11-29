<?php

/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Content Component Controller
 *
 * @package	LongCMS.Site
 * @subpackage	com_wrapper
 * @since		1.5
 */
class DealsController extends JControllerLegacy
{

    /**
     * Method to display a view.
     *
     * @param	boolean			If true, the view output will be cached
     * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        $cachable = true;


        // Set the default view name and format from the Request.
        $vName = JRequest::getCmd('view', 'deals');
        JRequest::setVar('view', $vName);

        parent::display($cachable, $urlparams);

        return $this;
    }

    public function make_deal()
    {
        $user = JFactory::getUser();
        $app = JFactory::getApplication();
        $id = $app->input->getInt('id', 0);
        $data = new stdClass();
        $data->state = false;
        $data->show_message = true;
        $data->message = JText::_('nothing change');
        
        
        if (empty($user->id))
        {
            $data->state = false;
            $data->message = JText::_('PLEAS_LOGIN_FIRST');
        }
        if (empty($id))
        {
            $data->state = false;
            $data->message = JTest::_('ID_NOT_SELECTED');
        }

        $model = $this->getModel('bids');

        $makeBid = $model->makeUserBid();

        if ($makeBid)
        {
            $data->state = true;
            $data->message = $model->getState('bids.message');
            $data->show_message = $model->getState('bids.show_message',0);
            $data->data = $makeBid;
        }else
        {
             $data->state = false;
            $data->message = $model->getState('bids.error');
            $data->show_message = true;
            $data->data = $makeBid;
        }



        echo json_encode($data);
        die;
    }

}

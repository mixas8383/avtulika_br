<?php
/**
 * @package    LongCMS.Site
 * @subpackage    Contact
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('core.application.component.controllerform');

class DealsControllerDeal extends JControllerForm
{
    public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, array('ignore_request' => false));
    }

    public function buy()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app    = JFactory::getApplication();
        $jinput = $app->input;
        $user   = PDeals::getUser();
        $id     = $jinput->post->getUint('id', 0);

        if (!$id) {
            $this->setMessage(JText::_('COM_DEALS_BUY_DEAL_NOTFOUND'), 'error');
            $this->setRedirect(JRoute::_('index.php', false));
            return false;
        }

        if (!$user->id) {
            $dealItemid = JMenu::getItemid('com_deals', 'deal');
            $dealurl    = JRoute::_('index.php?option=com_deals&view=deal&id=' . $id . 'Itemid=' . $dealItemid, false);
            $app->setUserState('users.login.form.return', $dealurl);
            $loginItemid = JMenu::getItemid('com_users', 'login');
            $this->setMessage(JText::_('COM_DEALS_BUY_MUSTLOGIN'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $loginItemid, false));
            return false;
        }

        if (!$user->hasProfile()) {
            $profileItemid = JMenu::getItemid('com_users', 'profile');
            $profileurl    = JRoute::_('index.php?option=com_users&view=profile&Itemid=' . $profileItemid, false);
            $this->setMessage(JText::_('COM_DEALS_BUY_MUSTPROFILE'), 'error');
            $this->setRedirect($profileurl);
            return false;
        }

        $deal = new Deal($id);

        if (!$deal->id) {
            $this->setMessage(JText::_('COM_DEALS_BUY_DEAL_NOTFOUND'), 'error');
            $this->setRedirect(JRoute::_('index.php', false));
            return false;
        }

        $allow = $deal->allowForBuy();
        if (!$allow) {
            $this->setMessage(JText::_('COM_DEALS_BUY_DEAL_NOTALLOWED'), 'error');
            $this->setRedirect(JRoute::_('index.php', false));
            return false;
        }
        $deals   = array();
        $deals[] = $deal->id;
        $session = JFactory::getSession();
        $session->set('deals', $deals);

        $buyItemid = JMenu::getItemid('com_deals', 'buy');

        $link = JRoute::_('index.php?option=com_deals&view=buy&Itemid=' . $buyItemid, false);
        $this->setRedirect($link);
    }

    public function buy2()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        //$link = 'https://docs.google.com/forms/d/1lJAT5gpoOgOHm_9fEhn6pat9mS4lbRn-hS1qbDy6EVo/viewform';
        $link = 'https://docs.google.com/forms/d/1-eKkmZyzyDGjmRLShfHC5h9Gj3yW_xWyvegz2O5J1Nc/viewform';

        header('Location: ' . $link);
    }

    public function buy3()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app    = JFactory::getApplication();
        $jinput = $app->input;
        $user   = PDeals::getUser();
        $id     = $jinput->post->getUint('id', 0);

        if (!$id) {
            $this->setMessage(JText::_('COM_DEALS_BUY_DEAL_NOTFOUND'), 'error');
            $this->setRedirect(JRoute::_('index.php', false));
            return false;
        }

        $deal = new Deal($id);

        if (!$deal->id) {
            $this->setMessage(JText::_('COM_DEALS_BUY_DEAL_NOTFOUND'), 'error');
            $this->setRedirect(JRoute::_('index.php', false));
            return false;
        }

        $allow = $deal->allowForBuy();
        if (!$allow) {
            $this->setMessage(JText::_('COM_DEALS_BUY_DEAL_NOTALLOWED'), 'error');
            $this->setRedirect(JRoute::_('index.php', false));
            return false;
        }
        $deals   = array();
        $deals[] = $deal->id;
        $session = JFactory::getSession();
        $session->set('deals', $deals);

        $buyItemid = JMenu::getItemid('com_deals', 'liberty');

        $link = JRoute::_('index.php?option=com_deals&view=liberty&Itemid=' . $buyItemid, false);
        $this->setRedirect($link);
    }

    public function buy4()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $link = 'https://docs.google.com/forms/d/1T2BgNYMNPp4PhG7YaGRoFUfUV7DfRSnQnYvFPi8lWzs/viewform';

        header('Location: ' . $link);
    }

    public function buy5()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $link = 'https://docs.google.com/forms/d/1Wcu-NCAUXHt0L_-2boUe199tBqG2L-14TJh-uDs_XfQ/viewform';

        header('Location: ' . $link);
    }

    public function liberty()
    {
        Transaction::log('LIBERTY RESPONSE', true, 'liberty');
        JResponse::setHeader('Content-Type', 'text/xml');
        JResponse::sendHeaders();
        echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        ?>
		<result>
			<resultcode>resultcode</resultcode>
			<resultdesc>resultdesc</resultdesc>
			<check>check</check>
			<data>data</data>
		</result>
		<?php
die();
    }

}

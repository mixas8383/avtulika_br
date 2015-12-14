<?php
/**
 * @package    LongCMS.Platform
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die('Restricted access');

jimport('core.utilities.math');
jimport('project.exception');
jimport('project.balance');
jimport('project.deal');
jimport('project.user');
jimport('project.transaction');
jimport('project.paymethods.paymethod');
jimport('project.mailjob');

/**
 * LongCMS Platform Factory class
 *
 * @package  LongCMS.Platform
 * @since    11.1
 */
abstract class PDeals
{

    private static $_user;
    private static $_months = array(
        1 => 'იანვარს',
        2 => 'თებერვალს',
        3 => 'მარტს',
        4 => 'აპრილს',
        5 => 'მაისს',
        6 => 'ივნისს',
        7 => 'ივლისს',
        8 => 'აგვისტოს',
        9 => 'სექტემბერს',
        10 => 'ოქტომბერს',
        11 => 'ნოემბერს',
        12 => 'დეკემბერს',
    );

    public static function getCategories($filter = false, $only_parents = false)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__deals_categories');
        if ($filter)
        {
            $query->where('published = 1');
        }
        if ($only_parents)
        {
            $query->where('level != 3');
        }

        //$query->where('a.extension = '.$db->quote($extension));
        $query->order('parent ASC, title ASC');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        return $data;
    }

    public static function getCompanies()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__deals_companies');
        //$query->where('a.extension = '.$db->quote($extension));
        $query->order('title ASC');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        return $data;
    }

    public static function getCities()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__deals_cities');
        //$query->where('a.extension = '.$db->quote($extension));
        $query->order('title ASC');
        $db->setQuery($query);
        $data = $db->loadObjectList();
        return $data;
    }

    public static function getUser($id = null, $field = 'id')
    {
        if (empty(self::$_user[$field][$id]))
        {
            self::$_user[$field][$id] = User::getInstance($id, $field);
        }
        return self::$_user[$field][$id];
    }

    public static function getMonthName($m)
    {
        return !empty(self::$_months[$m]) ? self::$_months[$m] : false;
    }

    public static function insertBatchMailJob(array $idx = array())
    {
        $return = new stdClass;
        $return->msg = '';
        $return->status = false;

        if (empty($idx))
        {
            $return->msg = 'Deal idx is empty';
            return $return;
        }
        $mailjob = new MailJob;

        try
        {
            $mailjob->create($idx);
        } catch (Exception $e)
        {
            $return->msg = $e->getMessage();
            return $return;
        }

        $return->status = true;
        return $return;
    }

    public static function runBatchMailJob()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $db->quote($db->getNullDate());

        $return = new stdClass;
        $return->msg = '';
        $return->status = false;

        $mailjob = new MailJob;
        try
        {
            $mailjob->load();
        } catch (Exception $e)
        {
            $return->msg = $e->getMessage();
            return $return;
        }
        $return->status = true;
        return $return;
    }

    public static function getDeals($category = null, $limit = null, $orderBy = null, $search = false)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $db->quote($db->getNullDate());

        $query->select('d.*');
        $query->from('#__deals_deals AS d');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

        // Join over the cities.
        $query->select('ci.title AS city_title');
        $query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

        // Join over the companies.
        $query->select('co.title AS company_title');
        $query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

        if ($category)
        {
            $query->where('d.category_id = ' . (int) $category);
        }
        $query->where('d.is_market = 0');
        $query->where('d.state = 1');
        $query->where('(d.publish_up = ' . $nullDate . ' OR d.publish_up <= ' . $query->quote($now) . ')');
        $query->where('((d.publish_down < ' . $query->quote($now) . ') AND ( d.bid_date > ' . $query->quote($now) . ') OR d.publish_down >= ' . $query->quote($now) . ')');

        //  $query->where('(d.publish_down < ' . $query->quote($now) . ') AND ( d.bid_date > ' . $query->quote($now) . ')');



        if ($search)
        {
            $post = JRequest::get('post');
            if (!empty($post['searchword']))
            {
                $searchword = $post['searchword'];
                $words = explode(' ', $post['searchword']);
                //$wheres = array();
                foreach ($words as $word)
                {
                    $word = $db->quote('%' . $db->escape($word, true) . '%', false);
                    $query->where('(d.title LIKE ' . $word . ' OR d.text LIKE ' . $word . ')');
                }

                //$query->where('(d.title LIKE "'.$word.'" OR d.text LIKE "'.$word.'")');
            }
        }

        if ($orderBy)
        {
            $query->order($orderBy);
        } else
        {
            $query->order('d.ordering ASC, d.publish_up DESC');
        }

        if ($limit)
        {
            $db->setQuery($query, 0, intval($limit));
        } else
        {
            $db->setQuery($query);
        }

        $data = $db->loadAssocList();

        $return = array();
        foreach ($data as $arr)
        {
            $obj = new Deal($arr);
            $return[] = $obj;
        }

        /* if (JDEBUG) {
          $i = 1;
          $success = 0;
          foreach($data as $item) {
          $id = $item['id'];
          if (!$id) {
          continue;
          }
          $sql = ' UPDATE `#__deals_deals` '
          .' SET `ordering`='.$i.' '
          .' WHERE `id`='.$id.' '
          .' LIMIT 1'
          ;
          $db->setQuery($sql);
          if ($db->query()) {
          $success++;
          }
          $i++;
          }


          } */

        return $return;
    }

    public static function getExDeals()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $db->quote($db->getNullDate());
        $app = JFactory::getApplication();
        $jinput = $app->input;

        // count
        $query->select('COUNT(d.id) AS total');
        $query->from('#__deals_deals AS d');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

        // Join over the cities.
        $query->select('ci.title AS city_title');
        $query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

        // Join over the companies.
        $query->select('co.title AS company_title');
        $query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

        $query->where('d.is_market = 0');
        $query->where('d.state = 1');
        //$query->where('(d.publish_up = '.$nullDate.' OR d.publish_up <= '.$query->quote($now).')');
        $query->where('(d.publish_down != ' . $nullDate . ' AND d.publish_down <= ' . $query->quote($now) . ')');
        $db->setQuery($query);
        $total = $db->loadResult();

        jimport('core.html.pagination');
        $limit = 15;
        $start = $jinput->get->getUint('start', 0);

        $pagination = new JPagination($total, $start, $limit);

        $query->clear();
        $query->select('d.*');
        $query->from('#__deals_deals AS d');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

        // Join over the cities.
        $query->select('ci.title AS city_title');
        $query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

        // Join over the companies.
        $query->select('co.title AS company_title');
        $query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

        $query->where('d.state = 1');
        //$query->where('(d.publish_up = '.$nullDate.' OR d.publish_up <= '.$query->quote($now).')');
        $query->where('(d.publish_down != ' . $nullDate . ' AND d.publish_down <= ' . $query->quote($now) . ')');

        $query->order('d.publish_down DESC');
        $db->setQuery($query, $pagination->limitstart, $pagination->limit);

        $data = $db->loadAssocList();

        $return = array();
        foreach ($data as $arr)
        {
            $obj = new Deal($arr);
            $return[] = $obj;
        }

        $retData = new stdClass;
        $retData->data = $return;
        $retData->pagination = $pagination;
        return $retData;
    }

    public static function getMarketDeals()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $db->quote($db->getNullDate());
        $app = JFactory::getApplication();
        $jinput = $app->input;

        // count
        $query->select('COUNT(d.id) AS total');
        $query->from('#__deals_deals AS d');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

        // Join over the cities.
        $query->select('ci.title AS city_title');
        $query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

        // Join over the companies.
        $query->select('co.title AS company_title');
        $query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

        $query->where('d.state = 1');
        //$query->where('(d.publish_up = '.$nullDate.' OR d.publish_up <= '.$query->quote($now).')');
        $query->where('(d.publish_down != ' . $nullDate . ' AND d.publish_down <= ' . $query->quote($now) . ')');
        $db->setQuery($query);
        $total = $db->loadResult();

        jimport('core.html.pagination');
        $limit = 15;
        $start = $jinput->get->getUint('start', 0);

        $pagination = new JPagination($total, $start, $limit);

        $query->clear();
        $query->select('d.*');
        $query->from('#__deals_deals AS d');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__deals_categories AS c ON c.id = d.category_id');

        // Join over the cities.
        $query->select('ci.title AS city_title');
        $query->join('LEFT', '#__deals_cities AS ci ON ci.id = d.city_id');

        // Join over the companies.
        $query->select('co.title AS company_title');
        $query->join('LEFT', '#__deals_companies AS co ON co.id = d.company_id');

        $query->where('d.state = 1');
        $query->where('d.is_market = 1');
        $query->where('(d.publish_up = ' . $nullDate . ' OR d.publish_up <= ' . $query->quote($now) . ')');
        $query->where('(d.publish_down = ' . $nullDate . ' OR d.publish_down >= ' . $query->quote($now) . ')');

        $query->order('d.publish_down DESC');
        $db->setQuery($query, $pagination->limitstart, $pagination->limit);

        $data = $db->loadAssocList();

        $return = array();
        foreach ($data as $arr)
        {
            $obj = new Deal($arr);
            $return[] = $obj;
        }

        $retData = new stdClass;
        $retData->data = $return;
        $retData->pagination = $pagination;
        return $retData;
    }

    public static function sendMailToAdmin($transaction)
    {
        $mailer = JFactory::getMailer();
        $app = JFactory::getApplication();
        $mailfrom = $app->getCfg('mailfrom');
        $fromname = $app->getCfg('fromname');
        $params = JComponentHelper::getParams('com_deals');

        $deals = $transaction->getDeals();
        if (empty($deals))
        {
            return false;
        }

        $user = $transaction->getUser();
        if (!$user->get('id'))
        {
            return false;
        }

        $admin_mail = $params->get('receiver_mail');
        if (!$admin_mail)
        {
            return false;
        }

        $body = self::_parseTemplate('admin', $transaction);
        $subject = JText::sprintf('COM_DEALS_BUY_USER_MAIL_SUBJECT');

        if (JDEBUG)
        {
            return true;
        }

        $send = $mailer->sendMail($mailfrom, $fromname, $admin_mail, $subject, $body, true);

        return $send;
    }

    public static function sendMailToUser($transaction)
    {
        $mailer = JFactory::getMailer();
        $app = JFactory::getApplication();
        $mailfrom = $app->getCfg('mailfrom');
        $fromname = $app->getCfg('fromname');
        $params = JComponentHelper::getParams('com_deals');

        $deals = $transaction->getDeals();
        if (empty($deals))
        {
            return false;
        }

        $user = $transaction->getUser();

        if (!$user->get('id'))
        {
            return false;
        }

        $user_mail = $user->get('email');
        if (!$user_mail)
        {
            return false;
        }

        $body = self::_parseTemplate('user', $transaction);
        $subject = JText::sprintf('COM_DEALS_BUY_USER_MAIL_SUBJECT');
        $send = $mailer->sendMail($mailfrom, $fromname, $user_mail, $subject, $body, true);

        return $send;
    }

    public static function sendMailToCompany($transaction)
    {
        $mailer = JFactory::getMailer();
        $app = JFactory::getApplication();
        $mailfrom = $app->getCfg('mailfrom');
        $fromname = $app->getCfg('fromname');
        $params = JComponentHelper::getParams('com_deals');

        $deals = $transaction->getDeals();
        if (empty($deals))
        {
            return false;
        }

        $user = $transaction->getUser();
        if (!$user->get('id'))
        {
            return false;
        }

        $company = $transaction->getCompany();
        $company_mail = $company->mail;
        if (!$company_mail)
        {
            return false;
        }

        $body = self::_parseTemplate('user', $transaction);
        $subject = JText::sprintf('COM_DEALS_BUY_USER_MAIL_SUBJECT');

        if (JDEBUG)
        {
            return true;
        }

        $send = $mailer->sendMail($mailfrom, $fromname, $company_mail, $subject, $body, true);

        return $send;
    }

    public static function sendBalanceAddMailToUser($transaction)
    {
        $mailer = JFactory::getMailer();
        $app = JFactory::getApplication();
        $mailfrom = $app->getCfg('mailfrom');
        $fromname = $app->getCfg('fromname');
        $params = JComponentHelper::getParams('com_deals');

        $user = $transaction->getUser();
        if (!$user->get('id'))
        {
            return false;
        }

        $user_mail = $user->get('email');
        if (!$user_mail)
        {
            return false;
        }
        $total = $transaction->getTotal();

        $lang = JFactory::getLanguage();
        $lang->load('com_deals');

        $body = JText::sprintf('COM_DEALS_DEPOSIT_USER_MAIL_BODY', $total);
        $subject = JText::sprintf('COM_DEALS_DEPOSIT_USER_MAIL_SUBJECT');

        $send = $mailer->sendMail($mailfrom, $fromname, $user_mail, $subject, $body, true);

        return $send;
    }

    private static function _parseTemplate($template, $transaction)
    {

        // @TODO: must refactor
        $template = 'user';

        $tpl_body = self::_getTemplate($template);
        if (!$tpl_body)
        {
            return false;
        }
        $app = JFactory::getApplication();
        $tpl = $app->getTemplate(true);
        $logo = $tpl->params->get('logo');

        $deals = $transaction->getDeals();

        $user = $transaction->getUser();

        $site_url = 'http://www.brao.ge/';

        switch ($template)
        {
            case 'user':

                $COUPON_CODE = '124214';
                $tpl_body = str_replace('{COUPON_CODE}', $COUPON_CODE, $tpl_body);

                $BRAO_LOGO = $site_url . $logo;
                $tpl_body = str_replace('{BRAO_LOGO}', $BRAO_LOGO, $tpl_body);

                $BRAO_TRANS_DATE = $transaction->getDate();
                $tpl_body = str_replace('{BRAO_TRANS_DATE}', $BRAO_TRANS_DATE, $tpl_body);

                $BRAO_TRANS_NUMBER = $transaction->getTransactionNumber();
                $tpl_body = str_replace('{BRAO_TRANS_NUMBER}', $BRAO_TRANS_NUMBER, $tpl_body);

                $deal = $deals[0];

                $PRODUCT_IMAGE = $site_url . $deal->getImage(1, 'image10');
                $tpl_body = str_replace('{PRODUCT_IMAGE}', $PRODUCT_IMAGE, $tpl_body);

                $PRODUCT_TITLE = $deal->getTitle();
                $tpl_body = str_replace('{PRODUCT_TITLE}', $PRODUCT_TITLE, $tpl_body);

                $PRODUCT_PRICE = $deal->getPrice() . ' ' . JText::_('GELI');
                $tpl_body = str_replace('{PRODUCT_PRICE}', $PRODUCT_PRICE, $tpl_body);

                $PRODUCT_DESCRIPTION = $deal->getText();
                $tpl_body = str_replace('{PRODUCT_DESCRIPTION}', $PRODUCT_DESCRIPTION, $tpl_body);

                $COMPANY_NAME = $deal->getCompanyName();
                $COMPANY_ADDRESS = $deal->getCompanyAddress();
                $COMPANY_PHONE = $deal->getCompanyPhone();
                $COMPANY_HOURS = $deal->getCompanyHours();
                $COMPANY_URL = $deal->getCompanyUrl();
                ob_start();
                if (!empty($COMPANY_NAME))
                {
                    ?>
                    <div class="deal_company_title" style="padding-bottom: 10px;">
                        <img src="		<?php echo $site_url ?>templates/longcms/images/icons/country_icon.png" alt="ico" title="კომპანიის სახელი" />
                        <span style="font-weight:bold;line-height:24px;width:284px;padding-left:10px;">
                            <?php echo $COMPANY_NAME; ?>
                        </span>
                        <div style="clear:both;"></div>
                    </div>
                    <?php
                }

                if (!empty($COMPANY_URL))
                {
                    $company_url2 = $COMPANY_URL;
                    if (substr($company_url2, 0, 4) !== 'http')
                    {
                        $company_url2 = 'http://' . $company_url2;
                    }
                    ?>
                    <div class="deal_company_url" style="padding-bottom: 10px;">
                        <img src="		<?php echo $site_url ?>templates/longcms/images/icons/web_icon.png" alt="ico" title="კომპანიის ვებ-საიტი" />
                        <span style="line-height:24px;width:284px;padding-left:10px;">
                            <a href="		<?php echo $company_url2; ?>" target="_blank">
                                <?php echo $COMPANY_URL; ?>
                            </a>
                        </span>
                        <div style="clear:both;"></div>
                    </div>
                    <?php
                }

                if (!empty($COMPANY_ADDRESS))
                {
                    ?>
                    <div class="deal_company_address" style="padding-bottom: 10px;">
                        <img src="		<?php echo $site_url ?>templates/longcms/images/icons/info_icon.png" alt="ico" title="კომპანიის მისამართი" />
                        <span style="line-height:24px;width:284px;padding-left:10px;">
                            <?php echo $COMPANY_ADDRESS; ?>
                        </span>
                        <div style="clear:both;"></div>
                    </div>
                    <?php
                }
                if (!empty($COMPANY_PHONE))
                {
                    ?>
                    <div class="deal_company_phone" style="padding-bottom: 10px;">
                        <img src="		<?php echo $site_url ?>templates/longcms/images/icons/phone_icon.png" alt="ico" title="კომპანიის ტელეფონი" />
                        <span style="line-height:24px;width:284px;padding-left:10px;">
                            <?php echo $COMPANY_PHONE; ?>
                        </span>
                        <div style="clear:both;"></div>
                    </div>
                    <?php
                }
                if (!empty($COMPANY_HOURS))
                {
                    ?>
                    <div class="deal_company_hours" style="padding-bottom: 10px;">
                        <img src="		<?php echo $site_url ?>templates/longcms/images/icons/time_icon.png" alt="ico" title="კომპანიის სამუშაო საათები" />
                        <span style="line-height:24px;width:284px;padding-left:10px;">
                            <?php echo $COMPANY_HOURS; ?>
                        </span>
                        <div style="clear:both;"></div>
                    </div>
                    <?php
                }
                $chtml = ob_get_clean();

                $tpl_body = str_replace('{COMPANY_DESCRIPTION}', $chtml, $tpl_body);

                $USER_FULL_NAME = $user->get('name') . ' ' . $user->get('surname');
                $tpl_body = str_replace('{USER_FULL_NAME}', $USER_FULL_NAME, $tpl_body);

                $USER_PERS_NUM = $user->get('persNumber');
                $tpl_body = str_replace('{USER_PERS_NUM}', $USER_PERS_NUM, $tpl_body);

                $USER_MOB = $user->get('mobile');
                $tpl_body = str_replace('{USER_MOB}', $USER_MOB, $tpl_body);

                break;
        }
        /* if (JDEBUG) {
          print($tpl_body);
          die;

          } */

        return $tpl_body;
    }

    public static function _parseBatchTemplate(array $deals)
    {
        if (empty($deals))
        {
            return false;
        }
        $app = JFactory::getApplication();
        $tpl = $app->getTemplate(true);
        $logo = $tpl->params->get('logo');
        $site_url = 'http://www.brao.ge/';

        $file = JPATH_LIBRARIES . '/project/mail/batch_head.php';
        if (!JFile::exists($file))
        {
            return false;
        }
        ob_start();
        require $file;
        $body_head = ob_get_clean();

        $file = JPATH_LIBRARIES . '/project/mail/batch_foot.php';
        if (!JFile::exists($file))
        {
            return false;
        }
        ob_start();
        require $file;
        $body_foot = ob_get_clean();

        $file = JPATH_LIBRARIES . '/project/mail/batch_deal.php';
        if (!JFile::exists($file))
        {
            return false;
        }
        ob_start();
        require $file;
        $body_deal = ob_get_clean();

        $body = $body_head;
        foreach ($deals as $deal)
        {
            $b_deal = $body_deal;

            // $BRAO_LOGO = $site_url.$logo;
            // $tpl_body = str_replace('{BRAO_LOGO}', $BRAO_LOGO, $tpl_body);

            $PRODUCT_IMAGE = $site_url . $deal->getImage(1, 'image10');
            $b_deal = str_replace('{PRODUCT_IMAGE}', $PRODUCT_IMAGE, $b_deal);

            $PRODUCT_TITLE = $deal->getTitle();
            $b_deal = str_replace('{PRODUCT_TITLE}', $PRODUCT_TITLE, $b_deal);

            $PRODUCT_LINK = $site_url . $deal->getUrl('&utm_source=mail');
            $b_deal = str_replace('{PRODUCT_LINK}', $PRODUCT_LINK, $b_deal);

            $PRODUCT_PRICE = $deal->getPrice() . ' ' . JText::_('GELI');
            $b_deal = str_replace('{PRODUCT_PRICE}', $PRODUCT_PRICE, $b_deal);

            $PRODUCT_DESCRIPTION = $deal->getText();
            $b_deal = str_replace('{PRODUCT_DESCRIPTION}', $PRODUCT_DESCRIPTION, $b_deal);

            $body .= $b_deal;
        }
        $body .= $body_foot;

        return $body;
    }

    private static function _getTemplate($template)
    {
        $file = JPATH_LIBRARIES . '/project/mail/' . $template . '.php';
        if (!JFile::exists($file))
        {
            return false;
        }

        ob_start();
        require $file;
        $body = ob_get_clean();
        return $body;
    }

    public static function sortCategories($categories, $for_display = false, $for_frontend = false)
    {

        $result = self::buildTree($categories);

        if ($for_display)
        {
            $array = array();
            self::sortTree($result, $array);
            $result = $array;
        }

        if ($for_frontend)
        {
            usort($result, array('PDeals', 'cmp'));
        }

        return $result;
    }

    public static function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element)
        {
            if ($element->parent == $parentId)
            {
                $children = self::buildTree($elements, $element->id);
                if ($children)
                {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public static function sortTree(array $elements, &$array)
    {

        foreach ($elements as $element)
        {
            $array[] = $element;
            if (!empty($element->children))
            {
                self::sortTree($element->children, $array);
                unset($element->children);
            }
        }


        return true;
    }

    public static function cmp($a, $b)
    {
        return strcmp($a->ordering, $b->ordering);
    }

    public static function getActiveAution($category = null, $limit = null, $orderBy = null, $search = false)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $db->quote($db->getNullDate());

        $query->select('d.*');
        $query->from('#__deals_deals AS d');



        // $query->where('(d.publish_up = ' . $nullDate . ' OR d.publish_up <= ' . $query->quote($now) . ')');
        //$query->where('(d.publish_down = ' . $nullDate . ' OR d.publish_down >= ' . $query->quote($now) . ')');
        $query->where('(d.publish_down < ' . $query->quote($now) . ') AND ( d.bid_date > ' . $query->quote($now) . ')');






        $db->setQuery($query);
        $data = $db->loadAssocList();

        $return = array();
        foreach ($data as $arr)
        {
            $obj = new Deal($arr);
            $return[] = $obj;
        }



        return $return;
    }

    public static function getUpdateDeals($ids = array())
    {
        if (empty($ids))
        {
            return false;
        }
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $db->quote($db->getNullDate());

        $query->select('d.*');
        $query->from('#__deals_deals AS d');



        // $query->where('(d.publish_up = ' . $nullDate . ' OR d.publish_up <= ' . $query->quote($now) . ')');
        //$query->where('(d.publish_down = ' . $nullDate . ' OR d.publish_down >= ' . $query->quote($now) . ')');
        $query->where('(d.publish_down < ' . $query->quote($now) . ') AND ( d.bid_date > ' . $query->quote($now) . ')');


        $where = array();

        $where[] = 'd.id in (' . implode(',', $ids) . ')';

        $where = count($where) ? ' WHERE (' . implode(') AND (', $where) . ')' : '';


        $db->setQuery(''
                . ' SELECT d.*,'
                . ''
                . '('
                . '     select concat(subu.name," ",subu.surname) '
                . '     from #__deals_bids AS subd'
                . '     left join #__users AS subu ON subu.id=subd.user_id'
                . '     where  subd.deal_id=d.id'
                . '     ORDER BY subd.id DESC limit 1'
                . ') as username '
                . ''
                . ''
                . ' from #__deals_deals AS d '
                . ' '
                . ''
                . $where
                . ''
                . '');
        $data = $db->loadObjectList();

        $return = array();
        $now = JFactory::getDate();
        foreach ($data as $arr)
        {
            //  $obj = new Deal($arr);

            $bidDate = JFactory::getDate($arr->bid_date);
            $jdate = JFactory::getDate($arr->publish_down);

            $arr->activeLot = 0;
            $arr->leftTime=0;
            
            if (($jdate->toUnix() - $now->toUnix()) < (60 * 60 * 24) && ($jdate->toUnix() - $now->toUnix()))
            {
                $arr->leftTime = $jdate->toUnix() - $now->toUnix();
            }

            if (($jdate->toUnix() - $now->toUnix()) < 0 && ($bidDate->toUnix() - $now->toUnix()) > 0)
            {
                $arr->activeLot = 1;
                $arr->leftTime = $bidDate->toUnix() - $now->toUnix();
            }




            $return[] = $arr;
        }



        return $return;
    }

    public static function loadActiveLot($id)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $jdate = JFactory::getDate();
        $now = $jdate->toSql();
        $nullDate = $db->quote($db->getNullDate());
        $query->select('d.*');
        $query->from('#__deals_deals AS d');
        $query->where('( d.bid_date > ' . $query->quote($now) . ')');
        $query->where(' d.id =' . (int) $id);
        //  $db->setQuery($query);
        $db->setQuery($query, 0, 1);
        $data = $db->loadAssoc();
        $obj = new Deal($data);
        return $obj;
    }

}

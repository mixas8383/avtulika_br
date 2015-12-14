<?php
/**
 * @package    LongCMS.Site
 * @subpackage    com_blank
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
?>
<div class="deals">

    <?php
    if ($this->params->get('show_page_heading'))
    {
        ?>
        <div class="page_title">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
        <?php
    }
    $deal = $this->item;

    $pageIds = array();
    $pageIds[] = 1;

    if ($deal->id)
    {
        $pageIds[] = $deal->id;
        $buyItemid = JMenu::getItemid('com_deals', 'buy');
        $dealItemid = JMenu::getItemid('com_deals', 'deal');
        $price = $deal->getPrice();
        $old_price = $deal->getOldPrice();
        $saving = $deal->getSaving();
        $title = $deal->getTitle();
        $text = $deal->getText();
        $id = $deal->getId();
        $link = JRoute::_('index.php?option=com_deals&view=deal&id=' . $id . '&Itemid=' . $buyItemid);
        $sold = $deal->getSold();
        $finish = $deal->getFinishDate();
        $description = $deal->getDescription();

        $company_title = $deal->getCompanyName();
        $company_map = $deal->getCompanyMap();
        $company_description = $deal->getCompanyDescription();


        // increment hits
        $deal->hit();

        $deal_url = JRoute::_('index.php?option=com_deals&view=deal&id=' . $id . '&Itemid=' . $dealItemid);
        $deal_url = htmlspecialchars($deal_url, ENT_QUOTES);
        //$deal_url = urlencode($deal_url);
        $deal_url = JURI::root(false, false, true) . $deal_url;


        $this->document->setMetadata('og:type', 'product');
        $fb_image = $deal->getImage(1, 'image13');

        if (!empty($deal_url))
        {
            $this->document->setMetadata('og:url', $deal_url);
        }

        if (!empty($fb_image))
        {
            $this->document->setMetadata('og:image', JURI::root() . $fb_image);
        }

        if (!empty($title))
        {
            $this->document->setMetadata('og:title', $title);
        }
        if (!empty($text))
        {
            $desc_text = strip_tags($text);
            $desc_text = preg_replace('#\s+#', ' ', $desc_text);
            $desc_text = htmlspecialchars($desc_text, ENT_QUOTES, 'UTF-8');
            $this->document->setMetadata('og:description', $desc_text);
        }





        $images = '';
        $images_count = 0;
        for ($i = 1; $i <= 5; $i++)
        {
            $image_big = $deal->getImage($i, 'image14');
            if (!$image_big)
            {
                continue;
            }
            $style = $i == 1 ? 'display:block' : 'display:none';
            ob_start();
            ?>
            <div class="deal_bimg" id="bimg_<?php echo $i ?>" style="<?php echo $style ?>">
                <div class="deal_bimg_in">
                    <img src="<?php echo $image_big ?>" />
                </div>
            </div>
            <?php
            $images .= ob_get_clean();
            $images_count++;
        }


        ob_start();
        ?>
        <script>
            var currentPageIds = new Array(<?php echo implode(',', $pageIds); ?>);
            $(function () {
                $("#deal_bimgs_in").cycle({
                    fx: "fade",
                    height: 330,
                    width: 582,
                    fit: true,
                    pause: true,
                    nowrap: 0,
                    prev: "#images_prev",
                    next: "#images_next",
                    speed: 2000
                });
            });
        </script>
        <?php
        $js = ob_get_clean();
        $this->document->addScriptDeclaration($js);
        ?>
        <!-- Left block -->
        <div class="deal_imgs">

            <div class="deal_title2">
                <?php echo $title; ?>
            </div>

            <div class="deal_bimgs">
                <div class="deal_bimgs_in" id="deal_bimgs_in">
                    <?php echo $images; ?>
                </div>
                <?php
                if ($images_count > 1)
                {
                    ?>
                    <div class="images_prevnext">
                        <a id="images_prev" href="javascript:void(0);"></a>
                        <a id="images_next" href="javascript:void(0);"></a>
                    </div>
                    <?php
                }
                ?>
                <div class="deal_img_block">
                    <div class="deal_price_sold">
                        <span class="dealin_price">
                            <!--                            <div class="price_old">
                            <?php echo JText::_('COM_DEALS_DEALS_PRICE_OLD') ?>
                                                            <span class="deal_price2">
                            <?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', $old_price) ?>
                                                            </span>
                                                        </div>-->
                            <div class="price_current">
                                <?php echo JText::_('COM_DEALS_DEALS_PRICE_CURRENT') ?>
                                <span class="deal_price1">
                                    <?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', '') ?>
                                    <span class="itemPrice_<?php echo $deal->id; ?>"> 
                                        <?php echo $price ?>
                                    </span>
                                </span>
                            </div>
                            <div class="cls"></div>
                        </span>
                        <!--                        <div class="price_danazog">
                                                    <div class="price_dan">
                        <?php echo JText::_('COM_DEALS_DEALS_DAN') ?>
                                                        <span>
                        <?php echo JText::sprintf('COM_DEALS_DEALS_PRICE3', $saving) ?>
                                                        </span>
                                                    </div>
                                                </div>-->
                        <!--                        <span class="dealin_sold">
                                                    <div class="dealin_soldtitle">
                        <?php echo JText::_('COM_DEALS_DEALS_SOLD_IN') ?>
                                                    </div>
                                                    <span>
                        <?php echo $sold ?>
                                                    </span>
                                                </span>-->
                        <div class="cls"></div>
                    </div>
                    <?php
                    if ($deal->isSoldOut())
                    {
                        ?>
                        <div class="deal_sold_out">
                            <img src="templates/longcms/images/sold_out.png" alt="Sold out" />
                        </div>
                        <?php
                    } else if ($deal->isFinished())
                    {
                        ?>
                        <div class="deal_sold_out">
                            <img src="templates/longcms/images/expired.png" alt="Expired" />
                        </div>
                        <?php
                    }/* else if ($deal->allowForBuy()) {
                      ?>
                      <div class="deal_buy">
                      <form id="deal_buy" action="<?php echo JRoute::_('index.php?option=com_deals&task=deal.buy'); ?>" method="post">
                      <button type="submit"></button>
                      <input type="hidden" name="id" value="<?php echo $id ?>" />
                      <input type="hidden" name="option" value="com_deals" />
                      <input type="hidden" name="task" value="deal.buy" />
                      <?php echo JHtml::_('form.token'); ?>
                      </form>
                      </div>
                      <?php
                      } */
                    ?>
                    <span>
                        <div class="dealin_time" style="float:left;font-size: 14px;color: green">
                            <?php
                            if ($deal->isFinished())
                            {
                                echo JText::sprintf('COM_DEALS_DEALS_FINISHED', $finish);
                            } else
                            {
                                echo JText::sprintf('COM_DEALS_DEALS_FINISH', $finish);
                            }
                            ?>
                        </div>

                        <div class="bid_button" style="float: right;display: block;width: 100px;background-color: orange;text-align: center;padding: 2px;border-radius: 2px" >
                            <?php echo $deal->getBidButton(); ?>
                        </div>
                        <div class="cls"></div>
                    </span>
                    <div class="deal_user_<?php echo $deal->id; ?>"><?php echo $deal->getLastBidUser(); ?></div>
                </div>
            </div>


            <div class="deal_socials">

                <div class="socials">
                    <span>
                        <a target="_blank" rel="nofollow" href="http://www.facebook.com/sharer.php?u=<?php echo $deal_url; ?>&amp;title=<?php echo $deal_url; ?>" title="Facebook"  onclick="window.open(this.href, 'FaceBook', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=700,height=480,directories=no,location=no');
                                    return false;">
                            <img src="templates/longcms/images/icons/facebook.png" alt="FaceBook" />
                        </a>
                    </span>
                    <span>
                        <a target="_blank" rel="nofollow" href="http://twitter.com/home?status=<?php echo $title; ?>&amp;title=<?php echo $title; ?>" title="Twitter"  onclick="window.open(this.href, 'Twitter', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=700,height=480,directories=no,location=no');
                                    return false;">
                            <img src="templates/longcms/images/icons/twitter.png" alt="Twitter"/>
                        </a>
                    </span>

                    <g:plusone size="medium" href="<?php echo $deal_url ?>"></g:plusone>
                    <?php
                    $script = 'window.___gcfg = {lang: "en-GB", parsetags: "onload"};
                                  (function() {
                                    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
                                    po.src = "https://apis.google.com/js/plusone.js";
                                    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
                                  })();
                                ';
                    $this->document->addScriptDeclaration($script);
                    ?>
                    <span>
                        <fb:like href="<?php echo $deal_url ?>" ref="content_<?php echo $id ?>" send="false" layout="button_count" width="450" show_faces="false"></fb:like>
                    </span>
                </div>

                <div class="cls"></div>
            </div>
            <div class="cls"></div>

            <div class="deal_title3">
                <?php echo JText::_('COM_DEALS_DEALS_ABOUT') ?>
            </div>

            <?php
            $video = $deal->getVideo();
            if (!empty($video))
            {
                ?>
                <div id="deal_video" style="width:592px;height:300px;text-align:center;padding-top:10px">
                    <img style="margin-top: 50px;" src="/templates/longcms/images/icons/paymethods_preloader.gif" alt="loading" />
                </div>
                <?php
                $js = 'jwplayer("deal_video").setup({
                            file: "' . $video . '",
                            width: "592",
                            height: "300",
                            primary: "flash",
                            controls: true,
                            stretching: "fill",
                            flashplayer: "' . JURI::root() . 'media/system/swf/jw_player.swf"
                        });';
                $this->document->addScriptDeclaration($js);
            }
            ?>
            <div class="deal_text2">
                <?php echo $text; ?>
            </div>

            <div class="deal_description">
                <?php echo $description; ?>
            </div>



            <div class="article_comments">
                <fb:comments href="<?php echo $deal_url ?>" width="592" num_posts="10" colorscheme="light" order_by="social"></fb:comments>
            </div>


        </div>



        <!-- Right block -->
        <div class="deal_specifications">
            <?php
            $company_title = $deal->getCompanyName();
            $company_map = $deal->getCompanyMap();
            $company_address = $deal->getCompanyAddress();
            $company_phone = $deal->getCompanyPhone();
            $company_fb_url = $deal->getCompanyFbUrl();
            $company_hours = $deal->getCompanyHours();
            $company_mail = $deal->getCompanyMail();
            $company_url = $deal->getCompanyUrl();
            ?>

            <!--            <div class="deal_buy_block">
            <?php
            if ($deal->allowForBuy())
            {
                ?>
                                            <div class="deal_buy1" style="margin: 10px; 10px">
                                                <form id="deal_buy1" action="<?php echo JRoute::_('index.php?option=com_deals&task=deal.buy'); ?>" method="post">
                                                    <button type="submit">შეძენა</button>
                                                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                                                    <input type="hidden" name="option" value="com_deals" />
                                                    <input type="hidden" name="task" value="deal.buy" />
                <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            </div>
                        
                <?php
                //if (JDEBUG || $_SERVER['REMOTE_ADDR'] == '94.43.206.163') {
                ?>
                                            <div class="deal_buy2" style="margin: 10px; 10px">
                                                <form id="deal_buy2" action="<?php echo JRoute::_('index.php?option=com_deals&task=deal.buy2'); ?>" method="post">
                                                    <div style="position: relative; ">
                                                        <button type="submit"></button>
                                                    </div>
                                                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                                                    <input type="hidden" name="option" value="com_deals" />
                                                    <input type="hidden" name="task" value="deal.buy2" />
                <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            </div>
                        
                        
                                            <div class="deal_buy3" style="margin: 10px; 10px">
                                                <form id="deal_buy3" action="<?php echo JRoute::_('index.php?option=com_deals&task=deal.buy3'); ?>" method="post">
                                                    <div style="position: relative; ">
                                                        <button type="submit"></button>
                                                    </div>
                        
                        
                                                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                                                    <input type="hidden" name="option" value="com_deals" />
                                                    <input type="hidden" name="task" value="deal.buy3" />
                <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            </div>
                <?php
                ?>
                                            <div class="deal_buy4" style="margin: 10px; 10px">
                                                <form id="deal_buy4" action="<?php echo JRoute::_('index.php?option=com_deals&task=deal.buy4'); ?>" method="post">
                                                    <div style="position: relative; ">
                                                        <button type="submit"></button>
                                                    </div>
                        
                                                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                                                    <input type="hidden" name="option" value="com_deals" />
                                                    <input type="hidden" name="task" value="deal.buy4" />
                <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            </div>
                        
                                            <div class="deal_buy5" style="margin: 10px; 10px">
                                                <form id="deal_buy5" action="<?php echo JRoute::_('index.php?option=com_deals&task=deal.buy5'); ?>" method="post">
                                                    <div style="position: relative; ">
                                                        <button type="submit"></button>
                                                    </div>
                        
                                                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                                                    <input type="hidden" name="option" value="com_deals" />
                                                    <input type="hidden" name="task" value="deal.buy5" />
                <?php echo JHtml::_('form.token'); ?>
                                                </form>
                                            </div>
                <?php
                /* $tbc_url = 'http://leavingstone.com/apps/tbc/installment/?brao&name='.rawurlencode($title).'&price='.$price.'';
                  ?>
                  <div class="deal_buy4" style="margin: 10px; 10px">

                  <a class="fancybox" data-fancybox-type="iframe" href="<?php echo $tbc_url?>" id="deal_buy4">CLICK</a>


                  </div>




                  <?php */
                //}
            }
            ?>
                        </div>-->












            <div class="deal_company_title2">
                ინფორმაცია კომპანიაზე
            </div>
            <?php
            if (!empty($company_title))
            {
                ?>
                <div class="deal_company_title">
                    <img src="templates/longcms/images/icons/country_icon.png" alt="ico" />
                    <span>
                        <?php echo $company_title; ?>
                    </span>
                    <div class="cls"></div>
                </div>
                <?php
            }

            if (!empty($company_url))
            {
                $company_url2 = $company_url;
                if (substr($company_url2, 0, 4) !== 'http')
                {
                    $company_url2 = 'http://' . $company_url2;
                }
                ?>
                <div class="deal_company_url">
                    <img src="templates/longcms/images/icons/web_icon.png" alt="ico" />
                    <span>
                        <a href="<?php echo $company_url2; ?>" target="_blank">
                            <?php echo $company_url; ?>
                        </a>
                    </span>
                    <div class="cls"></div>
                </div>
                <?php
            }

            if (!empty($company_address))
            {
                ?>
                <div class="deal_company_address">
                    <img src="templates/longcms/images/icons/info_icon.png" alt="ico" />
                    <span>
                        <?php echo $company_address; ?>
                    </span>
                    <div class="cls"></div>
                </div>
                <?php
            }

            /* if (!empty($company_mail)) {
              ?>
              <div class="deal_company_mail">
              <img src="templates/longcms/images/icons/msg_icon.png" alt="ico" />
              <span>
              <a href="mailto:<?php echo $company_mail; ?>">
              <?php echo $company_mail; ?>
              </a>
              </span>
              <div class="cls"></div>
              </div>
              <?php
              } */

            if (!empty($company_phone))
            {
                ?>
                <div class="deal_company_phone">
                    <img src="templates/longcms/images/icons/phone_icon.png" alt="ico" />
                    <span>
                        <?php echo $company_phone; ?>
                    </span>
                    <div class="cls"></div>
                </div>
                <?php
            }
            if (!empty($company_hours))
            {
                ?>
                <div class="deal_company_hours">
                    <img src="templates/longcms/images/icons/time_icon.png" alt="ico" />
                    <span>
                        <?php echo $company_hours; ?>
                    </span>
                    <div class="cls"></div>
                </div>
                <?php
            }

            if (!empty($company_fb_url))
            {
                ?>
                <div class="deal_company_fb_url">
                    <img src="templates/longcms/images/icons/face_icon.png" target="_blank" alt="ico" />
                    <span>
                        <a href="<?php echo $company_fb_url; ?>">
                            <?php echo $company_fb_url; ?>
                        </a>
                    </span>
                    <div class="cls"></div>
                </div>
                <?php
            }
            ?>
            <div class="deal_company_map">
                <?php
                if (!empty($company_map) && substr($company_map, 0, 23) == 'https://maps.google.com')
                {
                    ?>
                    <iframe width="324" scrolling="no" height="350" frameborder="0" src="<?php echo $company_map; ?>" marginwidth="0" marginheight="0"></iframe>
                    <?php
                }
                ?>
            </div>
            <?php
            if (!empty($this->related))
            {
                echo $this->loadTemplate('related');
            }
            ?>
        </div>
        <div class="cls"></div>



        <?php
        ob_start();
        ?>
        <script>
            $(function () {

                $('.deal_simg').on('click', function () {
                    $this = $(this);
                    var id = $this.data('id');
                    if (id) {
                        $('.deal_bimg').hide();
                        $('.deal_simg').removeClass('active');

                        $('#bimg_' + id).show();
                        $this.addClass('active');

                    }
                });


            });
        </script>
        <?php
        $js = ob_get_clean();
        $this->document->addScriptDeclaration($js);
    } else
    {
        ?>
        <div class="deal_not_found">
            <?php echo JText::_('COM_DEALS_DEAL_NOT_FOUND') ?>
        </div>
        <?php
    }
    ?>


</div>
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
    $heading = $this->params->get('page_heading');
    $post = JRequest::get('post');
    $searchword = !empty($post['searchword']) ? $post['searchword'] : '';

    if ($this->params->get('show_page_heading'))
    {
        if ($searchword)
        {
            $heading = 'ძიების რეზულტატები ფრაზისთვის "' . $searchword . '"';
        }
        ?>
        <div class="page_title">
            <h1>
                <?php echo $this->escape($heading); ?>
            </h1>
        </div>
        <?php
    }
    $pageIds = array();
    $pageIds[] = 1;

    if (!empty($this->items))
    {
        $dealItemid = JMenu::getItemid('com_deals', 'deal');
        $a = 1;
        foreach ($this->items as $deal)
        {
            $pageIds[] = $deal->id;
            $price = $deal->getPrice();
            $old_price = $deal->getOldPrice();
            $title = $deal->getTitle();
            $id = $deal->getId();
            $link = JRoute::_('index.php?option=com_deals&view=deal&id=' . $id . '&Itemid=' . $dealItemid);
            $sold = $deal->getSold();
            $finish = $deal->getFinishDate();



            $monthly = $deal->getMonthly();

            $image = $deal->getImage(1, 'image10');
            if ($a == 1)
            {
                ?>
                <div class="deal_items">
                    <?php
                }
                ?>
                <div class="deal">

                    <div class="deal_in">

                        <div class="deal_title">
                            <div class="deal_title1">
                                <a href="<?php echo $link ?>">
                                    <?php echo $title ?>
                                </a>
                            </div>
                        </div>

                        <div class="deal_in1">
                            <div class="deal_image">
                                <a href="<?php echo $link ?>">
                                    <img src="<?php echo $image ?>" title="<?php echo $title ?>" alt="<?php echo $title ?>" />
                                </a>
                                
                                <div class="deal_new_price">
                                    <span class="itemPrice_<?php echo $deal->id; ?>"><?php echo $price; ?> </span>
                                    <?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', '') ?>
                                </div>






                            </div>
                            <div class="deal_block">
                                <div class="deal_price">
                                    <?php echo JText::_('COM_DEALS_DEALS_PRICE') ?>
                                    <span class="deal_price1">
                                        <?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', '') ?>
                                        <span class="itemPrice_<?php echo $deal->id; ?>"> <?php echo $price; ?></span>
                                    </span>
                                    
                                </div>
                                <div class="deal_sold">
                                    
                                </div>
<!--                                <div class="deal_more">
                                    <?php
                                    if ($deal->isSoldOut())
                                    {
                                        ?>
                                        <span class="deal_sold_out2">
                                            გაყიდულია
                                        </span>
                                        <?php
                                    } else
                                    {
                                        ?>
                                        <a href="<?php echo $link ?>">
                                            <?php //echo JText::_('COM_DEALS_DEALS_MORE') ?>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>-->
<div>
    <div class="deal_time" style="float:left;font-size: 14px;color: green">
                                    <?php echo $finish ?>
                                </div>
    <div class="bid_button" style="float: right;display: block;width: 100px;background-color: orange;text-align: center;padding: 2px;border-radius: 2px">
                                    <?php echo $deal->getBidButton(); ?>
                                </div>
    <div class="cls"></div>
</div> 
<div class="deal_user_<?php echo $deal->id; ?>"><?php echo $deal->getLastBidUser(); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($a == 3)
                {
                    ?>
                    <div class="cls"></div>
                </div>
                <?php
                $a = 1;
            } else
            {
                $a++;
            }
        }



        if ($a > 1 && $a <= 3)
        {
            ?>
            <div class="cls"></div>
        </div>
        <?php
    }
} else
{
    ?>
    <div class="deals_notfound">
        <?php
        if ($searchword)
        {
            echo 'შეთავაზებები ფრაზისთვის "' . $searchword . '" არ მოიძება';
        } else
        {
            echo!empty($this->category) ? 'ამ კატეგორიაში შეთავაზებები არ არის' : 'შეთავაზებები არ არის';
        }
        ?>
    </div>
    <?php
}

?>


</div>


<?php

 

$uri = JFactory::getUri();
$uri->delVar('cat');
$url = $uri->toString();

ob_start();
?>
var currentPageIds = new Array(<?php echo implode(',',$pageIds); ?>);
$(function() {
var url = '<?php echo $url ?>';
$("#cat_filter").chosen();

$('#cat_filter').chosen().change(function(){
$this = $(this);
newUrl = updateURLParameter(url, 'cat', $this.val());
document.location.href = newUrl;
});
});
<?php
$js = ob_get_clean();
$this->document->addScriptDeclaration($js);


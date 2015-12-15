<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="deals">

	<?php
	if ($this->params->get('show_page_heading')) {
		?>
        <div class="page_title">
            <h1>
                <?php echo $this->escape($this->params->get('page_heading')); ?>
            </h1>
        </div>
		<?php
	}


	if (!empty($this->items)) {
		$dealItemid = JMenu::getItemid('com_deals', 'deal');
		$a = 1;
		foreach($this->items as $deal) {
			$price = $deal->getPrice();
			$old_price = $deal->getOldPrice();
			$title = $deal->getTitle();
			$id = $deal->getId();
			$link = JRoute::_('index.php?option=com_deals&view=deal&id='.$id.'&Itemid='.$dealItemid);
			$sold = $deal->getSold();
			$finish = $deal->getFinishDate();

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
						</div>

						<div class="deal_block">
							<div class="deal_price">
								<?php echo JText::_('COM_DEALS_DEALS_PRICE') ?>
								<span class="deal_price1">
									<?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', $price) ?>
								</span>
								<span class="deal_price2">
									<?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', $old_price) ?>
								</span>
							</div>
							<div class="deal_sold">
								<?php echo JText::_('COM_DEALS_DEALS_SOLD') ?>
								<span>
									<?php echo $sold ?>
								</span>
							</div>
							<div class="deal_more">
								<a href="<?php echo $link ?>">
									<?php //echo JText::_('COM_DEALS_DEALS_MORE') ?>
								</a>
							</div>
							<div class="deal_time">
								<?php echo JText::sprintf('COM_DEALS_DEALS_FINISHED', $finish) ?>
							</div>
						</div>

					</div>
				</div>
			</div>
			<?php
			if ($a == 3) {
				?>
	                <div class="cls"></div>
	                </div>
	                <?php
				$a=1;
			} else {
				$a++;
			}
		}
		if ($a>1 && $a<3) {
			?>
	            <div class="cls"></div>
	            </div>
	            <?php
		}
		?>
		<div class="cls"></div>
		<div class="deals_pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
			<div class="cls"></div>
		</div>
		<?php
	} else {
		?>
            <div class="deals_notfound">
            	<?php echo JText::_('COM_DEALS_NO_EXDEALS_FOUND') ?>
            </div>
            <?php
	}
	?>

</div>
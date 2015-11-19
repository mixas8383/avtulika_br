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
<div class="related_deals">


	<div class="related_deals_title">
		<?php echo JText::_('COM_DEALS_RELATED_DEALS'); ?>
	</div>




	<?php
	$dealItemid = JMenu::getItemid('com_deals', 'deal');
	$a = 1;
	foreach($this->related as $deal) {

		$price = $deal->getPrice();
		$old_price = $deal->getOldPrice();
		$title = $deal->getTitle();
		$id = $deal->getId();
		$link = JRoute::_('index.php?option=com_deals&view=deal&id='.$id.'&Itemid='.$dealItemid);
		$sold = $deal->getSold();
		$finish = $deal->getFinishDate();

		$image = $deal->getImage(1, 'image10');


		?>
		<div class="rdeal">

			<div class="rdeal_in">

				<div class="deal_title">
					<div class="deal_title1">
						<a href="<?php echo $link ?>">
							<?php echo $title ?>
						</a>
					</div>
				</div>

				<div class="rdeal_in1">
					<div class="deal_image">
						<a href="<?php echo $link ?>">
							<img src="<?php echo $image ?>" title="<?php echo $title ?>" alt="<?php echo $title ?>" />
						</a>
						<?php
						if ($deal->isInstallment()) {
							?>
							<div class="deal_installment">
								<a href="<?php echo $link ?>"></a>
							</div>
							<?php
						}
						?>
						<div class="deal_new_price">
							<?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', $price) ?>
						</div>
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
							<?php
							if ($deal->isSoldOut()) {
								?>
								<span class="deal_sold_out2">
									გაყიდულია
								</span>
								<?php
							} else {
								?>
								<a href="<?php echo $link ?>">
									<?php //echo JText::_('COM_DEALS_DEALS_MORE') ?>
								</a>
								<?php
							}
							?>
						</div>
						<div class="deal_time">
							<?php echo JText::sprintf('COM_DEALS_DEALS_FINISH', $finish) ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php


	}
	?>


</div>
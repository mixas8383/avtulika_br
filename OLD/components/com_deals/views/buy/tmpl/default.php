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
<div class="deals_buy">

	<?php
	$user = PDeals::getUser();
	$balance = $user->getBalance();
	$id = $user->get('id');



	if ($this->params->get('show_page_heading')) {
		?>
		<div class="page_title">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
		<?php
	}
	?>
	<div class="deals_buydeal">
		<?php
		foreach($this->items as $deal) {
			$image = $deal->getImage(1, 'image12');
			$title = $deal->getTitle();
			$price = $deal->getPrice();

			?>
			<div class="buy_deal">
				<div class="buy_deal_title">
					<?php echo $title ?>
				</div>
				<div class="buy_deal_img">
					<img src="<?php echo $image ?>" />
				</div>
				<div id="buy_deal_price">
					<div class="buy_deal_price">
						<?php echo JText::_('Price') ?>
					</div>
					<div class="buy_deal_price1">
						<?php echo JText::sprintf('COM_DEALS_DEALS_PRICE1', $price) ?>
					</div>
				</div>
			</div>
			<?php
		}
		$deal = $this->items[0];
		?>
	</div>

	<div id="deals_paymethods_preload">
		<img src="/templates/longcms/images/icons/paymethods_preloader.gif" alt="Loading.." title="Loading.." />
	</div>

	<div class="deals_paymethods" id="deals_paymethods" style="display:none;">

		<div class="buy_visa">
			<div class="dealbuy_title">
				<?php echo JText::_('COM_DEALS_BUY_VISA') ?>
			</div>
			<div class="buy_visa_in">
				<div class="deal_buy_infos">
					<div class="deab_buy_card">
						<p><img src="templates/longcms/images/icons/master.png" alt=" "/></p>
						<p><img src="templates/longcms/images/icons/visa.png" alt=" "/></p>
					</div>
				</div>
				<form id="buy_visa" class="buy_brao_form" action="<?php echo JRoute::_('index.php?option=com_deals&task=buy.visa'); ?>" method="post">
					<div class="deal_buy_buttons">
						<button type="submit" class="buyvisa_button"></button>
					</div>
					<div class="deal_buy_button_loader">
						<img src="/templates/longcms/images/icons/buy_loader.gif" alt="Loading.." title="Loading.." style="margin: 11px auto;" />
					</div>
					<input type="hidden" name="option" value="com_deals" />
					<input type="hidden" name="task" value="buy.visa" />
					<?php echo JHtml::_('form.token'); ?>
				</form>
			</div>
		</div>


		<div class="buy_brao">
			<div class="buy_braoin">
				<div class="dealbuy_title">
					<span><?php echo JText::_('COM_DEALS_BUY') ?></span>
					<span><img src="templates/longcms/images/icons/logosmall.png" alt=" "/></span>
					<span><?php echo JText::_('BUY_BRAO') ?></span>
				</div>
				<div class="buy_brao_in">
					<div class="deal_buy_infos">
						<div class="buy_brao_id">
							<span class="buy_brao_user_id">
								<?php echo JText::_('COM_DEALS_BUY_USER_ID') ?>
							</span>
							<span class="buy_brao_user_idnumber">
								<?php echo $id ?>
							</span>
						</div>

						<div class="buy_brao_id1">
							<span class="buy_brao_balance">
								<?php echo JText::_('COM_DEALS_BUY_USER_BALANCE') ?>
							</span>
							<span class="buy_brao_user_balancnumber">
								<?php echo $balance; ?>
							</span>
							<span class="buy_brao_user_balancnumber">
								<?php echo JText::_('GEL') ?>
							</span>
						</div>
					</div>
					<?php

					?>
					<form id="buy_brao" class="buy_brao_form" action="<?php echo JRoute::_('index.php?option=com_deals&task=buy.brao'); ?>" method="post">
						<div class="deal_buy_buttons">
							<button type="submit" class="buybrao_button"></button>
						</div>
						<div class="deal_buy_button_loader">
							<img src="/templates/longcms/images/icons/buy_loader.gif" alt="Loading.." title="Loading.." style="margin: 11px auto;" />
						</div>
						<input type="hidden" name="option" value="com_deals" />
						<input type="hidden" name="task" value="buy.brao" />
						<?php echo JHtml::_('form.token'); ?>
					</form>

				</div>
			</div>
		</div>



		<div class="buy_installment">
			<div class="buy_installmentin">
				<div class="dealbuy_title">
					<?php echo JText::_('COM_DEALS_BUY_INSTALLMENT') ?>
				</div>
				<?php
				if ($deal->isInstallment()) {
				?>
				<div class="buy_brao_installment">
					<div class="deal_buy_infos">
						<div class="buy_brao_installment_in">
							<img src="images/banners/rp1.png" alt=" "/>
						</div>
					</div>
					<form id="buy_brao" class="buy_brao_form" action="<?php echo JRoute::_('index.php?option=com_deals&task=buy.brao'); ?>" method="post">
						<div class="deal_buy_buttons">
							<button type="submit" class="buyinstal_button"></button>
						</div>
						<div class="deal_buy_button_loader">
							<img src="/templates/longcms/images/icons/buy_loader.gif" alt="Loading.." title="Loading.." style="margin: 11px auto;" />
						</div>
						<input type="hidden" name="option" value="com_deals" />
						<input type="hidden" name="task" value="buy.installment" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
					<?php
					} else {
					?>
					<div class="buy_brao_installment_disable">
						<div class="deal_buy_infos">
							<div class="buy_brao_installment_in">
								<img src="images/banners/rp1.png" alt=" "/>
							</div>
						</div>
						<div class="deal_buy_buttons">
							<button type="button" class="buyinstal_button_disable" onclick="alert('<?php echo JText::_('COM_DEALS_BUY_NOT_INSTALLMENT') ?>');return false;"></button>
						</div>
					</div>
					<?php
				}
				?>
				</div>
			</div>
			<div class="cls"></div>
		</div>
	</div>
</div>


<?php


ob_start();
?>
$(function() {
	$('#deals_paymethods_preload').hide();
	$('#deals_paymethods').slideDown();
	$('.buy_brao_form').submit(function() {
		$(this).find('div.deal_buy_buttons').hide();
		$(this).find('div.deal_buy_button_loader').show();
	});
});
<?php
$js = ob_get_clean();
$this->document->addScriptDeclaration($js);


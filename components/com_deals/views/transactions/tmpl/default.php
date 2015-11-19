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

		?>
		<div class="transactions">
			<div class="trans_titles">
				<div class="trans_title_number">
					<?php echo JText::_('COM_DEALS_TRANSACTIONS_TITLENUMBER'); ?>
				</div>
				<div class="trans_title_type">
					<?php echo JText::_('COM_DEALS_TRANSACTIONS_TYPE'); ?>
				</div>
				<div class="trans_title_price">
					<?php echo JText::_('COM_DEALS_TRANSACTIONS_PRICE'); ?>
				</div>
				<div class="trans_title_status">
					<?php echo JText::_('COM_DEALS_TRANSACTIONS_STATUS'); ?>
				</div>
				<div class="trans_title_date">
					<?php echo JText::_('COM_DEALS_TRANSACTIONS_DATE'); ?>
				</div>
                <div class="cls"></div>
			</div>
			<?php
			foreach($this->items as $item) {

				$transaction_number = $item->transaction_number;
				$amount = Balance::convertAsMajor($item->amount);
				$type = Transaction::getTypeName($item->type);
				$type = JText::_('COM_DEALS_TRANSACTIONS_TYPE_'.strtoupper($type));

				$payment_method = Transaction::getPayMethodName($item->payment_method);
				$payment_method = JText::_('COM_DEALS_TRANSACTIONS_PAYMETHOD_'.strtoupper($payment_method));

				$jdate = JFactory::getDate($item->date);
				$jdate->setTimezone('Asia/Tbilisi');
				$date = $jdate->format('Y-m-d H:i', true);
				$status = $item->status;

				if ($status == 1) {
					$status_img = 'pending.png';
					$status_title = JText::_('COM_DEALS_TRANSACTIONS_STATUS_PENDING');
				} else if ($status == 2) {
					$status_img = 'success.png';
					$status_title = JText::_('COM_DEALS_TRANSACTIONS_STATUS_SUCCESS');
				} else {
					$status_img = 'failed.png';
					$status_title = JText::_('COM_DEALS_TRANSACTIONS_STATUS_FAILED');
				}

				$href = JRoute::_('index.php?option=com_deals&view=transactions&layout=transaction&tid='.$item->id.'&tmpl=component');
				?>
				<div class="transaction">
					<div class="transaction_number">
						<a href="<?php echo $href ?>" rel="lightbox[<?php echo $item->id ?>]">
							<?php echo $transaction_number ?>
						</a>
					</div>
					<div class="transaction_type">
						<?php echo $type ?>
					</div>
					<div class="transaction_amount">
						<?php echo $amount.' '.JText::_('GEL') ?>
					</div>
					<div class="transaction_status">
						<img src="templates/longcms/images/icons/<?php echo $status_img ?>" alt="<?php echo $status_title ?>" title="<?php echo $status_title ?>" class="hasTip" />
					</div>
					<div class="transaction_date">
						<?php echo $date ?>
					</div>
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
		</div>
		<?php
		ob_start();
		?>
		$(function() {
			$("a[rel*=lightbox]").fancybox({
				type: 'iframe',
				padding: 0,
				width: 800
			});
		});
		<?php
		$js = ob_get_clean();
		$this->document->addScriptDeclaration($js);
	} else {
		?>
            <div class="deals_notfound">
            	<?php echo JText::_('COM_DEALS_NO_TRANSACTIONS_FOUND') ?>
            </div>
            <?php
	}
	?>


</div>
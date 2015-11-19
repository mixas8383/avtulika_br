<?php
/**
 * @package     	LongCMS.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user			= JFactory::getUser();
$userId		= $user->get('id');
$listOrder		= $this->escape($this->state->get('list.ordering'));
$listDirn		= $this->escape($this->state->get('list.direction'));
$canOrder		= $user->authorise('core.edit.state', JCOMPONENT.'.category');
$saveOrder	= $listOrder=='ordering';
$params		= (isset($this->state->params)) ? $this->state->params : new JObject();

$array = array(''=>JText::_('COM_DEALS_SELECT_STATUS'), '-1'=>'Declined', '1'=>'Pending', '2'=>'Success');
$type_array = array(''=>JText::_('Type Filter'), '1'=>'Buy', '2'=>'Deposit');
$paymethod_array = array(''=>JText::_('Payment Method Filter'), '1'=>'Visa', '2'=>'Brao.ge', '4'=>'Nova');

?>
<form action="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&view='.JVIEW); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<input type="text" name="filter_search" class="filter_reset" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_DEALS_SEARCH_IN_TITLE'); ?>" placeholder="Search" style="width: 200px;" />
			<input type="text" name="filter_pid" class="filter_reset" id="filter_pid" value="<?php echo $this->escape($this->state->get('filter.pid')); ?>" title="Product ID Filter" placeholder="Product ID Filter" />
			<input type="text" name="filter_uid" class="filter_reset" id="filter_uid" value="<?php echo $this->escape($this->state->get('filter.uid')); ?>" title="User ID Filter" placeholder="User ID Filter" />
				<span>
					<?php echo JHtml::_('calendar', $this->state->get('filter.datefrom'), 'filter_datefrom', 'filter_datefrom', '%Y-%m-%d', array('class'=>'filter_reset', 'placeholder'=>'Date From'));?>
				</span>
				<span>
					<?php echo JHtml::_('calendar', $this->state->get('filter.datetill'), 'filter_datetill', 'filter_datetill', '%Y-%m-%d', array('class'=>'filter_reset', 'placeholder'=>'Date To'));?>
				</span>
				<span>
					<select name="filter_state" class="inputbox filter_reset">
						<?php echo JHtml::_('select.options', $array, 'value', 'text', $this->state->get('filter.state'), true);?>
					</select>
				</span>
				<span>
					<select name="filter_type" class="inputbox filter_reset">
						<?php echo JHtml::_('select.options', $type_array, 'value', 'text', $this->state->get('filter.type'), true);?>
					</select>
				</span>
				<span>
					<select name="filter_paymethod" class="inputbox filter_reset">
						<?php echo JHtml::_('select.options', $paymethod_array, 'value', 'text', $this->state->get('filter.paymethod'), true);?>
					</select>
				</span>


			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$$('.filter_reset').set('value', '');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="clr"></div>
		<div class="deals_total" style="float: right; font-size: 18px; font-weight: bold;">
			<span>
				Total:
			</span>
			<span>
				<?php echo Balance::convertAsMajor($this->totalAmount).' GEL'; ?>
			</span>
		</div>



	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<div align="left">
						<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_TRANSID', 't.transaction_number', $listDirn, $listOrder); ?>
					</div>
				</th>
				<th>
					<div align="left">
						<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_USER', 'user_name', $listDirn, $listOrder); ?>
					</div>
				</th>
				<th class="center">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_AMOUNT', 'd.amount', $listDirn, $listOrder); ?>
				</th>
				<th class="center">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_TYPE', 'd.type', $listDirn, $listOrder); ?>
				</th>
				<th class="center" width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_PAYMETHOD', 'd.payment_method', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'd.status', $listDirn, $listOrder); ?>
				</th>

				<th class="center" width="10%">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_DATE', 'd.date', $listDirn, $listOrder); ?>
				</th>

				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_ID', 'd.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php

		$multipic = JFactory::getMultipic();
		$site_url = JURI::root();
		foreach ($this->items as $i => $item) :
			$canEdit	= $user->authorise('core.edit', JCOMPONENT.'.transaction.'.$item->id);
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state', JCOMPONENT.'.transaction.'.$item->id) && $canCheckin;

			$transaction_number = $this->escape($item->transaction_number);
			$user_name = $this->escape($item->user_name);
			$user_mail = $this->escape($item->user_mail);
			$user_id = $this->escape($item->user_id);
			$user_persNumber = $this->escape($item->user_persNumber);
			$user_mobile = $this->escape($item->user_mobile);
			$user_phone = $this->escape($item->user_phone);
			$deal_title = $this->escape($item->deal_title);
			$deal_image = $multipic->getImage('image12', $item->deal_image);
			$deal_price = Balance::convertAsMajor($item->deal_price);
			$deal_id = $this->escape($item->deal_id);

			$user_tip = '';
			$user_tip .= '<b>ID:</b> '.$user_id.'<br />';
			$user_tip .= '<b>E-mail:</b> '.$user_mail.'<br />';
			$user_tip .= '<b>P/N:</b> '.$user_persNumber.'<br />';
			if ($user_mobile) {
				$user_tip .= '<b>Mobile:</b> '.$user_mobile.'<br />';
			}
			if ($user_phone) {
				$user_tip .= '<b>Phone:</b> '.$user_phone.'<br />';
			}


			$trans_tip = '';
			$trans_tip .= '<img src="'.$site_url.$deal_image.'" title="'.$deal_title.'" alt="'.$deal_title.'" align="left" />';
			$trans_tip .= '<div style="margin-left:120px;">';
			$trans_tip .= $deal_title.'<br />';
			$trans_tip .= JText::_('COM_DEALS_HEADING_PRICE').': '.$deal_price.' '.JText::_('GEL');
			$trans_tip .= '<br />';
			$trans_tip .= JText::_('COM_DEALS_HEADING_ID').': '.$deal_id.'<br />';

			$trans_tip .= '</div>';




			$amount = Balance::convertAsMajor($item->amount);

			$type = Transaction::getTypeName($item->type);
			$type = JText::_('COM_DEALS_TRANSACTIONS_TYPE_'.strtoupper($type));

			$payment_method = Transaction::getPayMethodName($item->payment_method);
			$payment_method = JText::_('COM_DEALS_TRANSACTIONS_PAYMETHOD_'.strtoupper($payment_method));

			$status = $item->status;


			$jdate = JFactory::getDate($item->date);
			$jdate->setTimeZone('Asia/Tbilisi');
			$date = $jdate->format(null, true);

			$extra_data = json_decode($item->extra_data, true);

  			$extra= '';
  			if (is_array($extra_data)) {
				$extra .= '<br /><br />';
				$extra .= '<b>Extra Information</b><br />';
				foreach($extra_data as $k=>$v) {
					$extra .= '<u>'.$k.'</u>: '.$v.'<br />';
				}
  			}


			$states = array(
				0=>array('', JText::_('COM_DEALS_TRANSACTIONS_DECLINED'), JText::_('COM_DEALS_TRANSACTIONS_DECLINED').'::'.JText::_('COM_DEALS_TRANSACTIONS_DECLINED_DESC'), JText::_('COM_DEALS_TRANSACTIONS_DECLINED').'::'.JText::_('COM_DEALS_TRANSACTIONS_DECLINED_DESC').$extra, true, 'unpublish', 'unpublish'),
				1=>array('', JText::_('COM_DEALS_TRANSACTIONS_PENDING'), JText::_('COM_DEALS_TRANSACTIONS_PENDING').'::'.JText::_('COM_DEALS_TRANSACTIONS_PENDING_DESC'), JText::_('COM_DEALS_TRANSACTIONS_PENDING').'::'.JText::_('COM_DEALS_TRANSACTIONS_PENDING_DESC').$extra, true, 'pending', 'pending'),
				2=>array('', JText::_('COM_DEALS_TRANSACTIONS_SUCCESS'), JText::_('COM_DEALS_TRANSACTIONS_SUCCESS').'::'.JText::_('COM_DEALS_TRANSACTIONS_SUCCESS_DESC'), JText::_('COM_DEALS_TRANSACTIONS_SUCCESS').'::'.JText::_('COM_DEALS_TRANSACTIONS_SUCCESS_DESC').$extra, true, 'publish', 'publish'),
			);

			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php echo JHTML::tooltip($trans_tip, '', '', $transaction_number); ?>
				</td>

				<td>
					<?php echo JHTML::tooltip($user_tip, $user_name, '', $user_name); ?>
				</td>

				<td class="center">
					<?php echo $amount.' '.JText::_('GEL'); ?>
				</td>

				<td class="center">
					<?php echo $type; ?>
				</td>
				<td class="center">
					<?php echo $payment_method; ?>
				</td>

				<td class="center">
					<?php echo JHtml::_('jgrid.state', $states, $status, $i, 'categories.', false, false); ?>
				</td>


				<td class="center">
					<?php echo $date; ?>
				</td>



				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>


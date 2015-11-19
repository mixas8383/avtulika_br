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

$array = array(''=>JText::_('COM_DEALS_SELECT_STATUS'), 'REVIEW'=>'REVIEW', 'DISCARDED'=>'DISCARDED', 'APPROVED'=>'APPROVED');

?>
<form action="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&view='.JVIEW); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<input type="text" name="filter_search" class="filter_reset" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_DEALS_SEARCH_IN_TITLE'); ?>" placeholder="Search" style="width: 200px;" />
			<input type="text" name="filter_pid" class="filter_reset" id="filter_pid" value="<?php echo $this->escape($this->state->get('filter.pid')); ?>" title="Product ID Filter" placeholder="Product ID Filter" />
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


			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$$('.filter_reset').set('value', '');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="clr"></div>


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
				<th class="center">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_AMOUNT', 'd.amount', $listDirn, $listOrder); ?>
				</th>

				<th class="center">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_FULLNAME', 'd.fullname', $listDirn, $listOrder); ?>
				</th>
				<th class="center">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_PHONE', 'd.shipping_phone', $listDirn, $listOrder); ?>
				</th>
				<th class="center">
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_TRANSACTIONS_HEADING_ADDRESS', 'd.shipping_address', $listDirn, $listOrder); ?>
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
			$canEdit	= $user->authorise('core.edit', JCOMPONENT.'.liberty.'.$item->id);
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state', JCOMPONENT.'.liberty.'.$item->id) && $canCheckin;

			$transaction_number = $this->escape($item->ordercode);

			$deal_title = $this->escape($item->deal_title);
			$deal_image = $multipic->getImage('image12', $item->deal_image);
			$deal_price = Balance::convertAsMajor($item->deal_price);
			$deal_id = $this->escape($item->deal_id);



			$trans_tip = '';
			$trans_tip .= '<img src="'.$site_url.$deal_image.'" title="'.$deal_title.'" alt="'.$deal_title.'" align="left" />';
			$trans_tip .= '<div style="margin-left:120px;">';
			$trans_tip .= $deal_title.'<br />';
			$trans_tip .= JText::_('COM_DEALS_HEADING_PRICE').': '.$deal_price.' '.JText::_('GEL');
			$trans_tip .= '<br />';
			$trans_tip .= JText::_('COM_DEALS_HEADING_ID').': '.$deal_id.'<br />';

			$trans_tip .= '</div>';


			$amount = Balance::convertAsMajor($item->amount);

			$status = $item->status;


			$jdate = JFactory::getDate($item->date);
			$jdate->setTimeZone('Asia/Tbilisi');
			$date = $jdate->format(null, true);



			$firstname = $this->escape($item->shipping_firstname);
			$lastname = $this->escape($item->shipping_lastname);
			$phone = $this->escape($item->shipping_phone);
			$address = $this->escape($item->shipping_address);

			$fullname = $firstname.' '.$lastname;

			if ($status == '0') {
				$status = 'SENT';
			}

			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php echo JHTML::tooltip($trans_tip, '', '', $transaction_number); ?>
				</td>

				<td class="center">
					<?php echo $amount.' '.JText::_('GEL'); ?>
				</td>


				<td class="center">
					<?php echo $fullname; ?>
				</td>

				<td class="center">
					<?php echo $phone; ?>
				</td>

				<td class="center">
					<?php echo $address; ?>
				</td>




				<td class="center">
					<?php echo $status; ?>
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


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
$canOrder		= $user->authorise('core.edit.state', JCOMPONENT.'.deal');
$saveOrder	= $listOrder=='a.ordering';
$params		= (isset($this->state->params)) ? $this->state->params : new JObject();

$market_array = array(''=>'Market Filter', '1'=>'Is Market', '2'=>'Is Deal');

?>
<style type="text/css">
	input,select {
		height: 15px;
	}

</style>

<form action="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&view=deals'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" class="filter_reset" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_DEALS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$$('.filter_reset').set('value', '');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

			<button type="button" id="dates_btn"><?php echo JText::_('COM_DEALS_RELAUNCHBTN'); ?></button>

		</div>
		<div class="filter-select fltrt">

			<select name="filter_market" class="inputbox filter_reset" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', $market_array, 'value', 'text', $this->state->get('filter.market', ''), true);?>

			</select>

			<select name="filter_state" class="inputbox filter_reset" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_DEALS_SELECT_STATUS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>

			</select>

			<select name="filter_category_id" class="inputbox filter_reset" onchange="this.form.submit()">
				<option value="0"><?php echo JText::_('COM_DEALS_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', DealsHelper::getCategoryOptions(), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>

			<select name="filter_company_id" class="inputbox filter_reset" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_DEALS_SELECT_COMPANY');?></option>
				<?php echo JHtml::_('select.options', DealsHelper::getCompanyOptions(), 'value', 'text', $this->state->get('filter.company_id'));?>
			</select>

			<select name="filter_city_id" class="inputbox filter_reset" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_DEALS_SELECT_CITY');?></option>
				<?php echo JHtml::_('select.options', DealsHelper::getCityOptions(), 'value', 'text', $this->state->get('filter.city_id'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<fieldset id="filter-bar2" class="hide">
		<?php echo JHTML::_('calendar', '', 'datetill', 'datetill', '%Y-%m-%d 23:59:59', array('class'=>'inputbox', 'size'=>'22',  'maxlength'=>'19', 'style'=>'vertical-align:baseline;')); ?>
		<button type="button" onclick="if ($('datetill').get('value') == ''){alert('Please select date');} else if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('deals.relaunch')}"><?php echo JText::_('COM_DEALS_RELAUNCH'); ?></button>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_DEALS_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="2%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_MARKET', 'a.is_market', $listDirn, $listOrder); ?>
				</th>
				<th width="2%">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_CATEGORY', 'a.category_id', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_COMPANY', 'a.company_id', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_CITY', 'a.city_id', $listDirn, $listOrder); ?>
				</th>

				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_PRICE', 'a.price', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_COMISSION', 'a.comission', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'deals.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_SOLD', 'a.sold', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		foreach ($this->items as $i => $item) :
			$canEdit	= $user->authorise('core.edit', JCOMPONENT.'.deal.'.$item->id);
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state', JCOMPONENT.'.deal.'.$item->id) && $canCheckin;
			$ordering	= ($listOrder == 'a.ordering');
			$title = $this->escape($item->title);
			$text = $this->escape($item->text);
			$market = $item->is_market ? '<img alt="Market" src="/apanel/templates/bluestork/images/admin/featured.png" />' : '<img alt="Deal" src="/apanel/templates/bluestork/images/admin/disabled.png" />';
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, JVIEW.'.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&task=deal.edit&id='.(int) $item->id); ?>">
							<?php echo JHTML::tooltip($text, $title, '', $title); ?>
						</a>
					<?php else : ?>
							<?php echo JHTML::tooltip($text, $title, '', $title); ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $market ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'deals.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title) ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->company_title);?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->city_title); ?>
				</td>
				<td class="center">
					<?php echo Balance::convertAsMajor($item->price).' '.JText::_('GEL');?>
				</td>
				<td class="center">
					<?php echo Balance::convertAsMajor($item->comission).' '.JText::_('GEL');?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'deals.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'deals.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'deals.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'deals.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $item->hits; ?>
				</td>
				<td class="center">
					<?php echo $item->sold; ?>
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


<script type="text/javascript">
window.addEvent('domready', function() {
    $('dates_btn').addEvent('click', function(e) {
        $('filter-bar2').toggleClass('hide');
    });
});
</script>

<?php

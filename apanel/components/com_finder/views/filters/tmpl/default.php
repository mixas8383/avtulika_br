<?php
/**
 * @package     	LongCMS.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

JText::script('COM_FINDER_INDEX_CONFIRM_DELETE_PROMPT');
?>

<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton == 'filters.delete') {
		if (confirm(Joomla.JText._('COM_FINDER_INDEX_CONFIRM_DELETE_PROMPT'))) {
			Joomla.submitform(pressbutton);
		} else {
			return false;
		}
	}
	Joomla.submitform(pressbutton);
}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_finder&view=filters');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::sprintf('COM_FINDER_SEARCH_LABEL', JText::_('COM_FINDER_FILTERS')); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_FINDER_FILTER_SEARCH_DESCRIPTION'); ?>" />
			<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_FINDER_INDEX_FILTER_BY_STATE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('finder.statelist'), 'value', 'text', $this->state->get('filter.state'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'a.created_by_alias', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_FINDER_FILTER_TIMESTAMP', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_FINDER_FILTER_MAP_COUNT', 'a.map_count', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.filter_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->items) == 0): ?>
			<tr class="row0">
				<td class="center" colspan="7">
					<?php
					if ($this->total == 0):
						echo JText::_('COM_FINDER_NO_FILTERS');
						?>
						<a href="<?php echo JRoute::_('index.php?option=com_finder&task=filter.add'); ?>" title="<?php echo JText::_('COM_FINDER_CREATE_FILTER'); ?>">
							<?php echo JText::_('COM_FINDER_CREATE_FILTER'); ?>
						</a>
						<?php
					else:
						echo JText::_('COM_FINDER_NO_RESULTS');
					endif;
					?>
				</td>
			</tr>
			<?php endif; ?>

			<?php foreach ($this->items as $i => $item):
			$canCreate	= $user->authorise('core.create',		'com_finder');
			$canEdit	= $user->authorise('core.edit',			'com_finder');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_finder') && $canCheckin;
			?>

			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->filter_id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) {
						echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'filters.', $canCheckin);
					} ?>
					<?php if ($canEdit) { ?>
						<a href="<?php echo JRoute::_('index.php?option=com_finder&task=filter.edit&filter_id=' . (int) $item->filter_id); ?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php } else {
							echo $this->escape($item->title);
					} ?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'filters.', $canChange); ?>
				</td>
				<td class="center nowrap">
					<?php echo $item->created_by_alias ? $item->created_by_alias : $item->user_name; ?>
				</td>
				<td class="center nowrap">
					<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center nowrap">
					<?php echo $item->map_count; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->filter_id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="7" class="nowrap">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.direction'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

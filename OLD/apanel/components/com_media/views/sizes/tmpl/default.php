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
$canOrder		= $user->authorise('core.edit.state', JCOMPONENT.'.size');
$saveOrder	= $listOrder=='ordering';
$params		= (isset($this->state->params)) ? $this->state->params : new JObject();

?>
<form action="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&view=sizes'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" class="filter_reset" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_MEDIA_SIZES_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$$('.filter_reset').set('value', '');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
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
					<?php echo JHtml::_('grid.sort',  'COM_MEDIA_SIZES_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>

				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_CODENAME', 'a.codename', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_WIDTH', 'a.width', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_HEIGHT', 'a.height', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_IMGEDIT', 'a.imgedit', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_QUALITY', 'a.quality', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_WATERMARK', 'a.watermark', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_INEDITOR', 'a.ineditor', $listDirn, $listOrder); ?>
				</th>

				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_MEDIA_SIZES_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		foreach ($this->items as $i => $item) :
			$canEdit	= $user->authorise('core.edit', JCOMPONENT.'.size.'.$item->id);
			$canCheckin	= $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state', JCOMPONENT.'.size.'.$item->id) && $canCheckin;

			$title = $this->escape($item->title);
			$edit_type = isset($this->edit_types[$item->imgedit]) ? $this->edit_types[$item->imgedit] : 'Unknown';
			$width = !empty($item->width) ? $item->width : '*';
			$height = !empty($item->height) ? $item->height : '*';

			$watermark = 'None';
			if ($item->watermark == 0) {
				$watermark = JText::_('JOFF');
			} else if ($item->watermark == 1) {
				$watermark = JText::_('JON');
			} else if ($item->watermark == -1) {
				$watermark = JText::_('JGLOBAL_USE_GLOBAL');
			}

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
						<a href="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&task=size.edit&id='.(int) $item->id); ?>">
							<?php echo JHTML::tooltip($title, $title, '', $title); ?>
						</a>
					<?php else : ?>
							<?php echo JHTML::tooltip($title, $title, '', $title); ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $item->codename ?>
				</td>
				<td class="center">
					<?php echo $width ?>
				</td>
				<td class="center">
					<?php echo $height ?>
				</td>
				<td class="center">
					<?php echo $edit_type ?>
				</td>
				<td class="center">
					<?php echo $item->quality ?>
				</td>
				<td class="center">
					<?php echo $watermark ?>
				</td>
				<td class="center">
					<?php echo $item->ineditor ?>
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
<?php

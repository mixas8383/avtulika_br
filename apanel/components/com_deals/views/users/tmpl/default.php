<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.modal');

$canDo = UsersHelper::getActions();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$loggeduser = JFactory::getUser();
?>
<script type="text/javascript">
Joomla.submitbutton = function submitbutton(pressbutton) {
	if (pressbutton == 'users.delete') {
		if (!confirm('Are you sure?')) {
			return false;
		}
	}
	// Some conditions
	document.adminForm.task.value=pressbutton;
	// More conditions
	submitform(pressbutton);
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_deals&view=users');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<input type="text" name="filter_search" id="filter_search" class="filter_reset" placeholder="Search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_USERS_SEARCH_USERS'); ?>" />
			<input type="text" name="filter_persnum" id="filter_persnum" class="filter_reset" placeholder="Personal Number Filter" value="<?php echo $this->escape($this->state->get('filter.persnum')); ?>" title="Personal Number Filter" />
			<span>
				<?php echo JHtml::_('calendar', $this->state->get('filter.datefrom'), 'filter_datefrom', 'filter_datefrom', '%Y-%m-%d', array('class'=>'filter_reset', 'placeholder'=>'Reg Date From'));?>
			</span>
			<span>
				<?php echo JHtml::_('calendar', $this->state->get('filter.datetill'), 'filter_datetill', 'filter_datetill', '%Y-%m-%d', array('class'=>'filter_reset', 'placeholder'=>'Reg Date To'));?>
			</span>


			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$$('.filter_reset').set('value', '');this.form.submit();"><?php echo JText::_('JSEARCH_RESET'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<label for="filter_state">
				<?php echo JText::_('COM_USERS_FILTER_LABEL'); ?>
			</label>

			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo JText::_('COM_USERS_FILTER_STATE');?></option>
				<?php echo JHtml::_('select.options', UsersHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>

			<select name="filter_active" class="inputbox" onchange="this.form.submit()">
				<option value="*"><?php echo JText::_('COM_USERS_FILTER_ACTIVE');?></option>
				<?php echo JHtml::_('select.options', UsersHelper::getActiveOptions(), 'value', 'text', $this->state->get('filter.active'));?>
			</select>

			<select name="filter_group_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_USERS_FILTER_USERGROUP');?></option>
				<?php echo JHtml::_('select.options', UsersHelper::getGroups(), 'value', 'text', $this->state->get('filter.group_id'));?>
			</select>

			<select name="filter_range" id="filter_range" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_USERS_OPTION_FILTER_DATE');?></option>
				<?php echo JHtml::_('select.options', Usershelper::getRangeOptions(), 'value', 'text', $this->state->get('filter.range'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_ENABLED', 'a.block', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_ACTIVATED', 'a.activation', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_SUBSCRIBED', 'a.mail_subscribed', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="5%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_PHONE', 'a.phone', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_EMAIL', 'a.email', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="5%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_PERS_NUMBER', 'a.persNumber', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_DEALS_BALANCE', 'ab.balance', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_LAST_VISIT_DATE', 'a.lastvisitDate', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_USERS_HEADING_REGISTRATION_DATE', 'a.registerDate', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="3%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canEdit	= $canDo->get('core.edit');
			$canChange	= $loggeduser->authorise('core.edit.state',	'com_users');
			// If this group is super admin and this user is not super admin, $canEdit is false
			if ((!$loggeduser->authorise('core.admin')) && JAccess::check($item->id, 'core.admin')) {
				$canEdit	= false;
				$canChange	= false;
			}
			$name = $item->name.' '.$item->surname;
			$balance = Balance::convertAsMajor($item->balance).' '.JText::_('GEL');

			$phones = array();
			if ($item->phone) {
				$phones[] = $item->phone;
			}
			if ($item->mobile) {
				$phones[] = $item->mobile;
			}
			$phone = implode(', ', $phones);

			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php if ($canEdit) : ?>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_deals&task=user.edit&id='.(int) $item->id); ?>" title="<?php echo JText::sprintf('COM_USERS_EDIT_USER', $this->escape($item->name)); ?>">
						<?php echo $this->escape($name); ?></a>
					<?php else : ?>
						<?php echo $this->escape($name); ?>
					<?php endif; ?>
				</td>

				<td class="center">
					<?php if ($canChange) : ?>
						<?php if ($loggeduser->id != $item->id) : ?>
							<?php echo JHtml::_('grid.boolean', $i, !$item->block, 'users.unblock', 'users.block'); ?>
						<?php else : ?>
							<?php echo JHtml::_('grid.boolean', $i, !$item->block, 'users.block', null); ?>
						<?php endif; ?>
					<?php else : ?>
						<?php echo JText::_($item->block ? 'JNO' : 'JYES'); ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('grid.boolean', $i, !$item->activation, 'users.activate', null); ?>
				</td>

				<td class="center">
					<?php if ($canChange) : ?>
						<?php echo JHtml::_('grid.boolean', $i, $item->mail_subscribed, 'users.subscribe', 'users.unsubscribe'); ?>
					<?php else : ?>
						<?php echo JText::_(!$item->mail_subscribed ? 'JNO' : 'JYES'); ?>
					<?php endif; ?>
				</td>


				<td class="center">
					<?php echo $this->escape($phone); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->email); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->persNumber); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($balance); ?>
				</td>
				<td class="center">
					<?php if ($item->lastvisitDate!='0000-00-00 00:00:00'):?>
						<?php echo JHtml::_('date', $item->lastvisitDate, 'Y-m-d H:i:s'); ?>
					<?php else:?>
						<?php echo JText::_('JNEVER'); ?>
					<?php endif;?>
				</td>
				<td class="center">
					<?php echo JHtml::_('date', $item->registerDate, 'Y-m-d H:i:s'); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
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

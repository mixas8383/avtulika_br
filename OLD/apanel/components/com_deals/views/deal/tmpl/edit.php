<?php
/**
 * @package	LongCMS.Administrator
 * @subpackage	com_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'deal.cancel' || document.formvalidator.isValid(document.id('deal-form'))) {
			<?php echo $this->form->getField('text')->save(); ?>
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('deal-form'));
		}
	}

</script>

<form action="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="deal-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_DEALS_NEW_DEAL') : JText::sprintf('COM_DEALS_DEAL_DETAILS', $this->item->id); ?></legend>

			<div class="clr"></div>
			<?php echo $this->form->getLabel('title'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('title'); ?>


			<div class="clr"></div>
			<?php echo $this->form->getLabel('text'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('text'); ?>
			<div class="clr"></div>


			<?php echo $this->form->getLabel('description'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description'); ?>
			<div class="clr"></div>


		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_DEALS_DETAILS_OTHER'); ?></legend>

			<ul class="adminformlist">

				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>

				<li><?php echo $this->form->getLabel('category_id'); ?>
				<?php echo $this->form->getInput('category_id'); ?></li>

				<li><?php echo $this->form->getLabel('company_id'); ?>
				<?php echo $this->form->getInput('company_id'); ?></li>

				<li><?php echo $this->form->getLabel('city_id'); ?>
				<?php echo $this->form->getInput('city_id'); ?></li>
			</ul>
			<div class="clr"> </div>

		</fieldset>
	</div>

	<div class="width-40 fltrt">
		<fieldset class="panelform">
			<legend><?php echo JText::_('COM_DEALS_OPTIONS'); ?></legend>

			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('publish') as $field): ?>
					<li><?php echo $field->label; ?>
						<?php echo $field->input; ?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>

		<fieldset class="panelform">
			<legend><?php echo JText::_('COM_DEALS_IMAGES'); ?></legend>

			<ul class="adminformlist">
				<?php foreach($this->form->getFieldset('images') as $field): ?>
					<li><?php echo $field->label; ?>
						<?php echo $field->input; ?></li>
				<?php endforeach; ?>
			</ul>
		</fieldset>


		<?php echo JHtml::_('sliders.start', 'deals-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'metadata'); ?>
			<fieldset class="panelform">
				<ul class="adminformlist">
					<?php foreach($this->form->getFieldset('metadata') as $field): ?>
						<li><?php echo $field->label; ?>
							<?php echo $field->input; ?></li>
					<?php endforeach; ?>
				</ul>
			</fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

<div class="clr"></div>
</form>

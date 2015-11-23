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
		if (task == 'city.cancel' || document.formvalidator.isValid(document.id('deal-form'))) {
			Joomla.submitform(task, document.getElementById('deal-form'));
		}
	}

</script>

<form action="<?php echo JRoute::_('index.php?option='.JCOMPONENT.'&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="deal-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_DEALS_NEW_CITY') : JText::sprintf('COM_DEALS_DEAL_DETAILS', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?></li>
				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published'); ?></li>
			</ul>

		</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>


	<div class="clr"></div>
</form>

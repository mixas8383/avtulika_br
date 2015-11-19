<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;


JHtml::_('behavior.keepalive');
//JHtml::_('behavior.formvalidation');
JHtml::_('behavior.noframes');

?>
<div class="deals">
<div class="installment_body">
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
	?>

<?php
if ($this->params->get('installment_description')) {
	?>
	<div class="installment_body">
		<?php echo $this->params->get('installment_description'); ?>
	</div>
	<?php
}
?>

<form id="installment-profile" action="<?php echo JRoute::_('index.php?option=com_deals&task=installment.submit'); ?>" method="post" class="form-validate">
	<div class="installment_block">
<?php
 foreach ($this->form->getFieldset('installment') as $field):?>
			<?php if ($field->hidden):?>
				<?php echo $field->input;?>
			<?php else:?>
			<div class="installment_items">
				<div class="installment_label">
					<?php echo $field->label; ?>
				</div>
				<div class="installment_input">
					<?php echo $field->input; ?>
				</div>
                <div class="cls"></div>
			</div>
			<?php endif;?>
		<?php endforeach;?>

		<div class="installment_button_save">
		<button type="submit" class="validate send_button"></button>
		<input type="hidden" name="option" value="com_deals" />
		<input type="hidden" name="task" value="installment.submit" />
		<?php echo JHtml::_('form.token'); ?>
		</div>
        </div>
	</form>
</div>
</div>
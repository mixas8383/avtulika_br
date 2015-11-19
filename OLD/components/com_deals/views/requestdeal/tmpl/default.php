<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
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
	if (!empty($this->requestdeal_text)) {
		?>
		<div class="requestdeal_text">
			<?php echo $this->requestdeal_text; ?>
		</div>
		<?php
	}
	?>
<div class="contact-form">
	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
		<fieldset>
		<legend class="contact_formtitle"><?php echo JText::_('COM_DEALS_REQUESTDEAL_FORM_LABEL'); ?></legend>

			<?php
			foreach($this->form->getFieldset('requestdeals') as $field):
			?>
			<div class="rd_labelinput">
				<div class="rd_label">
					<?php echo $field->label; ?>
				</div>
				<div class="rd_input">
					<?php echo $field->input; ?>
				</div>
				<div class="cls"></div>
			</div>
			<?php
			endforeach;
			?>

		<div class="deal_buttons">
			<div class="deal_check">
				<button class="send_button validate" type="submit"><?php //echo JText::_('COM_DEALS_REQUESTDEAL_FORM_SEND'); ?></button>
			</div>
			<div class="cls"></div>
		</div>
		<input type="hidden" name="option" value="com_deals" />
		<input type="hidden" name="task" value="requestdeal.submit" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</fieldset>
	</form>
</div>


</div>
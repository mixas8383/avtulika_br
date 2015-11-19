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
JHtml::_('behavior.noframes');
?>
<div class="module_block">
<div class="login_body<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="login_title">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
    </div>
<?php endif; ?>
	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate">
    <div class="regist_body" style="width: 300px">
<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($fieldset->name);?>
	<?php if (count($fields)):?>
		<fieldset>

			<div class="asteriks_warn" style="padding: 10px 0;">
				ყველა ვარსკვლავით (*) აღნიშნული ველის შევსება სავალდებულოა
			</div>
			<div class="mail_warn" style="padding: 10px 0; color: red;">
				გთხოვთ შეიყვანოთ აქტიური ელ. ფოსტა რომელზეც გამოგეგზავნებათ აქტივაციის ბმული და მონაცემები brao.ge საიტით სარგებლობისათვის
			</div>

			<div class="regist_block">
				<?php foreach($fields as $field):// Iterate through the fields in the set and display them.?>
                    <?php if ($field->hidden):// If the field is hidden, just display the input.?>
                        <?php echo $field->input;?>
                    <?php else:?>
                    <div class="regist_item">
                        <div class="regist_label">
                            <?php echo $field->label; ?>
                            <?php if (!$field->required && $field->type!='Spacer'): ?>
                                <span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="regist_input">
							<?php echo ($field->type!='Spacer') ? $field->input : "&#160;"; ?>
                        </div>
                    </div>
                    <?php endif;?>
                <?php endforeach;?>
			</div>
		</fieldset>
	<?php endif;?>
<?php endforeach;?>
		<div class="regist_but">
			<button type="submit" class="regist_button validate"><?php //echo JText::_('JREGISTER');?></button>
			<?php //echo JText::_('COM_USERS_OR');?>
			<a class="cancel_reg" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php //echo JText::_('JCANCEL');?></a>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="registration.register" />
			<?php echo JHtml::_('form.token');?>
            <div class="cls"></div>
		</div>
    </div>
	</form>
</div>
</div>
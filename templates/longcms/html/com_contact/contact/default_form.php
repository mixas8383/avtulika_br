<?php

 /**
 * @package	LongCMS.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

 if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>

<div class="contact_form">
	<form id="contact_form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
		<fieldset>
            <legend class="contact_formtitle"><?php echo JText::_('COM_CONTACT_FORM_LABEL'); ?></legend>
            <div class="contact_labelinput">
                <div class="contact_label">
                    <?php echo $this->form->getLabel('contact_name'); ?>
                </div>
                <div class="contact_input">
                    <?php echo $this->form->getInput('contact_name'); ?>
                </div>
                <div class="cls"></div>
            </div>
            <div class="contact_labelinput">
                <div class="contact_label">
                    <?php echo $this->form->getLabel('contact_email'); ?>
                </div>
                <div class="contact_input">
                    <?php echo $this->form->getInput('contact_email'); ?>
                </div>
                <div class="cls"></div>
            </div>
            <div class="contact_labelinput">
                <div class="contact_label">
                    <?php echo $this->form->getLabel('contact_subject'); ?>
                </div>
                <div class="contact_input">
                    <?php echo $this->form->getInput('contact_subject'); ?>
                </div>
                <div class="cls"></div>
            </div>
            <div class="contact_labelinput">
                <div class="contact_label">
                    <?php echo $this->form->getLabel('contact_message'); ?>
                </div>
                <div class="contact_input">
                    <?php echo $this->form->getInput('contact_message'); ?>
                </div>
                <div class="cls"></div>
            </div>
            <?php 	if ($this->params->get('show_email_copy')){ ?>
                <div class="contact_sendmail">
                    <div class="contact_check">
                        <span><?php echo $this->form->getInput('contact_email_copy'); ?></span>
                        <span><?php echo $this->form->getLabel('contact_email_copy'); ?></span>
                    </div>
                    <div class="cls"></div>
                </div>
            <?php 	} ?>
			<?php //Dynamically load any additional fields from plugins. ?>
			     <?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			          <?php if ($fieldset->name != 'contact'):?>
			               <?php $fields = $this->form->getFieldset($fieldset->name);?>
			               <?php foreach($fields as $field): ?>
			                    <?php if ($field->hidden): ?>
			                         <?php echo $field->input;?>
			                    <?php else:?>
			                         <dt>
			                            <?php echo $field->label; ?>
			                            <?php if (!$field->required && $field->type != "Spacer"): ?>
			                               <span class="optional"><?php echo JText::_('COM_CONTACT_OPTIONAL');?></span>
			                            <?php endif; ?>
			                         </dt>
			                         <dd><?php echo $field->input;?></dd>
			                    <?php endif;?>
			               <?php endforeach;?>
			          <?php endif ?>
			     <?php endforeach;?>
                 <div class="contact_buttons">
                    <div class="contact_check">
                        <button class="send_button validate" type="submit"><?php //echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
                   </div>
                   <div class="cls"></div>
               </div>
                <input type="hidden" name="option" value="com_contact" />
                <input type="hidden" name="task" value="contact.submit" />
                <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
                <input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
                <?php echo JHtml::_( 'form.token' ); ?>
		</fieldset>
	</form>
</div>

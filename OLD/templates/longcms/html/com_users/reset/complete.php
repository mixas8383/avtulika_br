<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.noframes');
?>
<div class="module_block">
    <div class="login_body">
        <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="login_title">
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </div>
        <?php endif; ?>
    
        <form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.complete'); ?>" method="post" class="form-validate">
    
            <?php foreach ($this->form->getFieldsets() as $fieldset): ?>
            <div class="reset_desc">
				<?php echo JText::_($fieldset->label); ?>
            </div>
            <fieldset>
                <div class="reset_items">
                <?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
                	<div class="login-fields">
                        <div class="reset_label">
							<?php echo $field->label; ?>
                        </div>
                        <div class="reset_input">
							<?php echo $field->input; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </fieldset>
            <?php endforeach; ?>
    
            <div class="reset_but">
                <button type="submit" class="save_button validate"><?php //echo JText::_('JSUBMIT'); ?></button>
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </form>
    </div>
</div>
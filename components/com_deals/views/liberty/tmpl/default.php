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
if ($this->params->get('liberty_description')) {
	?>
	<div class="installment_body">
		<?php echo $this->params->get('liberty_description'); ?>
	</div>
	<?php
}






//$url = JRoute::_('index.php?option=com_deals&task=liberty.submit');
$url = 'http://onlineinstallment.lb.ge/installment';









?>



<form id="liberty-profile" action="<?php echo $url; ?>" method="post" class="form-validate">
	<div class="installment_block">

		<div class="installment_items">

			<div class="installment_label">
				<label id="jform_ins_shipping_firstname-lbl" for="jform_ins_shipping_firstname" class="hasTip required">სახელი<span class="star">&nbsp;*</span></label>
			</div>
			<div class="installment_input">
				<input type="text" name="shipping_firstname" id="jform_ins_shipping_firstname" class="required _hid" />
			</div>
			<div class="cls"></div>

			<div class="installment_label">
				<label id="jform_ins_shipping_lastname-lbl" for="jform_ins_shipping_lastname" class="hasTip required">გვარი<span class="star">&nbsp;*</span></label>
			</div>
			<div class="installment_input">
				<input type="text" name="shipping_lastname" id="jform_ins_shipping_lastname" class="required _hid" />
			</div>
			<div class="cls"></div>

			<div class="installment_label">
				<label id="jform_ins_shipping_phone-lbl" for="jform_ins_shipping_phone" class="hasTip required">ტელეფონი<span class="star">&nbsp;*</span></label>
			</div>
			<div class="installment_input">
				<input type="text" name="shipping_phone" id="jform_ins_shipping_phone" class="required _hid" />
			</div>
			<div class="cls"></div>


			<div class="installment_label">
				<label id="jform_ins_shipping_address-lbl" for="jform_ins_shipping_address" class="hasTip required">მიტანის მისამართი<span class="star">&nbsp;*</span></label>
			</div>
			<div class="installment_input">
				<textarea name="shipping_address" id="jform_ins_shipping_address" cols="50" rows="10" class="required _hid"></textarea>
			</div>
			<div class="cls"></div>



		</div>


		<div class="installment_button_save">
			<button type="submit" id="_submit" class="validate send_button"></button>
		</div>
        </div>




		<input type="hidden" name="products[0][id]" value="<?php echo $this->deal->getId() ?>" />
		<input type="hidden" name="products[0][title]" value="<?php echo $this->deal->getTitle() ?>" />
		<input type="hidden" name="products[0][amount]" value="1" />
		<input type="hidden" name="products[0][price]" value="<?php echo $this->deal->getPrice() ?>" />
		<input type="hidden" name="products[0][type]" value="0" />
		<input type="hidden" name="products[0][installmenttype]" value="0" />

		<input type="hidden" id="_merchant" name="merchant" value="" />
		<input type="hidden" id="_ordercode" name="ordercode" value="" />
		<input type="hidden" id="_callid" name="callid" value="" />
		<input type="hidden" id="_testmode" name="testmode" value="" />
		<input type="hidden" id="_check" name="check" value="" />





	</form>
</div>
</div>
<?php


ob_start();
?>
<script>
$(function() {
    	$('#liberty-profile').on('submit', function(){


		$firstname = $('#jform_ins_shipping_firstname').val();
		if ($firstname == '') {
			alert('Firstname not specified!');
			return false;
		}

		$lastname = $('#jform_ins_shipping_lastname').val();
		if ($lastname == '') {
			alert('Lastname not specified!');
			return false;
		}

		$phone = $('#jform_ins_shipping_phone').val();
		if ($lastname == '') {
			alert('Phone not specified!');
			return false;
		}


		$address = $('#jform_ins_shipping_address').val();
		if ($address == '') {
			alert('Address not specified!');
			return false;
		}

		$button = $('#_submit');

		$submit = false;

		$.ajax({
			url: "<?php echo JRoute::_('index.php?option=com_deals&task=liberty.check')?>",
			type: "POST",
			cache: false,
			timeout: 5000,
			dataType: 'json',
			data: 'shipping_address='+$address+'&shipping_firstname='+$firstname+'&shipping_lastname='+$lastname+'&shipping_phone='+$phone,
			async : false,
			beforeSend: function(jqXHR, settings) {
				$button.hide();
				//$('#balance_loading').show();
			},
			success: function(data) {
				if (data.status == 'success') {
					merchant = data.merchant;
					ordercode = data.ordercode;
					callid = data.callid;
					check = data.check;
					testmode = data.testmode;

					$('#_merchant').val(merchant);
					$('#_ordercode').val(ordercode);
					$('#_callid').val(callid);
					$('#_testmode').val(testmode);
					$('#_check').val(check);

					//$('#balance_body').html(data.data);
					//$('#balance_updated').html(data.updated);

					//if (data.sum < 0) {
					//	$('#balance_sum').removeClass('badge-success').addClass('badge-important');
					//} else {
					//	$('#balance_sum').removeClass('badge-important').addClass('badge-success');
					//}
					//$('#balance_sum').html(data.sum);
					//$('#balance_msg').html(data.msg).removeClass('alert-danger').addClass('alert-success').show();


					//$submit = false;
					$submit = true;
				} else {
					//$('#balance_msg').html(data.msg).removeClass('alert-success').addClass('alert-danger').show();
					$button.show();
				}
				//$('#balance_loading').hide();
				//$this.show();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				//$('#balance_msg').html(update_error).removeClass('alert-success').addClass('alert-danger').show();
				//$('#balance_loading').hide();
				$button.show();
			}
		});


    		return $submit;
    	});
});
</script>
<?php
$js = ob_get_clean();
$this->document->addScriptDeclaration($js);





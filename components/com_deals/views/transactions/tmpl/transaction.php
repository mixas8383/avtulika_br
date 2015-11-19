<?php
/**
 * @package	LongCMS.Site
 * @subpackage	com_blank
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="deals">

	<?php
	$user = JFactory::getUser();
	// check user
	if (!$user->id) {
		echo JText::_('COM_DEALS_MUSTLOGIN');
		echo '</div>';
		return false;
	}

	if (!empty($this->items)) {


  		$jdate = JFactory::getDate($this->items->date);
  		$jdate->setTimeZone('Asia/Tbilisi');
  		$date = $jdate->format(null, true);

		$title = JFilterOutput::clean($this->items->title);

		$multipic = JFactory::getMultipic();
		$img = $this->items->image1;
		$image = Juri::root().$multipic->getImage('image10', $img);

		$price = Balance::convertAsMajor($this->items->amount);
		$description = $this->items->description;

		$company_name = JFilterOutput::clean($this->items->company_name);
		$company_desc = $this->items->company_description;

		$app = JFactory::getApplication();
		$tpl   = $app->getTemplate(true);
		$logo = Juri::root().$tpl->params->get('logo');

		$company_url = JFilterOutput::clean($this->items->company_url);
		$company_address = JFilterOutput::clean($this->items->company_address);
		$company_phone = JFilterOutput::clean($this->items->company_phone);
		$company_hours = JFilterOutput::clean($this->items->company_hours);



		?>
		<div class="transactions">
			<div class="brao_print" style="float: right;">
				<a onclick="window.print();return false;" href="javascript:void(0);" title="<?php echo JText::_('JGLOBAL_PRINT') ?>">
					<img alt="<?php echo JText::_('JGLOBAL_PRINT') ?>" src="<?php echo JURI::root()?>/media/system/images/printButton.png" />
				</a>
			</div>

			<div class="brao_transaction">
				<div class="brao_header">
					<div class="brao_header_center" style="font-size:18px; text-align:center;font-weight:bold; padding-bottom:5px;">
						კუპონის კოდი: <?php echo $this->items->transaction_number ?>
					</div>
					<div class="brao_header_right">
						<div class="brao_header_logo" style="text-align:right; padding-bottom:5px;">
							<img src="<?php echo $logo ?>" alt="Brao.Ge" title="Brao.Ge" />
						</div>
						<div class="brao_header_transdate" style="text-align:right; padding-bottom:3px;">
							შეკვეთის თარიღი: <?php echo $date ?>
						</div>
						<div class="brao_header_transaction" style="text-align:right; padding-bottom:10px;">
							შეკვეთის ნომერი: <?php echo $this->items->transaction_number ?>
						</div>
						<div class="brao_header_transaction" style="text-align:right; padding-bottom:10px;">
							შემკვეთი: <?php echo $this->items->user_fullname ?> (პ/ნ: <?php echo $this->items->user_persNumber ?>)
						</div>

					</div>
				</div>

				<div class="brao_product" style="margin-top:20px">
					<div class="brao_product_header">
			        <table width="100%" border="0">
			          <tr>
			            <td><div class="brao_product_image">
							<img src="<?php echo $image ?>" alt="<?php echo $title ?>" title="<?php echo $title ?>" align="left" style="margin:0 10px 5px 0;" />
						</div></td>
			            <td valign="middle" align="left"><div class="brao_product_title" style="font-size:18px; font-weight:bold; padding-bottom:5px;height:204px">
							<?php echo $title ?>
						</div>

					<div class="brao_product_price" style="font-size:18px; text-align:center; font-weight:bold; padding-bottom:10px;">
						<div style="float:left; width:30%; text-align:right; padding-top:10px;">
							ღირებულება:&nbsp;&nbsp;&nbsp;&nbsp;
						</div>
						<div style="float:left;width:69%; text-align:left; border-top:1px solid #CCCCCC; padding-top:10px;">
							<?php echo $price.' '.JText::_('GELI') ?>
						</div>
						<div style="clear:both;"></div>
					</div>


					</td>
			          </tr>
			        </table>



					</div>
					<div style="clear:both"></div>


			        <div class="brao_product_company_desc" style="margin-top:30px">
			            <div class="brao_product_body" style="float:left; width:46%;">
			                <div class="brao_product_desc">
			                    <div class="brao_product_desc_t" style="font-size:15px; font-weight:bold; padding-bottom:10px;">
			                        კუპონის აღწერა/გამოყენება
			                    </div>
			                    <div class="brao_product_desc_b" style="padding-left:15px;">
			                        <?php echo $description ?>
			                    </div>
			                </div>
			            </div>
			            <div class="brao_company_body" style="float:right; width:46%;">
			                <div class="brao_company_desc">
			                    <div class="brao_company_desc_t" style="font-size:15px; padding-bottom:20px; font-weight:bold;">
			                        ინფორმაცია კომპანიაზე
			                    </div>

			                    <div class="brao_company_desc_b">

							<?php
							if (!empty($company_name)) {
								?>
								<div class="deal_company_title" style="padding-bottom: 10px;">
									<img src="templates/longcms/images/icons/country_icon.png" alt="ico" title="კომპანიის სახელი" />
									<span style="font-weight:bold;line-height:24px;width:284px;padding-left:10px;vertical-align: top;">
										<?php echo $company_name; ?>
									</span>
				                      		<div style="clear:both;"></div>
								</div>
								<?php
							}


							if (!empty($company_url)) {
								$company_url2 = $company_url;
								if (substr($company_url2, 0, 4) !== 'http') {
									$company_url2 = 'http://'.$company_url2;
								}
								?>
								<div class="deal_company_url" style="padding-bottom: 10px;">
									<img src="/templates/longcms/images/icons/web_icon.png" alt="ico" title="კომპანიის ვებ-საიტი" />
									<span style="line-height:24px;width:284px;padding-left:10px;vertical-align: top;">
										<a href="<?php echo $company_url2; ?>" target="_blank">
											<?php echo $company_url; ?>
										</a>
									</span>
				                      		<div style="clear:both;"></div>
								</div>
								<?php
							}

							if (!empty($company_address)) {
								?>
								<div class="deal_company_address" style="padding-bottom: 10px;">
									<img src="templates/longcms/images/icons/info_icon.png" alt="ico" title="კომპანიის მისამართი" />
									<span style="line-height:24px;width:284px;padding-left:10px;vertical-align: top;">
										<?php echo $company_address; ?>
									</span>
				                      		<div style="clear:both;"></div>
								</div>
								<?php
							}
							if (!empty($company_phone)) {
								?>
								<div class="deal_company_phone" style="padding-bottom: 10px;">
									<img src="templates/longcms/images/icons/phone_icon.png" alt="ico" title="კომპანიის ტელეფონი" />
									<span style="line-height:24px;width:284px;padding-left:10px;vertical-align: top;">
										<?php echo $company_phone; ?>
									</span>
				                      		<div style="clear:both;"></div>
								</div>
								<?php
							}
							if (!empty($company_hours)) {
								?>
								<div class="deal_company_hours" style="padding-bottom: 10px;">
									<img src="templates/longcms/images/icons/time_icon.png" alt="ico" title="კომპანიის სამუშაო საათები" />
									<span style="line-height:24px;width:284px;padding-left:10px;vertical-align: top;">
										<?php echo $company_hours; ?>
									</span>
				                      		<div style="clear:both;"></div>
								</div>
								<?php
							}
							?>
			                    </div>
			                </div>
			            </div>
			            <div style="clear:both;"></div>
			        </div>

				</div>


				<div class="brao_bottom" style="margin-top:20px;margin-left:15px">
					გმადლობთ რომ სარგებლობთ www.brao.ge-ს მომსახურეობით
				</div>

			</div>

		</div>
		<?php
	} else {
		?>
            <div class="deals_notfound">
            	<?php echo JText::_('COM_DEALS_NO_TRANSACTION_FOUND') ?>
            </div>
            <?php
	}
	?>


</div>


<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div class="brao_transaction">
	<div class="brao_header">
		<div class="brao_header_center" style="font-size:18px; text-align:center;font-weight:bold; padding-bottom:5px;">
			ვაუჩერის კოდი: {BRAO_TRANS_NUMBER}
		</div>
		<div class="brao_header_right">
			<div class="brao_header_logo" style="text-align:right; padding-bottom:5px;">
				<img src="{BRAO_LOGO}" alt="Brao.Ge" title="Brao.Ge" />
			</div>
			<div class="brao_header_transdate" style="text-align:right; padding-bottom:3px;">
				შეკვეთის თარიღი: {BRAO_TRANS_DATE}
			</div>
			<div class="brao_header_transaction" style="text-align:right; padding-bottom:3px;">
				შეკვეთის ნომერი: {BRAO_TRANS_NUMBER}
			</div>
			<div class="brao_header_transaction" style="text-align:right; padding-bottom:3px;">
				შემკვეთი: {USER_FULL_NAME}
			</div>
			<div class="brao_header_transaction" style="text-align:right; padding-bottom:3px;">
				შემკვეთის პ/ნ: {USER_PERS_NUM}
			</div>
			<div class="brao_header_transaction" style="text-align:right; padding-bottom:3px;">
				შემკვეთის მობ: {USER_MOB}
			</div>

		</div>
	</div>

	<div class="brao_product">
		<div class="brao_product_header">
        <table width="100%" border="0">
          <tr>
            <td><div class="brao_product_image">
				<img src="{PRODUCT_IMAGE}" alt="{PRODUCT_TITLE}" title="{PRODUCT_TITLE}" align="left" style="margin:0 10px 5px 0;" />
			</div></td>
            <td valign="middle" align="left"><div class="brao_product_title" style="font-size:18px; font-weight:bold; padding-bottom:5px;">
				{PRODUCT_TITLE}
			</div></td>
          </tr>
        </table>



		</div>
		<div style="clear:both"></div>

		<div class="brao_product_price" style="font-size:22px; text-align:center; font-weight:bold; padding-bottom:10px;">
        	<div style="float:left; width:80%; text-align:right; padding-top:10px;">
			ღირებულება:
            </div>
            <div style="float:left; width:20%; text-align:center; border-top:1px solid #CCCCCC; padding-top:10px;">
             {PRODUCT_PRICE}
             </div>
             <div style="clear:both;"></div>
		</div>
        <div class="brao_product_company_desc" style="margin-top: 20px;">
            <div class="brao_product_body" style="float:left; width:46%;">
                <div class="brao_product_desc">
                    <div class="brao_product_desc_t" style="font-size:15px; font-weight:bold; padding-bottom:10px;">
                        კუპონის აღწერა/გამოყენება
                    </div>
                    <div class="brao_product_desc_b" style="padding-left:15px;">
                        {PRODUCT_DESCRIPTION}
                    </div>
                </div>
            </div>
            <div class="brao_company_body" style="float:right; width:46%;">
                <div class="brao_company_desc">
                    <div class="brao_company_desc_t" style="font-size:15px; padding-bottom:20px; font-weight:bold;">
                        ინფორმაცია კომპანიაზე
                    </div>
                    <div class="brao_company_desc_b">
                        {COMPANY_DESCRIPTION}
                    </div>
                </div>
            </div>
            <div style="clear:both;"></div>
        </div>

	</div>

	<div class="brao_bottom"  style="margin-top:20px;margin-left:15px">
		გმადლობთ რომ სარგებლობთ www.brao.ge-ს მომსახურეობით
	</div>
</div>

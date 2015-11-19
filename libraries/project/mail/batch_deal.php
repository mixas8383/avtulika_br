<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>



<tr>
	<td>
    <div style="padding-bottom:10px; border-bottom:1px solid #CCCCCC; margin-bottom:10px; position:relative;">
    <table border="0" cellpadding="0" width="100%">
    	<tr>
        <td rowspan="2">
		<div class="braob_product_image">
			<a href="{PRODUCT_LINK}" target="_blank">
				<img src="{PRODUCT_IMAGE}" alt="{PRODUCT_TITLE}" title="{PRODUCT_TITLE}" align="left" style="margin:0 20px 5px 0;" />
			</a>
		</div>
        </td>
        <td valign="top">
		<div class="braob_product_title" style="font-size:14px; font-weight:bold; padding-bottom:5px;">
			{PRODUCT_TITLE}
		</div>
        </td>
        </tr>
        <tr>
        <td valign="bottom">
		<div class="braob_product_detail" style="padding-top:10px; text-align:right; position:absolute; right:0px; bottom:10px;">
			<a href="{PRODUCT_LINK}" target="_blank">
				<img src="<?php echo JURI::root();?>templates/longcms/images/det_hover.png" alt=" "/>
			</a>
		</div>
        </td>
        </tr>
    </table>
    <div style="clear:both;"></div>
    </div>
	</td>
</tr>





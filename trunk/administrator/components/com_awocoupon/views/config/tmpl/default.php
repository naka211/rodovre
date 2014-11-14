<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
?>

<style>
	table.admintable td.key { white-space:nowrap; width:auto; }
	div.current {background-color:#ffffff;border:1px solid #cccccc;clear:both;padding: 10px 10px;}
</style>

<form action="index.php" method="post" id="adminForm" name="adminForm">
<div class="width-100">


<?php

if(version_compare( JVERSION, '1.6.0', 'ge' )) {
$options = array(
    'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
    'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
    'startOffset' => 0,  // 0 starts on the first tab, 1 starts the second, etc...
    'useCookie' => true, // this must not be a string. Don't use quotes.
);	
	echo JHtml::_('tabs.start', 'content-pane',$options); 
		echo JHtml::_('tabs.panel', JText::_( 'COM_AWOCOUPON_CFG_GENERAL' ), 'jfcpanel-panel-general');
 
} 
else {
	$pane		= JPane::getInstance('Tabs');
	echo $pane->startPane("content-pane");
		echo $pane->startPanel( JText::_( 'COM_AWOCOUPON_CFG_GENERAL' ), 'jfcpanel-panel-general' );
}
?>	
			
	<table class="admintable">
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_GBL_ECOMMERCE' ); ?></label></td>
		<td><?php echo $this->lists['estore']; ?></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_ENABLE_STORE_COUPONS' ); ?></label></td>
		<td><?php echo $this->lists['enable_store_coupon']; ?></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_CALC_DISCOUNT_BEFORE_TAX' ); ?> (<?php echo JText::_( 'COM_AWOCOUPON_GC_GIFTCERTS' ); ?>)</label></td>
		<td><?php echo $this->lists['enable_giftcert_discount_before_tax']; ?></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_CALC_DISCOUNT_BEFORE_TAX' ); ?> (<?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?>)</label></td>
		<td><?php echo $this->lists['enable_coupon_discount_before_tax']; ?></td>
	</tr>
	<tr valign="top"><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_ENABLE_MULTIPLE_COUPONS' ); ?></label></td>
		<td><table><tr valign="top"><td><?php echo $this->lists['enable_multiple_coupon']; ?></td>
			
			<td><div><table>
				<tr><td><?php echo JText::_( 'COM_AWOCOUPON_GBL_MAX' ); ?></td>
					<td nowrap><input type="text" size="4" name="params[multiple_coupon_max]" value="<?php echo $this->params->get('multiple_coupon_max', ''); ?>" > &nbsp;</td>
					<td> <?php echo JText::_( 'COM_AWOCOUPON_GBL_ALL' ); ?></td>
				</tr>
				<tr><td></td>
					<td nowrap><input type="text" size="4" name="params[multiple_coupon_max_auto]" value="<?php echo $this->params->get('multiple_coupon_max_auto', ''); ?>" > &nbsp;</td>
					<td><?php echo JText::_( 'COM_AWOCOUPON_CP_AUTO_DISCOUNT' ); ?></td>
				</tr>
				<tr><td></td>
					<td nowrap><input type="text" size="4" name="params[multiple_coupon_max_giftcert]" value="<?php echo $this->params->get('multiple_coupon_max_giftcert', ''); ?>" > &nbsp;</td>
					<td><?php echo JText::_( 'COM_AWOCOUPON_GC_GIFTCERTS' ); ?></td>
				</tr>
				<tr><td></td>
					<td nowrap><input type="text" size="4" name="params[multiple_coupon_max_coupon]" value="<?php echo $this->params->get('multiple_coupon_max_coupon', ''); ?>" > &nbsp;</td>
					<td><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></td>
				</tr>
				</table>
			</div>
			</td></tr></table>
						
		</td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_FRONTEND_IMAGE' ); ?></label></td>
		<td><?php echo $this->lists['enable_frontend_image']; ?></td>
	</tr>
	<?php if(AWOCOUPON_ESTORE!='redshop') { ?>
	<tr valign="top"><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_ORDERCANCEL' ); ?></label></td>
		<td><?php echo $this->lists['ordercancel_order_status']; ?></td>
	</tr>
	<?php } ?>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_DELETE_EXPIRED_COUPON' ); ?></label></td>
		<td><input type="text" size="4" name="params[delete_expired]" value="<?php echo $this->params->get('delete_expired', ''); ?>" ></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_GEN_CASE_SENSITIVE' ); ?></label></td>
		<td><?php echo $this->lists['casesensitive']; echo $this->lists['casesensitiveold']; ?></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_GEN_CSV_DELIMITER' ); ?></label></td>
		<td><?php echo $this->lists['csvDelimiter']; ?></td>
	</tr>

	<?php if(version_compare( JVERSION, '1.6.0', 'ge' )) ; else { ?>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_JOOMFISH_INSTALL' ); ?></label></td>
		<td><input type="checkbox" size="4" name="install_joomfish" value="1" ></td>
	</tr>
	<?php } ?>

	</table>
<?php

if(version_compare( JVERSION, '1.6.0', 'ge' )) {
	echo JHtml::_('tabs.panel', JText::_('COM_AWOCOUPON_GC_PRODUCTS'), 'jfcpanel-panel-giftcert');

}
else {	
	echo $pane->endPanel();
	echo $pane->startPanel( JText::_( 'COM_AWOCOUPON_GC_PRODUCTS' ), 'jfcpanel-panel-giftcert' );
}
?>

	
	<fieldset class="adminform"><legend><?php echo JText::_( 'COM_AWOCOUPON_CFG_GENERAL' ); ?></legend>
		<table class="admintable">

		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_GC_ORDER_STATUS' ); ?></label></td>
			<td><?php echo $this->lists['giftcert_order_status']; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_GC_RECIPIENT_NAME' ); ?></label></td>
			<td><input type="text" size="75" name="params[<?php echo AWOCOUPON_ESTORE; ?>_giftcert_field_recipient_name]" value="<?php echo $this->params->get(AWOCOUPON_ESTORE.'_giftcert_field_recipient_name', 0); ?>" ></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_GC_RECIPIENT_EMAIL' ); ?></label></td>
			<td><input type="text" size="75" name="params[<?php echo AWOCOUPON_ESTORE; ?>_giftcert_field_recipient_email]" value="<?php echo $this->params->get(AWOCOUPON_ESTORE.'_giftcert_field_recipient_email', 0); ?>" ></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_GC_RECIPIENT_MSG' ); ?></label></td>
			<td><input type="text" size="75" name="params[<?php echo AWOCOUPON_ESTORE; ?>_giftcert_field_recipient_message]" value="<?php echo $this->params->get(AWOCOUPON_ESTORE.'_giftcert_field_recipient_message', 0); ?>" ></td>
		</tr>
		</table>
	</fieldset>
	
	
	<fieldset class="adminform"><legend><?php echo JText::_( 'COM_AWOCOUPON_CP_VENDOR' ); ?></legend>
		<table class="admintable">
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_LI_ACTIVATE' ); ?></label></td>
			<td><?php echo $this->lists['giftcert_vendor_enable']; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_PF_EMAIL_SUBJECT' ); ?></label></td>
			<td><input type="text" name="params[giftcert_vendor_subject]" value="<?php echo $this->params->get('giftcert_vendor_subject', ''); ?>" size="35"></td>
		</tr>
		<tr valign="top"><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_PF_EMAIL_BODY' ); ?></label>
			<br /><br /><table style="text-align:left;font-weight:normal;" align="right" cellspacing=0><tr><th><?php echo JText::_( 'TAGS' ); ?></th></tr>
			<tr><td>{vendor_name}</td></tr>
			<tr><td>{vouchers}<tr><td>
			<tr><td>{purchaser_first_name}<tr><td>
			<tr><td>{purchaser_last_name}<tr><td>
			<tr><td>{today_date}<tr><td>
			<tr><td>{order_id}<tr><td>
			<tr><td>{order_number}<tr><td>
			</table>
		</td>
			<td><?php echo $this->editor->display( 'params[giftcert_vendor_email]',  $this->params->get('giftcert_vendor_email','') , '100%', '350', '75', '20' ) ; ?></td>
		</tr>
		<tr><td class="key"><label>{vouchers}</label></td>
			<td><input type="text" name="params[giftcert_vendor_voucher_format]" value="<?php echo $this->params->get('giftcert_vendor_voucher_format', '<div>{voucher} - {price} - {product_name}</div>'); ?>" size="65"></td>
		</tr>

		</table>
	</fieldset>
	
<?php
if(version_compare( JVERSION, '1.6.0', 'ge' )) { 
	echo JHtml::_('tabs.panel', JText::_('COM_AWOCOUPON_CP_COUPON_CODE').' '.JText::_( 'COM_AWOCOUPON_CFG_ERROR_DESC' ), 'jfcpanel-panel-err');
}
else {	
	echo $pane->endPanel();
	echo $pane->startPanel( JText::_('COM_AWOCOUPON_CP_COUPON_CODE').' '.JText::_( 'COM_AWOCOUPON_CFG_ERROR_DESC' ), 'jfcpanel-panel-err' );
}
?>
	
	<table class="admintable">
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_NO_RECORD' ); ?></label></td>
		<td><input type="text" size="75" name="params[errNoRecord]" value="<?php echo $this->params->get('errNoRecord', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_MIN_VAL' ); ?></label></td>
		<td><input type="text" size="75" name="params[errMinVal]" value="<?php echo $this->params->get('errMinVal', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_LOGGED_IN' ); ?></label></td>
		<td><input type="text" size="75" name="params[errUserLogin]" value="<?php echo $this->params->get('errUserLogin', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_CUSTOMER_LIST' ); ?></label></td>
		<td><input type="text" size="75" name="params[errUserNotOnList]" value="<?php echo $this->params->get('errUserNotOnList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_CUSTOMER_LIST_NOT' ); ?></label></td>
		<td><input type="text" size="75" name="params[errUserGroupNotOnList]" value="<?php echo $this->params->get('errUserGroupNotOnList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_MAX_USES_USER' ); ?></label></td>
		<td><input type="text" size="75" name="params[errUserMaxUse]" value="<?php echo $this->params->get('errUserMaxUse', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_MAX_USES_TOTAL' ); ?></label></td>
		<td><input type="text" size="75" name="params[errTotalMaxUse]" value="<?php echo $this->params->get('errTotalMaxUse', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_INC_PRODUCT' ); ?></label></td>
		<td><input type="text" size="75" name="params[errProductInclList]" value="<?php echo $this->params->get('errProductInclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_EXC_PRODUCT' ); ?></label></td>
		<td><input type="text" size="75" name="params[errProductExclList]" value="<?php echo $this->params->get('errProductExclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_INC_CATEGORY' ); ?></label></td>
		<td><input type="text" size="75" name="params[errCategoryInclList]" value="<?php echo $this->params->get('errCategoryInclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_EXC_CATEGORY' ); ?></label></td>
		<td><input type="text" size="75" name="params[errCategoryExclList]" value="<?php echo $this->params->get('errCategoryExclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_INC_MANU' ); ?></label></td>
		<td><input type="text" size="75" name="params[errManufacturerInclList]" value="<?php echo $this->params->get('errManufacturerInclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_EXC_MANU' ); ?></label></td>
		<td><input type="text" size="75" name="params[errManufacturerExclList]" value="<?php echo $this->params->get('errManufacturerExclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_INC_VENDOR' ); ?></label></td>
		<td><input type="text" size="75" name="params[errVendorInclList]" value="<?php echo $this->params->get('errVendorInclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_EXC_VENDOR' ); ?></label></td>
		<td><input type="text" size="75" name="params[errVendorExclList]" value="<?php echo $this->params->get('errVendorExclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_NO_SHIPPING' ); ?></label></td>
		<td><input type="text" size="75" name="params[errShippingSelect]" value="<?php echo $this->params->get('errShippingSelect', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_NO_SHIPPING_VALID' ); ?></label></td>
		<td><input type="text" size="75" name="params[errShippingValid]" value="<?php echo $this->params->get('errShippingValid', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_INC_SHIPPING' ); ?></label></td>
		<td><input type="text" size="75" name="params[errShippingInclList]" value="<?php echo $this->params->get('errShippingInclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_EXC_SHIPPING' ); ?></label></td>
		<td><input type="text" size="75" name="params[errShippingExclList]" value="<?php echo $this->params->get('errShippingExclList', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_USED_GIFTCERT' ); ?></label></td>
		<td><input type="text" size="75" name="params[errGiftUsed]" value="<?php echo $this->params->get('errGiftUsed', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_VALDEF_THRESHOLD' ); ?></label></td>
		<td><input type="text" size="75" name="params[errProgressiveThreshold]" value="<?php echo $this->params->get('errProgressiveThreshold', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_EXC_PRODUCT_DISCOUNTED' ); ?></label></td>
		<td><input type="text" size="75" name="params[errDiscountedExclude]" value="<?php echo $this->params->get('errDiscountedExclude', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXCLUDE_GIFTCERT' ); ?></label></td>
		<td><input type="text" size="75" name="params[errGiftcertExclude]" value="<?php echo $this->params->get('errGiftcertExclude', $this->defaulterror); ?>"></td>
	</tr>
	
	
	
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_BUYXY_INC_LIST1' ); ?></label></td>
		<td><input type="text" size="75" name="params[errBuyXYList1IncludeEmpty]" value="<?php echo $this->params->get('errBuyXYList1IncludeEmpty', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_BUYXY_EXC_LIST1' ); ?></label></td>
		<td><input type="text" size="75" name="params[errBuyXYList1ExcludeEmpty]" value="<?php echo $this->params->get('errBuyXYList1ExcludeEmpty', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_BUYXY_INC_LIST2' ); ?></label></td>
		<td><input type="text" size="75" name="params[errBuyXYList2IncludeEmpty]" value="<?php echo $this->params->get('errBuyXYList2IncludeEmpty', $this->defaulterror); ?>"></td>
	</tr>
	<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CFG_COUPERR_BUYXY_EXC_LIST2' ); ?></label></td>
		<td><input type="text" size="75" name="params[errBuyXYList2ExcludeEmpty]" value="<?php echo $this->params->get('errBuyXYList2ExcludeEmpty', $this->defaulterror); ?>"></td>
	</tr>
	
	
	</table>
<?php
if(version_compare( JVERSION, '1.6.0', 'ge' )) { 
	echo JHtml::_('tabs.end');
}
else {	
		echo $pane->endPanel();
	echo $pane->endPane();
}
?>

</div>	



	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="config" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

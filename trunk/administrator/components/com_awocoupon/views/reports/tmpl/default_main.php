<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
?>

<form action="index.php" method="post" id="adminForm" name="adminForm" target="_blank">

	<div class="width-100">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_AWOCOUPON_RPT_REPORTS'); ?></legend>
			<select name="report_type">
				<option value="coupon_list"><?php echo JText::_('COM_AWOCOUPON_RPT_COUPON_LIST'); ?></option>
				<option value="purchased_giftcert_list"><?php echo JText::_('COM_AWOCOUPON_RPT_PURCHASED_GIFTCERT'); ?></option>
				<option value="coupon_vs_total"><?php echo JText::_('COM_AWOCOUPON_RPT_COUPON_USAGE_TOTAL'); ?></option>
				<option value="coupon_vs_location"><?php echo JText::_('COM_AWOCOUPON_RPT_COUPON_USAGE_LOCATION'); ?></option>
				<option value="history_uses_coupons"><?php echo JText::_('COM_AWOCOUPON_CP_HISTORY_USES').' - '.JText::_('COM_AWOCOUPON_CP_COUPONS'); ?></option>
				<option value="history_uses_giftcerts"><?php echo JText::_('COM_AWOCOUPON_CP_HISTORY_USES').' - '.JText::_('COM_AWOCOUPON_GC_GIFTCERTS'); ?></option>
			</select>
		</fieldset>
	</div>

	<div class="width-100">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_AWOCOUPON_RPT_ORDER_DETAILS'); ?></legend>
			<table class="admintable">
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_DATE_START'); ?></label></td>
					<td><?php echo JHTML::calendar('', 'start_date', 'start_date', '%Y-%m-%d',
									array('size'=>'26',
									'maxlength'=>'10',
								));
						?><i style="color:#777777;">(YYYY-MM-DD)</i>
					</td>
				</tr>
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_DATE_END'); ?></label></td>
					<td><?php echo JHTML::calendar('', 'end_date', 'end_date', '%Y-%m-%d',
									array('size'=>'26',
									'maxlength'=>'10',
								));
						?><i style="color:#777777;">(YYYY-MM-DD)</i>
					</td>
				</tr>
				<tr><td class="key" nowrap valign="top"><label><?php echo JText::_('COM_AWOCOUPON_RPT_STATUS'); ?></label></td><td><?php echo $this->lists['order_status']; ?><input type="button" onclick="clearbox()" value="<?php echo JText::_('COM_AWOCOUPON_GBL_CLEAR'); ?>" /></td></tr>
			</table>
		</fieldset>
	</div>
							

	<div class="width-100">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_AWOCOUPON_CP_COUPON_DETAILS'); ?></legend>
			<table class="admintable">
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE'); ?></label></td><td><?php echo $this->lists['function_type']; ?></td></tr>
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_PERCENT_AMOUNT'); ?></label></td><td><?php echo $this->lists['coupon_value_type']; ?></td></tr>
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE'); ?></label></td><td><?php echo $this->lists['discount_type']; ?></td></tr>
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_CP_TEMPLATE'); ?></label></td><td><?php echo $this->lists['templatelist']; ?></td></tr>
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_RPT_STATUS'); ?></label></td><td><?php echo $this->lists['published']; ?></td></tr>
				<tr><td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_GC_PRODUCT'); ?></label></td><td><?php echo $this->lists['giftcert_product']; ?></td></tr>
			</table>
		</fieldset>
	</div>

	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="controller" value="reports" />
	<input type="hidden" name="view" value="reports" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>


<script language="javascript" type="text/javascript">
<!--
function clearbox(val) {
	var form = document.adminForm;
	form.elements['order_status[]'].selectedIndex = -1;
}
//-->
</script>


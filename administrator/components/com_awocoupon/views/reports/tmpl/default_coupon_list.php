<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 

$labels = array('~'.JText::_('COM_AWOCOUPON_GBL_ID'),JText::_('COM_AWOCOUPON_CP_COUPON_CODE'),JText::_('COM_AWOCOUPON_CP_PUBLISHED'),
			JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE'),JText::_('COM_AWOCOUPON_CP_PERCENT_AMOUNT'),JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE'),
			JText::_('COM_AWOCOUPON_CP_VALUE'),JText::_('COM_AWOCOUPON_CP_VALUE_DEFINITION'),
			JText::_('COM_AWOCOUPON_CP_NUMBER_USES'),JText::_('COM_AWOCOUPON_CP_NUMBER_USES'),
			JText::_('COM_AWOCOUPON_CP_VALUE_MIN'),JText::_('COM_AWOCOUPON_CP_VALUE_MIN'),
			JText::_('COM_AWOCOUPON_CP_DATE_START'),JText::_('COM_AWOCOUPON_CP_EXPIRATION'),
			JText::_('COM_AWOCOUPON_CP_CUSTOMERS'),JText::_('COM_AWOCOUPON_CP_CUSTOMERS'),JText::_('COM_AWOCOUPON_CP_CUSTOMERS'),
			
			
			JText::_('COM_AWOCOUPON_CP_ASSET').' - '.JText::_('COM_AWOCOUPON_GBL_TYPE'),JText::_('COM_AWOCOUPON_CP_ASSET'),JText::_('COM_AWOCOUPON_CP_ASSET').' - '.JText::_('COM_AWOCOUPON_GBL_NUMBER'),JText::_('COM_AWOCOUPON_CP_ASSET'),
			JText::_('COM_AWOCOUPON_CP_ASSET').' 2 - '.JText::_('COM_AWOCOUPON_GBL_TYPE'),JText::_('COM_AWOCOUPON_CP_ASSET').' 2',JText::_('COM_AWOCOUPON_CP_ASSET').' 2 - '.JText::_('COM_AWOCOUPON_GBL_NUMBER'),JText::_('COM_AWOCOUPON_CP_ASSET').' 2',
			JText::_('COM_AWOCOUPON_CP_EXCLUDE_SPECIAL'),JText::_('COM_AWOCOUPON_CP_EXCLUDE_GIFTCERT'),JText::_('COM_AWOCOUPON_CP_ADMIN_NOTE'),
			JText::_('COM_AWOCOUPON_CP_PARENT_TYPE'),JText::_('COM_AWOCOUPON_CP_MAX_DISCOUNT_QTY'),JText::_('COM_AWOCOUPON_CP_DONT_MIX_PRODUCTS'),
			JText::_('COM_AWOCOUPON_CP_ADDTOCART_GETY'),
			
			JText::_('COM_AWOCOUPON_CP_SECRET_KEY'),JText::_('COM_AWOCOUPON_CP_CUSTOMERS'),JText::_('COM_AWOCOUPON_CP_ASSET'),JText::_('COM_AWOCOUPON_CP_ASSET').' 2',);
$columns = array('id','coupon_code','str_published','str_function_type','str_coupon_value_type',
			'str_discount_type','str_coupon_value','coupon_value_def','str_num_of_uses_type','num_of_uses',
			'str_min_value_type','str_min_value','str_startdate','str_expiration','str_user_type','str_user_mode','str_userlist',
			
			
			'str_asset1_type','str_asset1_mode','str_asset1_qty','str_asset',
			'str_asset2_type','str_asset2_mode','str_asset2_qty','str_asset2',
			'str_exclude_special','str_exclude_giftcert','str_note',
			'str_process_type','str_max_discount_qty','str_product_match','str_addtocart',
			
			'passcode','str_userliststr','str_assetstr','str_assetstr2',);

if(!empty($this->row->rows)) {
	$style = null;
	$arrstr = $this->reportgrid('grid',$this->row->rows,$labels,$columns,$style);
	
}

?>
<script language="javascript" type="text/javascript">
<!--
function submitform(task) {
	form = document.adminForm;
	form.task.value = (typeof(task) !== 'undefined') ? task : '';

	// Submit the form.
	if (typeof form.onsubmit == 'function') form.onsubmit();
	if (typeof form.fireEvent == "function") form.fireEvent('submit');
	form.submit();
}
window.onload = function() {
    if( typeof Joomla != 'undefined' ){
        Joomla.submitform = submitform;
	}
}
//-->
</script>

<center>
<div><font size="5"><?php echo JText::_('COM_AWOCOUPON_RPT_COUPON_LIST'); ?></font></div>

<br><br>
<?php


if(!empty($arrstr)) {
?>
<form action="index.php" method="post" name="adminForm">

	<table border="0">
	<tr><td><INPUT TYPE="image" onclick="submitform('exportreports');" NAME="submit" src="<?php echo com_awocoupon_ASSETS;?>/images/excel.gif" border="0" alt="Export CSV">
			<font size="4"><?php echo JText::_('COM_AWOCOUPON_RPT_COUPON_LIST'); ?></font>
	</td></tr>
	<tr><td><?php echo $arrstr['html'];?></td></tr>
	</table>
	<br><br>
	
	<table align="center"><tr><td><?php echo $this->pageNav->getListFooter(); ?></td></tr></table>
	<br><br>

	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="reports" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="rpt_labels" value="<?php echo htmlentities(json_encode($labels)); ?>" />
	<input type="hidden" name="rpt_columns" value="<?php echo htmlentities(json_encode($columns)); ?>" />
	<input type="hidden" name="report_type" value="<?php echo $this->report_type; ?>" />
	<INPUT type="hidden" name="filename" value="coupon_list.csv">
	<?php echo $this->getUserParameters(); ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php echo $arrstr['js'];?>

<?php

} else echo '<br><br><div style=""><b>'.JText::_('COM_AWOCOUPON_RPT_NO_DATA').'</b></div>';


?>





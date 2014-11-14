<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 

$labels = array(JText::_('COM_AWOCOUPON_CP_COUPON_CODE'),JText::_('COM_AWOCOUPON_CP_PRODUCT'),JText::_('COM_AWOCOUPON_CP_VALUE'),JText::_('COM_AWOCOUPON_GC_VALUE_USED'),JText::_('COM_AWOCOUPON_GC_BALANCE'),JText::_('COM_AWOCOUPON_CP_EXPIRATION'),
			JText::_('COM_AWOCOUPON_RPT_USER_ID'),JText::_('COM_AWOCOUPON_GBL_USERNAME'),
			JText::_('COM_AWOCOUPON_GBL_LAST_NAME').' 2',JText::_('COM_AWOCOUPON_GBL_FIRST_NAME'),
			JText::_('COM_AWOCOUPON_GC_ORDER_NUM'),JText::_('COM_AWOCOUPON_RPT_ORDER_DATE'),);
$columns = array('coupon_code','product_name','coupon_valuestr','coupon_value_usedstr','balancestr','expiration','user_id','username','last_name','first_name','order_number','order_date',);

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

<style>
table.criteria td { text-align:left; }
</style>

<center>
<div><font size="5"><?php echo JText::_('COM_AWOCOUPON_CP_HISTORY_USES').' - '.JText::_('COM_AWOCOUPON_GC_GIFTCERTS'); ?></font></div>
<table class="criteria">
<?php if(!empty($this->start_date)) { ?><tr><td><b><?php echo JText::_('COM_AWOCOUPON_CP_DATE_START'); ?>:</b></td><td><?php echo $this->start_date; ?></td></tr><?php } ?>
<?php if(!empty($this->end_date)) { ?><tr><td><b><?php echo JText::_('COM_AWOCOUPON_CP_DATE_END'); ?>:</b></td><td><?php echo $this->end_date; ?></td></tr><?php } ?>
<?php if(!empty($this->order_status)) { ?><tr><td><b><?php echo JText::_('COM_AWOCOUPON_RPT_STATUS'); ?>:</b></td><td><?php echo $this->order_status; ?></td></tr><?php } ?>
</table>

<br><br>
<?php


if(!empty($arrstr)) {
?>
<form action="index.php" method="post" name="adminForm">

	<table border="0">
	<tr><td><INPUT TYPE="image" onclick="submitform('exportreports');" NAME="submit" src="<?php echo com_awocoupon_ASSETS;?>/images/excel.gif" border="0" alt="Export CSV">
			<font size="4"><?php echo JText::_('COM_AWOCOUPON_CP_HISTORY_USES').' - '.JText::_('COM_AWOCOUPON_GC_GIFTCERTS'); ?></font>
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
	<INPUT type="hidden" name="filename" value="history_uses_giftcerts_<?php if(!empty($this->start_date)) { echo str_replace('-','',$this->start_date).'-'.str_replace('-','',$this->end_date); } ?>.csv">
	<?php echo $this->getUserParameters(); ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php echo $arrstr['js'];?>

<?php

} else echo '<br><br><div style=""><b>'.JText::_('COM_AWOCOUPON_RPT_NO_DATA').'</b></div>';


?>





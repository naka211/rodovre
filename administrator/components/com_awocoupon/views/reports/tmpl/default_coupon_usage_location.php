<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 

$labels = array(JText::_('COM_AWOCOUPON_CP_COUPON_CODE'),JText::_('COM_AWOCOUPON_RPT_COUNTRY'),JText::_('COM_AWOCOUPON_RPT_STATE'),JText::_('COM_AWOCOUPON_RPT_CITY'),JText::_('COM_AWOCOUPON_CP_DISCOUNT'),
				 JText::_('COM_AWOCOUPON_GBL_TOTAL'),JText::_('COM_AWOCOUPON_RPT_COUNT'), '% '.JText::_('COM_AWOCOUPON_GBL_TOTAL'), '% '.JText::_('COM_AWOCOUPON_RPT_COUNT'));
$columns = array('coupon_code','country','state','city','discountstr','totalstr','count','alltotal','allcount' );

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
<div><font size="5"><?php echo JText::_('COM_AWOCOUPON_RPT_COUPON_USAGE_LOCATION'); ?></font></div>
<table class="criteria">
<?php if(!empty($this->start_date)) { ?><tr><td><b><?php echo JText::_('COM_AWOCOUPON_CP_DATE_START'); ?>:</b></td><td><?php echo $this->start_date; ?></td></tr><?php } ?>
<?php if(!empty($this->end_date)) { ?><tr><td><b><?php echo JText::_('COM_AWOCOUPON_CP_DATE_END'); ?>:</b></td><td><?php echo $this->end_date; ?></td></tr><?php } ?>
<?php if(!empty($this->order_status)) { ?><tr><td><b><?php echo JText::_('COM_AWOCOUPON_RPT_STATUS'); ?>:</b></td><td><?php echo $this->order_status; ?></td></tr><?php } ?>
</table>

<br><br>
<?php


if(!empty($arrstr)) {
?>
<form action="{$ajax_url}" method="post" name="adminForm">

	<table border="0">
	<tr><td><INPUT TYPE="image" onclick="submitform('exportreports');" NAME="submit" src="<?php echo com_awocoupon_ASSETS;?>/images/excel.gif" border="0" alt="Export CSV">
			<font size="4"><?php echo JText::_('COM_AWOCOUPON_RPT_COUPON_USAGE_LOCATION'); ?></font>
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
	<INPUT type="hidden" name="filename" value="coupon_usage_location_<?php if(!empty($this->start_date)) { echo str_replace('-','',$this->start_date).'-'.str_replace('-','',$this->end_date); } ?>.csv">
	<?php echo $this->getUserParameters(); ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php echo $arrstr['js'];?>



<?php

} else echo '<br><br><div style=""><b>'.JText::_('COM_AWOCOUPON_RPT_NO_DATA').'</b></div>';


?>





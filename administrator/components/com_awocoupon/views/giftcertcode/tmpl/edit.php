<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<script language="javascript" type="text/javascript">
<!--

//-->
</script>
<style>
.admintable tr.columnheaders td.key { text-align: center; }
.admintable tr.columnheaders td.key2 { font-weight;900;background-color:#bbbbbb;color:#ffffff;text-align: center; 	border-bottom: 1px solid #e9e9e9; border-right: 1px solid #e9e9e9; }
.admintable tr.columndata td { border:1px solid #cccccc; color:#777777; }
</style>


<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	<div class="width-100">
		<fieldset class="adminform"><legend><?php echo JText::_('COM_AWOCOUPON_CP_PRODUCT');?></legend>
			<?php echo $this->lists['productlist']; ?>
		</fieldset>
	</div>
	<div class="width-100">
		<fieldset class="adminform"><legend><?php echo JText::_('COM_AWOCOUPON_GBL_FILE');?></legend>
			<div><input type="checkbox" value="1" name="exclude_first_row" <?php if(!empty($this->exclude_first_row)) echo 'CHECKED';?>><?php echo JText::_('COM_AWOCOUPON_IMP_EXC_ROW1');?></div>
			<div><input type="checkbox" value="1" name="store_none_errors" <?php if(!empty($this->store_none_errors)) echo 'CHECKED';?>><?php echo JText::_('COM_AWOCOUPON_IMP_SAVE_BATCH_WITH_ERRS');?></div>
			<div><input type="file" name="file" style="width:100%;"></div>
		</fieldset>
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="giftcertcode" />
	<input type="hidden" name="layout" value="edit" />
</form>




<div class="width-100">
	<fieldset class="adminform"><legend><?php echo JText::_('COM_AWOCOUPON_IMP_CSV_FORMAT');?></legend>
		<table class="admintable">
		<tr class="columnheaders" valign="top">
			<td class="key"><?php echo JText::_('COM_AWOCOUPON_CP_COUPON_CODE');?></td>
			<td class="key"><?php echo JText::_('COM_AWOCOUPON_RPT_STATUS');?></td>
			<td class="key"><?php echo JText::_('COM_AWOCOUPON_CP_ADMIN_NOTE');?></td>
		</tr>
		<tr class="columnheaders" >
			<td class="key2">A</td>
			<td class="key2">B</td>
			<td class="key2">C</td>
		</tr>
		<tr class="columndata">
			<td>23MXRTC45</td>
			<td><?php echo JText::_('COM_AWOCOUPON_GBL_ACTIVE');?></td>
			<td>&nbsp;</td>
		</tr>
		<tr class="columndata">
			<td>y8B3A0E8</td>
			<td><?php echo JText::_('COM_AWOCOUPON_GBL_INACTIVE');?></td>
			<td>my admin note</td>
		</tr>
		<tr class="columndata"><td colspan="12" style="border:0;">......</td></tr>
		</table>
	</fieldset>
</div>





<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>
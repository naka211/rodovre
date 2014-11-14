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
.admintable tr.columnheaders td.key { text-align: center; background-color:#f4f4f4; }
.admintable tr.columnheaders td.key2 { font-weight:900;background-color:#bbbbbb;color:#ffffff;text-align: center; 	border-bottom: 1px solid #e9e9e9; border-right: 1px solid #e9e9e9; }
.admintable tr.columndata td { border:1px solid #cccccc; color:#777777; }
.admintable tr.columndata td.na { background-color:#F4F4F4; }
</style>


<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	<div class="width-100">
		<fieldset class="adminform"><legend><?php echo JText::_('COM_AWOCOUPON_GBL_FILE');?></legend>
			<div><input type="checkbox" value="1" name="exclude_first_row" <?php if(!empty($this->exclude_first_row)) echo 'CHECKED';?>><?php echo JText::_('COM_AWOCOUPON_IMP_EXC_ROW1');?></div>
			<br />
			<div><input type="checkbox" value="1" name="store_none_errors" <?php if(!empty($this->store_none_errors)) echo 'CHECKED';?>><?php echo JText::_('COM_AWOCOUPON_IMP_SAVE_BATCH_WITH_ERRS');?></div>
			<div><input type="file" name="file" style="width:100%;"></div>
		</fieldset>
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="import" />
</form>




<fieldset class="adminform" style="width:97%;"><legend><?php echo JText::_('COM_AWOCOUPON_IMP_CSV_FORMAT');?></legend>
	<div style="border:1px solid #555;overflow-x:auto;background-color:#fff;">


		<table class="admintable">
		<tr class="columnheaders" valign="top">
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_GBL_ID');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_COUPON_CODE');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_PUBLISHED');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_PERCENT_AMOUNT');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_VALUE');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_VALUE_DEFINITION');?></td>
			<td class="key" rowspan="2" colspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_NUMBER_USES');?></td>
			<td class="key" rowspan="2" colspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_VALUE_MIN');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_DATE_START');?><br>(YYYYMMDD hhmmss)</td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_EXPIRATION');?><br>(YYYYMMDD hhmmss)</td>
			<td class="key" rowspan="2" colspan="3"><?php echo JText::_('COM_AWOCOUPON_CP_CUSTOMERS');?></td>
			<td class="key" colspan="4"><?php echo JText::_('COM_AWOCOUPON_CP_ASSET');?></td>
			<td class="key" colspan="4"><?php echo JText::_('COM_AWOCOUPON_CP_ASSET');?> 2</td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE_SPECIAL');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE_GIFTCERT');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_ADMIN_NOTE');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_PARENT_TYPE');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_MAX_DISCOUNT_QTY');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_DONT_MIX_PRODUCTS');?></td>
			<td class="key" rowspan="2"><?php echo JText::_('COM_AWOCOUPON_CP_ADDTOCART_GETY');?></td>
		</tr>
		<tr class="columnheaders" valign="top">
			<td class="key"><?php echo JText::_('COM_AWOCOUPON_GBL_TYPE');?></td>
			<td class="key">&nbsp;</td>
			<td class="key"><?php echo JText::_('COM_AWOCOUPON_GBL_NUMBER');?></td>
			<td class="key">&nbsp;</td>
			<td class="key"><?php echo JText::_('COM_AWOCOUPON_GBL_TYPE');?></td>
			<td class="key">&nbsp;</td>
			<td class="key"><?php echo JText::_('COM_AWOCOUPON_GBL_NUMBER');?></td>
			<td class="key">&nbsp;</td>
		</tr>
		<tr class="columnheaders" >
			<td class="key2">A</td>
			<td class="key2">B</td>
			<td class="key2">C</td>
			<td class="key2">D</td>
			<td class="key2">E</td>
			<td class="key2">F</td>
			<td class="key2">G</td>
			<td class="key2">H</td>
			<td class="key2">I</td>
			<td class="key2">J</td>
			<td class="key2">K</td>
			<td class="key2">L</td>
			<td class="key2">M</td>
			<td class="key2">N</td>
			<td class="key2">O</td>
			<td class="key2">P</td>
			<td class="key2">Q</td>
			<td class="key2">R</td>
			<td class="key2">S</td>
			<td class="key2">T</td>
			<td class="key2">U</td>
			<td class="key2">V</td>
			<td class="key2">W</td>
			<td class="key2">X</td>
			<td class="key2">Y</td>
			<td class="key2">Z</td>
			<td class="key2">AA</td>
			<td class="key2">AB</td>
			<td class="key2">AC</td>
			<td class="key2">AD</td>
			<td class="key2">AE</td>
			<td class="key2">AF</td>
		</tr>
		<tr class="columndata">
			<td>1</td>
			<td>RTC45</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PUBLISHED');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_COUPON');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PERCENTAGE');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_OVERALL');?></td>
			<td>10</td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('COM_AWOCOUPON_GBL_TOTAL');?></td>
			<td>2</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>20100810</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_CUSTOMER');?></td>
			<td NOWRAP><?php echo JText::_('COM_AWOCOUPON_CP_INCLUDE');?></td>
			<td>5,7,9</td>
			<td NOWRAP><?php echo JText::_('COM_AWOCOUPON_CP_PRODUCT');?></td>
			<td NOWRAP><?php echo JText::_('COM_AWOCOUPON_CP_INCLUDE');?></td>
			<td class="na"></td>
			<td>12,24</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('COM_AWOCOUPON_GBL_NO');?></td>
			<td>&nbsp;</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
		</tr>
		<tr class="columndata">
			<td>2</td>
			<td>A0E8</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_UNPUBLISHED');?></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_COUPON');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_AMOUNT');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_SPECIFIC');?></td>
			<td>&nbsp;</td>
			<td nowrap>2-10;4-12;</td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_PER_CUSTOMER');?></td>
			<td NOWRAP><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE');?></td>
			<td>1</td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_OVERALL');?></td>
			<td>100</td>
			<td>20100510 022305</td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_SHOPPER_GROUP');?></td>
			<td>1</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_CATEGORY');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE');?></td>
			<td class="na"></td>
			<td>25,8,44</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td><?php echo JText::_('COM_AWOCOUPON_GBL_YES');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_GBL_NO');?></td>
			<td>my admin note</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
		</tr>
		<tr class="columndata">
			<td>3</td>
			<td>ZJ72M</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PUBLISHED');?></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_SHIPPING');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PERCENTAGE');?></td>
			<td>&nbsp;</td>
			<td>100</td>
			<td>&nbsp;</td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_GBL_TOTAL');?></td>
			<td>100</td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_SPECIFIC');?></td>
			<td>50</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="na"></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_INCLUDE');?></td>
			<td class="na"></td>
			<td >7,24,3</td>
			<td align="center"></td>
			<td align="center"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td>&nbsp;</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
		</tr>
		<tr class="columndata">
			<td>4</td>
			<td>MBSI97</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PUBLISHED');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_GC_GIFTCERT');?></td>
			<td class="na"></td>
			<td class="na"></td>
			<td>50</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td>20100110</td>
			<td>20100810 160000</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td><?php echo JText::_('COM_AWOCOUPON_GBL_YES');?></td>
			<td>&nbsp;</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
		</tr>
		<tr class="columndata">
			<td>5</td>
			<td>S4a2Bp</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PUBLISHED');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PARENT');?></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td align="center">7,24,56,23</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td>note for parent coupon</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PARENT_ALL');?></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
		</tr>
		<tr class="columndata">
			<td>6</td>
			<td>SHIP283</td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PUBLISHED');?></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_SHIPPING');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_PERCENTAGE');?></td>
			<td><?php echo JText::_('COM_AWOCOUPON_CP_SPECIFIC');?></td>
			<td>100</td>
			<td>&nbsp;</td>
			<td nowrap></td>
			<td></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_OVERALL');?></td>
			<td>50</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="na"></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_INCLUDE');?></td>
			<td class="na"></td>
			<td ></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_SHIPPING');?></td>
			<td nowrap><?php echo JText::_('COM_AWOCOUPON_CP_EXCLUDE');?></td>
			<td class="na"></td>
			<td>2,3,4,5</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
			<td class="na"></td>
		</tr>
		<tr class="columndata"><td colspan="12" style="border:0;">......</td></tr>
		</table>
	</div>
</fieldset>










<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>
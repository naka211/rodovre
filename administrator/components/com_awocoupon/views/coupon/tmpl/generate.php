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

function submitbutton(pressbutton) {
	if (pressbutton == 'cancelcoupon') {
		submitform( pressbutton );
		return;
	}
	else if(pressbutton == 'generatecoupons') {
		var form = document.adminForm;

		var err = '';
			
		if(form.template.value == '' || !isUnsignedInteger(form.template.value)) err += '\n<?php echo JText::_('COM_AWOCOUPON_CP_TEMPLATE').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION'); ?>';
		if(form.number.value=='' || !isUnsignedInteger(form.number.value) || form.number.value<1) err += '\n<?php echo JText::_('COM_AWOCOUPON_GBL_NUMBER').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE'); ?>';
			

		if(err != '') alert(err);
		else { submitform( pressbutton ); }
		return; 
	}

}

//-->
</script>


<br />
<br />

<form action="index.php" method="post" id="adminForm" name="adminForm" onsubmit="return submitbutton()">

<div class="width-100">
	<fieldset>
		<table class="admintable">
			<tr>
				<td class="key" nowrap><label><?php echo JText::_('COM_AWOCOUPON_SELECT_TEMPLATE'); ?></label></td>
				<td><?php echo $this->templatelist; ?></td>
			</tr>
			<tr>
				<td class="key" nowrap><label><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUMBER' ); ?></label></td>
				<td><input class="inputbox" type="text" name="number" size="20" maxlength="10" value="" /></td>
			</tr>
		</table>
	</fieldset>
</div>


<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="coupons" />
<input type="hidden" name="mask" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>
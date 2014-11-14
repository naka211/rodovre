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
var str_confirm = '<?php echo addslashes(JText::_( 'COM_AWOCOUPON_ERR_CONFIRM_DELETE' )); ?>';

function submitbutton(pressbutton) {

	var form = document.adminForm;

	if (pressbutton != 'licensedelete') submitform( pressbutton );
	else if(confirm(str_confirm)) submitform( pressbutton );

	return;
}
</script>
<style>
table.admintable td.key { white-space:nowrap; width:auto; }
table.admintable td.key2 { background-color:#ffffff;}
table.admintable td input.readonly, table.admintable td textarea.readonly  { background-color:#ffffff; }
</style>

<form action="index.php" method="post" id="adminForm" name="adminForm">

	<div class="width-100">
		<fieldset><?php if($this->rows->ispermanent=='yes') { ?><legend><?php echo JText::_( 'COM_AWOCOUPON_LI_PERMANENT' ); ?></legend><?php } ?>
					
			<table class="admintable">
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_LI_WEBSITE' ); ?></label></td>
				<td><input type="text" size="75" name="website" value="<?php echo $this->rows->website; ?>" <?php if($this->rows->ispermanent=='yes') echo 'READONLY DISABLED class="readonly"'; ?>></td>
			</tr>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_LI_LICENSE' ); ?></label></td>
				<td><input type="text" size="75" name="license" value="<?php echo $this->rows->license; ?>" <?php if($this->rows->ispermanent=='yes') echo 'READONLY DISABLED class="readonly"'; ?>></td>
			</tr>
			<?php if(!empty($this->rows->license)) { ?>
			<tr valign="top"><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_LI_LOCAL_KEY' ); ?></label></td>
				<td><textarea rows=20 cols=100 name="local_key" <?php if($this->rows->ispermanent=='yes') echo 'READONLY DISABLED class="readonly"'; ?>><?php echo $this->rows->local_key; ?></textarea></td>
			</tr>
			<?php } ?>
			</table>
			
		</fieldset>
	</div>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="license" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

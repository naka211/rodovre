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
var $j = jQuery.noConflict();  // added so jquery does not conflict with mootools

$j(document).ready(function() {
	message_type_change(true);
	
	var form = document.adminForm;
	
	<?php if(!empty($this->row->couponcode_font_color)) { ?>form.couponcode_font_color.value = '<?php echo $this->row->couponcode_font_color;?>';<?php } ?>
	<?php if(!empty($this->row->couponvalue_font_color)) { ?>form.couponvalue_font_color.value = '<?php echo $this->row->couponvalue_font_color;?>';<?php } ?>
	<?php if(!empty($this->row->expiration_font_color)) { ?>form.expiration_font_color.value = '<?php echo $this->row->expiration_font_color;?>';<?php } ?>
	<?php if(!empty($this->row->freetext1_font_color)) { ?>form.freetext1_font_color.value = '<?php echo $this->row->freetext1_font_color;?>';<?php } ?>
	<?php if(!empty($this->row->freetext2_font_color)) { ?>form.freetext2_font_color.value = '<?php echo $this->row->freetext2_font_color;?>';<?php } ?>
	<?php if(!empty($this->row->freetext3_font_color)) { ?>form.freetext3_font_color.value = '<?php echo $this->row->freetext3_font_color;?>';<?php } ?>
	<?php 
	foreach($this->row->imgplugin as $k=>$r) {
		foreach($r as $k2=>$r2) {
			if(!empty($r2->font)) echo 'form.elements["imgplugin['.$k.']['.$k2.'][font]"].value = "'.addslashes($r2->font).'";';
			if(!empty($r2->font_color)) echo 'form.elements["imgplugin['.$k.']['.$k2.'][font_color]"].value = "'.addslashes($r2->font_color).'";';
			if(!empty($r2->text)) echo 'form.elements["imgplugin['.$k.']['.$k2.'][text]"].value = "'.addslashes($r2->text).'";';
			if(!empty($r2->align)) echo 'form.elements["imgplugin['.$k.']['.$k2.'][align]"].value = "'.addslashes($r2->align).'";';
		}
	}
	?>

	
	checkimage();

 
});


var str_coupon_code = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_COUPON_CODE')); ?>';
var str_coupon_value = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_VALUE')); ?>';
var str_expiration = '<?php echo addslashes(JText::_('COM_AWOCOUPON_CP_EXPIRATION')); ?>';
var str_free_text = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_FREE_TEXT')); ?>';
var str_padding = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_PADDING')); ?>';
var str_y_axis = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_YAXIS')); ?>';
var str_font_size = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_FONT_SIZE')); ?>';
var str_message_type = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_MESSAGE_TYPE')); ?>';
var str_image = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_IMAGE')); ?>';
var str_font = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_FONT')); ?>';
var str_title = '<?php echo addslashes(JText::_('COM_AWOCOUPON_PF_TITLE')); ?>';

var str_int_error = '<?php echo addslashes(JText::_('COM_AWOCOUPON_ERR_POSITIVE_INT_REQUIRED')); ?>';
var str_selection_error = '<?php echo addslashes(JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION')); ?>';
var str_input_error = '<?php echo addslashes(JText::_('COM_AWOCOUPON_ERR_ENTER_INPUT')); ?>';


function submitbutton(pressbutton) {
	var form = document.adminForm;

	if (pressbutton == 'CANCELprofile') {
		submitform( pressbutton );
		return;
	}

	else if (pressbutton == 'SAVEprofile' || pressbutton == 'previewprofile') {
		var err = '';
		
		if($j.trim(form.title.value) == '') err += '\n'+str_title+': '+str_input_error;
		if(form.message_type.value=='') err += '\n'+str_message_type+': '+str_selection_error;
		else {
			if(form.message_type.value == 'text');
			else if(form.message_type.value == 'html') {
			
				//if(form.image.value=='') err += '\n'+str_image+': '+str_selection_error;
				
				if(form.image.selectedIndex != 0) {

				
					if(form.couponcode_align.value!='C' && (!isUnsignedInteger($j.trim(form.couponcode_padding.value)) || $j.trim(form.couponcode_padding.value)<1)) err += '\n'+str_coupon_code+'=>'+str_padding+': '+str_int_error;
					if(!isUnsignedInteger($j.trim(form.couponcode_y.value)) || $j.trim(form.couponcode_y.value)<1) err += '\n'+str_coupon_code+'=>'+str_y_axis+': '+str_int_error;
					if(form.couponcode_font.value=='') err += '\n'+str_coupon_code+'/'+str_font+': '+str_selection_error;
					if(!isUnsignedInteger($j.trim(form.couponcode_font_size.value)) || $j.trim(form.couponcode_font_size.value)<1) err += '\n'+str_coupon_code+'=>'+str_font_size+': '+str_int_error;

					if(form.couponvalue_align.value!='C' && (!isUnsignedInteger($j.trim(form.couponvalue_padding.value)) || $j.trim(form.couponvalue_padding.value)<1)) err += '\n'+str_coupon_value+'=>'+str_padding+': '+str_int_error;
					if(!isUnsignedInteger($j.trim(form.couponvalue_y.value)) || $j.trim(form.couponvalue_y.value)<1) err += '\n'+str_coupon_value+'=>'+str_y_axis+': '+str_int_error;
					if(form.couponvalue_font.value=='') err += '\n'+str_coupon_value+'/'+str_font+': '+str_selection_error;
					if(!isUnsignedInteger($j.trim(form.couponvalue_font_size.value)) || $j.trim(form.couponvalue_font_size.value)<1) err += '\n'+str_coupon_value+'=>'+str_font_size+': '+str_int_error;
					
					if($j.trim(form.expiration_text.value)!='') {
						if(form.expiration_align.value!='C' && (!isUnsignedInteger($j.trim(form.expiration_padding.value)) || $j.trim(form.expiration_padding.value)<1)) err += '\n'+str_expiration+'=>'+str_padding+': '+str_int_error;
						if(!isUnsignedInteger($j.trim(form.expiration_y.value)) || $j.trim(form.expiration_y.value)<1) err += '\n'+str_expiration+'=>'+str_y_axis+': '+str_int_error;
						if(form.expiration_font.value=='') err += '\n'+str_expiration+'=>'+str_font+': '+str_selection_error;
						if(!isUnsignedInteger($j.trim(form.expiration_font_size.value)) || $j.trim(form.expiration_font_size.value)<1) err += '\n'+str_expiration+'=>'+str_font_size+': '+str_int_error;
					}
					
					if($j.trim(form.freetext1_text.value)!='') {
						if(form.freetext1_align.value!='C' && (!isUnsignedInteger($j.trim(form.freetext1_padding.value)) || $j.trim(form.freetext1_padding.value)<1)) err += '\n'+str_free_text+' 1=>'+str_padding+': '+str_int_error;
						if(!isUnsignedInteger($j.trim(form.freetext1_y.value)) || $j.trim(form.freetext1_y.value)<1) err += '\n'+str_free_text+' 1=>'+str_y_axis+': '+str_int_error;
						if(form.freetext1_font.value=='') err += '\n'+str_free_text+' 1=>'+str_font+': '+str_selection_error;
						if(!isUnsignedInteger($j.trim(form.freetext1_font_size.value)) || $j.trim(form.freetext1_font_size.value)<1) err += '\n'+str_free_text+' 1=>'+str_font_size+': '+str_int_error;
					}
					
					if($j.trim(form.freetext2_text.value)!='') {
						if(form.freetext2_align.value!='C' && (!isUnsignedInteger($j.trim(form.freetext2_padding.value)) || $j.trim(form.freetext2_padding.value)<1)) err += '\n'+str_free_text+' 2=>'+str_padding+': '+str_int_error;
						if(!isUnsignedInteger($j.trim(form.freetext2_y.value)) || $j.trim(form.freetext2_y.value)<1) err += '\n'+str_free_text+' 2=>'+str_y_axis+': '+str_int_error;
						if(form.freetext2_font.value=='') err += '\n'+str_free_text+' 2=>'+str_font+': '+str_selection_error;
						if(!isUnsignedInteger($j.trim(form.freetext2_font_size.value)) || $j.trim(form.freetext2_font_size.value)<1) err += '\n'+str_free_text+' 2=>'+str_font_size+': '+str_int_error;
					}
					
					if($j.trim(form.freetext3_text.value)!='') {
						if(form.freetext3_align.value!='C' && (!isUnsignedInteger($j.trim(form.freetext3_padding.value)) || $j.trim(form.freetext3_padding.value)<1)) err += '\n'+str_free_text+' 3=>'+str_padding+': '+str_int_error;
						if(!isUnsignedInteger($j.trim(form.freetext3_y.value)) || $j.trim(form.freetext3_y.value)<1) err += '\n'+str_free_text+' 3=>'+str_y_axis+': '+str_int_error;
						if(form.freetext3_font.value=='') err += '\n'+str_free_text+' 3=>'+str_font+': '+str_selection_error;
						if(!isUnsignedInteger($j.trim(form.freetext3_font_size.value)) || $j.trim(form.freetext3_font_size.value)<1) err += '\n'+str_free_text+' 3=>'+str_font_size+': '+str_int_error;
					}
					<?php 
					foreach($this->row->imgplugin as $k=>$r) { foreach($r as $k2=>$r2) {
						$jstitle = addslashes(@$r2->title);
					?>
						if($j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][text]"].value)!='') {
							if(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][align]"].value!='C' && (!isUnsignedInteger($j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][padding]"].value)) || $j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][padding]"].value)<1)) err += '\n<?php echo $jstitle; ?>=>'+str_padding+': '+str_int_error;
							if(!isUnsignedInteger($j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][y]"].value)) || $j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][y]"].value)<1) err += '\n<?php echo $jstitle; ?>=>'+str_y_axis+': '+str_int_error;
							<?php if(empty($r2->is_ignore_font)) { ?>if(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font]"].value=='') err += '\n<?php echo $jstitle; ?>=>'+str_font+': '+str_selection_error;<?php } ?>
							<?php if(empty($r2->is_ignore_font_size)) { ?>if(!isUnsignedInteger($j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font_size]"].value)) || $j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font_size]"].value)<1) err += '\n<?php echo $jstitle; ?>=>'+str_font_size+': '+str_int_error;<?php } ?>
						}
					<?php }} ?>
				
				}
			}
		}
		if(err != '') {
			alert(err);
			return false;
		}

		<?php echo $this->editor->save( 'email_html' ); ?>
		if (pressbutton == 'SAVEprofile') submitform( pressbutton );
		
		

	}
	return true;

}






function resetall() {
	var form = document.adminForm;
	form.image.selectedIndex = 0;
	
	form.couponcode_padding.value = '';
	form.couponcode_y.value = '';
	form.couponcode_font_size.value = '';
	form.couponcode_align.selectedIndex = 0; 
	form.couponcode_font.selectedIndex = 0; 
	form.couponcode_font_color.selectedIndex = 0; 
	
	form.couponvalue_padding.value = '';
	form.couponvalue_y.value = '';
	form.couponvalue_font_size.value = '';
	form.couponvalue_align.selectedIndex = 0; 
	form.couponvalue_font.selectedIndex = 0; 
	form.couponvalue_font_color.selectedIndex = 0; 
	
	form.expiration_text.value = '';
	form.expiration_padding.value = '';
	form.expiration_y.value = '';
	form.expiration_font_size.value = '';
	form.expiration_align.selectedIndex = 0; 
	form.expiration_font.selectedIndex = 0; 
	form.expiration_font_color.selectedIndex = 0; 
	
	form.freetext1_text.value = '';
	form.freetext1_padding.value = '';
	form.freetext1_y.value = '';
	form.freetext1_font_size.value = '';
	form.freetext1_align.selectedIndex = 0; 
	form.freetext1_font.selectedIndex = 0; 
	form.freetext1_font_color.selectedIndex = 0; 
	
	form.freetext2_text.value = '';
	form.freetext2_padding.value = '';
	form.freetext2_y.value = '';
	form.freetext2_font_size.value = '';
	form.freetext2_align.selectedIndex = 0; 
	form.freetext2_font.selectedIndex = 0; 
	form.freetext2_font_color.selectedIndex = 0; 
	
	form.freetext3_text.value = '';
	form.freetext3_padding.value = '';
	form.freetext3_y.value = '';
	form.freetext3_font_size.value = '';
	form.freetext3_align.selectedIndex = 0; 
	form.freetext3_font.selectedIndex = 0; 
	form.freetext3_font_color.selectedIndex = 0; 
	
	
	<?php foreach($this->row->imgplugin as $k=>$r) { foreach($r as $k2=>$r2) { ?>
		form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][text]"].value = '';
		form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][padding]"].value = '';
		form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][y]"].value = '';
		form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font_size]"].value = '';
		form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][align]"].selectedIndex = 0; 
		form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font]"].selectedIndex = 0; 
		form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font_color]"].selectedIndex = 0; 
	<?php }} ?>
	
	form.email_body.value = ''; 
	<?php echo $this->editor->setContent( 'email_html','form.email_body.value' ); ?>
		
	hideall();
}
function hideall() {
	document.getElementById('tbl_html').style.display = 'none';
	document.getElementById('tbl_text').style.display = 'none';
	document.getElementById('tbl_syntax').style.display = 'none';
	
}
function message_type_change(is_edit) {
	var form = document.adminForm;
	
	is_edit = (is_edit == undefined) ? false : is_edit;
	if(!is_edit) resetall();
	else hideall();
	
	if(form.message_type.value == 'text') {
		document.getElementById('tbl_text').style.display = '';
		document.getElementById('tbl_syntax').style.display = '';
	} else if(form.message_type.value == 'html') {
		document.getElementById('tbl_html').style.display = '';
		document.getElementById('tbl_syntax').style.display = '';
	}
}
function generate_preview() {
	if(submitbutton('previewprofile')) {
		var form = document.adminForm;
		var str = '';
		
		str += '&image='+encodeURIComponent(form.image.value);
		str += '&code='+encodeURIComponent(form.couponcode_align.value+'|'+form.couponcode_padding.value+'|'+form.couponcode_y.value+'|'+form.couponcode_font.value+'|'+form.couponcode_font_size.value+'|'+form.couponcode_font_color.value);
		str += '&value='+encodeURIComponent(form.couponvalue_align.value+'|'+form.couponvalue_padding.value+'|'+form.couponvalue_y.value+'|'+form.couponvalue_font.value+'|'+form.couponvalue_font_size.value+'|'+form.couponvalue_font_color.value);
		if($j.trim(form.expiration_text.value)!='')
			str += '&expiration='+encodeURIComponent(form.expiration_text.value+'|'+form.expiration_align.value+'|'+form.expiration_padding.value+'|'+form.expiration_y.value+'|'+form.expiration_font.value+'|'+form.expiration_font_size.value+'|'+form.expiration_font_color.value);
		if($j.trim(form.freetext1_text.value)!='')
			str += '&freetext1='+encodeURIComponent(form.freetext1_text.value+'|'+form.freetext1_align.value+'|'+form.freetext1_padding.value+'|'+form.freetext1_y.value+'|'+form.freetext1_font.value+'|'+form.freetext1_font_size.value+'|'+form.freetext1_font_color.value);
		if($j.trim(form.freetext2_text.value)!='')
			str += '&freetext2='+encodeURIComponent(form.freetext2_text.value+'|'+form.freetext2_align.value+'|'+form.freetext2_padding.value+'|'+form.freetext2_y.value+'|'+form.freetext2_font.value+'|'+form.freetext2_font_size.value+'|'+form.freetext2_font_color.value);
		if($j.trim(form.freetext3_text.value)!='')
			str += '&freetext3='+encodeURIComponent(form.freetext3_text.value+'|'+form.freetext3_align.value+'|'+form.freetext3_padding.value+'|'+form.freetext3_y.value+'|'+form.freetext3_font.value+'|'+form.freetext3_font_size.value+'|'+form.freetext3_font_color.value);
		<?php foreach($this->row->imgplugin as $k=>$r) {  foreach($r as $k2=>$r2) { ?>
			if($j.trim(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][text]"].value)!='')
				str += '&imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>]='+encodeURIComponent(form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][text]"].value+'|'+form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][align]"].value+'|'+form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][padding]"].value+'|'+form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][y]"].value+'|'+form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font]"].value+'|'+form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font_size]"].value+'|'+form.elements["imgplugin[<?php echo $k; ?>][<?php echo $k2; ?>][font_color]"].value);
		<?php } } ?>

//		rel="{handler: \'iframe\', size: {x: 580, y: 590}}">
		
		SqueezeBox.setContent('iframe','index.php?option=com_awocoupon&view=profile&task=previewprofileEdit'+str);
	}

}

function checkimage() {
	elem = document.adminForm.image;
	if(elem.selectedIndex == 0) document.getElementById('image_properties').style.display = 'none';
	else document.getElementById('image_properties').style.display = '';
}
//-->
</script>

<style>
table.admintable td.top { width:auto; text-align:center; }
</style>



<form action="index.php" method="post" id="adminForm" name="adminForm">

<table bgcolor="#ffffff"><tr valign="top"><td>
<table class="admintable">
	<tr><td class="key key2"><label><?php echo JText::_( 'COM_AWOCOUPON_PF_TITLE' ); ?></label></td>
		<td><input type="text" size="60" name="title" value="<?php echo $this->row->title; ?>" maxlength="255"></td>
	</tr>
	<tr><td class="key key2"><label><?php echo JText::_( 'COM_AWOCOUPON_PF_FROM_NAME' ); ?></label></td>
		<td><input type="text" size="60" name="from_name" value="<?php echo $this->row->from_name; ?>" maxlength="255"></td>
	</tr>
	<tr><td class="key key2"><label><?php echo JText::_( 'COM_AWOCOUPON_PF_FROM_EMAIL' ); ?></label></td>
		<td><input type="text" size="60" name="from_email" value="<?php echo $this->row->from_email; ?>" maxlength="255"></td>
	</tr>
	<tr><td class="key key2"><label>
			<img src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/icon-multilingual.jpg" height="20"/>
			<?php echo JText::_( 'COM_AWOCOUPON_PF_EMAIL_SUBJECT' ); ?></label></td>
		<td><input type="text" size="60" name="email_subject" value="<?php echo $this->row->email_subject; ?>" maxlength="255"></td>
	</tr>
	<tr><td class="key key2"><label><?php echo JText::_( 'COM_AWOCOUPON_PF_BCC_ADMIN' ); ?></label></td>
		<td><input type="checkbox" size="60" name="bcc_admin" <?php if(!empty($this->row->bcc_admin)) echo 'CHECKED'; ?> value="1"></td>
	</tr>
	<tr><td class="key key2"><label><?php echo JText::_( 'COM_AWOCOUPON_PF_MESSAGE_TYPE' ); ?></label></td>
		<td><?php echo $this->lists['message_type'] ?></td>
	</tr>
</table>

</td><td>

<table class="admintable" id="tbl_text" style="display:none;">
	<tr valign="top">
		<td class="key key2">
			<label>
				<img src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/icon-multilingual.jpg" height="20"/>
				<?php echo JText::_( 'COM_AWOCOUPON_PF_EMAIL_BODY' ); ?>
			</label>
		</td>
		<td><textarea cols="45" rows="10" name="email_body" ><?php echo $this->row->email_body;?></textarea></td>
	</tr>
</table>

<table class="admintable" id="tbl_html" style="display:none;">
<tr><td class="key" colspan="2"><?php echo JText::_( 'COM_AWOCOUPON_PF_IMAGE' ); ?> &nbsp; &nbsp;<?php echo $this->lists['image']; ?>
	&nbsp;&nbsp;&nbsp;<input type="button" onclick="generate_preview();" value="<?php echo JText::_( 'COM_AWOCOUPON_PF_PREVIEW' ); ?>"></td></tr>
<tr><td colspan="2">
<table bgcolor="#ffffff" id="image_properties" style="display:none;">
<tr>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_GBL_DESCRIPTION' ); ?></td>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_PF_TEXT' ); ?></td>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_PF_ALIGN' ); ?></td>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_PF_PADDING' ); ?></td>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_PF_YAXIS' ); ?></td>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_PF_FONT' ); ?></td>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_PF_FONT_SIZE' ); ?></td>
	<td class="key top"><?php echo JText::_( 'COM_AWOCOUPON_PF_FONT_COLOR' ); ?></td>
</tr>
<tr><td class="key"><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></td>
	<td>---</td>
	<td><?php echo $this->lists['couponcode_align']; ?></td>
	<td><input type="text" size="5" name="couponcode_padding" value="<?php echo $this->row->couponcode_padding; ?>"></td>
	<td><input type="text" size="5" name="couponcode_y" value="<?php echo $this->row->couponcode_y; ?>"></td>
	<td><?php echo $this->lists['couponcode_font']; ?></td>
	<td><input type="text" size="5" name="couponcode_font_size" value="<?php echo $this->row->couponcode_font_size; ?>"></td>
	<td><?php echo $this->lists['couponcode_font_color']; ?></td>
</tr>
<tr><td class="key"><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></td>
	<td>---</td>
	<td><?php echo $this->lists['couponvalue_align']; ?></td>
	<td><input type="text" size="5" name="couponvalue_padding" value="<?php echo $this->row->couponvalue_padding; ?>"></td>
	<td><input type="text" size="5" name="couponvalue_y" value="<?php echo $this->row->couponvalue_y; ?>"></td>
	<td><?php echo $this->lists['couponvalue_font']; ?></td>
	<td><input type="text" size="5" name="couponvalue_font_size" value="<?php echo $this->row->couponvalue_font_size; ?>"></td>
	<td><?php echo $this->lists['couponvalue_font_color']; ?></td>
</tr>
<tr><td class="key"><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ); ?></td>
	<td><?php echo $this->lists['expiration_text']; ?></td>
	<td><?php echo $this->lists['expiration_align']; ?></td>
	<td><input type="text" size="5" name="expiration_padding" value="<?php echo $this->row->expiration_padding; ?>"></td>
	<td><input type="text" size="5" name="expiration_y" value="<?php echo $this->row->expiration_y; ?>"></td>
	<td><?php echo $this->lists['expiration_font']; ?></td>
	<td><input type="text" size="5" name="expiration_font_size" value="<?php echo $this->row->expiration_font_size; ?>"></td>
	<td><?php echo $this->lists['expiration_font_color']; ?></td>
</tr>
<tr><td class="key"><?php echo JText::_( 'COM_AWOCOUPON_PF_FREE_TEXT' ); ?> 1</td>
	<td><input type="text"  name="freetext1_text" value="<?php echo $this->row->freetext1_text; ?>"></td>
	<td><?php echo $this->lists['freetext1_align']; ?></td>
	<td><input type="text" size="5" name="freetext1_padding" value="<?php echo $this->row->freetext1_padding; ?>"></td>
	<td><input type="text" size="5" name="freetext1_y" value="<?php echo $this->row->freetext1_y; ?>"></td>
	<td><?php echo $this->lists['freetext1_font']; ?></td>
	<td><input type="text" size="5" name="freetext1_font_size" value="<?php echo $this->row->freetext1_font_size; ?>"></td>
	<td><?php echo $this->lists['freetext1_font_color']; ?></td>
</tr>
<tr><td class="key"><?php echo JText::_( 'COM_AWOCOUPON_PF_FREE_TEXT' ); ?> 2</td>
	<td><input type="text"  name="freetext2_text" value="<?php echo $this->row->freetext2_text; ?>"></td>
	<td><?php echo $this->lists['freetext2_align']; ?></td>
	<td><input type="text" size="5" name="freetext2_padding" value="<?php echo $this->row->freetext2_padding; ?>"></td>
	<td><input type="text" size="5" name="freetext2_y" value="<?php echo $this->row->freetext2_y; ?>"></td>
	<td><?php echo $this->lists['freetext2_font']; ?></td>
	<td><input type="text" size="5" name="freetext2_font_size" value="<?php echo $this->row->freetext2_font_size; ?>"></td>
	<td><?php echo $this->lists['freetext2_font_color']; ?></td>
</tr>
<tr><td class="key"><?php echo JText::_( 'COM_AWOCOUPON_PF_FREE_TEXT' ); ?> 3</td>
	<td><input type="text"  name="freetext3_text" value="<?php echo $this->row->freetext3_text; ?>"></td>
	<td><?php echo $this->lists['freetext3_align']; ?></td>
	<td><input type="text" size="5" name="freetext3_padding" value="<?php echo $this->row->freetext3_padding; ?>"></td>
	<td><input type="text" size="5" name="freetext3_y" value="<?php echo $this->row->freetext3_y; ?>"></td>
	<td><?php echo $this->lists['freetext3_font']; ?></td>
	<td><input type="text" size="5" name="freetext3_font_size" value="<?php echo $this->row->freetext3_font_size; ?>"></td>
	<td><?php echo $this->lists['freetext3_font_color']; ?></td>
</tr>
<?php
foreach($this->row->imgplugin as $k=>$r) {foreach($r as $k2=>$r2) {
?>
	<tr><td class="key"><?php echo @$r2->title.' <input type="hidden" name="imgplugin['.$k.']['.$k2.'][title]" value="'.@$r2->title.'" />'; ?></td>
		<td><?php echo str_replace('{text_name}','imgplugin['.$k.']['.$k2.'][text]',@$r2->text_html).' <input type="hidden" name="imgplugin['.$k.']['.$k2.'][text_html]" value="'.htmlentities(@$r2->text_html).'" />'; ?></td>
		<td><?php echo $this->lists[$k.'_'.$k2.'_align']; ?></td>
		<td><input type="text" size="5" name="imgplugin[<?php echo $k;?>][<?php echo $k2;?>][padding]" value="<?php echo @$r2->padding; ?>"></td>
		<td><input type="text" size="5" name="imgplugin[<?php echo $k;?>][<?php echo $k2;?>][y]" value="<?php echo $r2->y; ?>"></td>
		<td><?php echo $this->lists[$k.'_'.$k2.'_font']; ?><?php if(!empty($r2->is_ignore_font)) echo '<input type="hidden" name="imgplugin['.$k.']['.$k2.'][is_ignore_font]" value="1" />'; ?></td>
		<td><input type="text" size="5" name="imgplugin[<?php echo $k;?>][<?php echo $k2;?>][font_size]" value="<?php echo @$r2->font_size; ?>"><?php if(!empty($r2->is_ignore_font_size)) echo '<input type="hidden" name="imgplugin['.$k.']['.$k2.'][is_ignore_font_size]" value="1" />'; ?></td>
		<td><?php echo $this->lists[$k.'_'.$k2.'_font_color']; ?><?php if(!empty($r2->is_ignore_font_color)) echo '<input type="hidden" name="imgplugin['.$k.']['.$k2.'][is_ignore_font_color]" value="1" />'; ?></td>
	</tr>

<?php
} }
?>
</table>
</td></tr>
<tr valign="top">
	<td><?php echo $this->editor->display( 'email_html',  $this->row->email_body , '650', '550', '75', '20' ) ; ?></td>
	<td>
		<img src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/icon-multilingual.jpg" height="20"/>
		<br /><br /><br /><br />
	
	<?php 
	$pn = 1;
	echo awoJHtmlSliders::start('extra_options', array('startOffset'=>0,'startTransition'=>-1));
	echo awoJHtmlSliders::panel(JText::_('COM_AWOCOUPON_GC_GIFTCERTS'), 'pn_'.$pn);
	?>
	<div style="padding:10px;">
		<div><b><?php echo JText::_( 'TAGS' ); ?></b></div>
		<div>{store_name}</div>
		<div>{siteurl}</div>
		<div>{vouchers}</div>
		<div>{image_embed}</div>
		<div>{purchaser_username}</div>
		<div>{purchaser_first_name}</div>
		<div>{purchaser_last_name}</div>
		<div>{recipient_name}</div>
		<div>{recipient_email}</div>
		<div>{recipient_message}</div>
		<div>{today_date}</div>
		<div>{order_id}</div>
		<div>{order_number}</div>
		<div>{order_status}</div>
		<div>{order_date}</div>
		<div>{order_link}</div>
		<div>{order_total}</div>
		<div>{product_name}</div>
	</div>
	<?php
	if(!empty($this->tagdesc)) {
		foreach($this->tagdesc as $t) {
			$pn++;
			echo awoJHtmlSliders::panel($t['title'], 'pn_'.$pn).'<div style="padding:10px;">'.$t['description'].'</div>';
		}
	}

	echo awoJHtmlSliders::end();
	?>

</td></tr>

</table>


<table id="tbl_syntax" style="display:none;">
	<tr><td class="key">
			<img src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/icon-multilingual.jpg" height="20"/>
			<label>{vouchers}</label>
		</td>
		<td><input type="text" name="voucher_text" value="<?php echo $this->row->voucher_text; ?>" size="65"></td>
	</tr>
	<tr><td class="key">
			<img src="<?php echo JURI::root(true); ?>/administrator/components/com_awocoupon/assets/images/icon-multilingual.jpg" height="20"/>
			<label>{expiration_text}</label>
		</td>
		<td><input type="text" name="voucher_exp_text" value="<?php echo $this->row->voucher_exp_text; ?>" size="65"></td>
	</tr>
</table>


</td></tr></table>













<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="view" value="profile" />
<input type="hidden" name="layout" value="edit" />
<input type="hidden" name="cid[]" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="mask" value="0" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>
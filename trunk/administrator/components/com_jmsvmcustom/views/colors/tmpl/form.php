<?php 
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

defined('_JEXEC') or die('Restricted access');
$row = $this->row; 
$this->_toolbarEdit();
?>
<script type="text/javascript" src="<?php echo JURI::root();?>administrator/components/com_jmsvmcustom/assets/script.js"></script>
<script language="javascript" type="text/javascript">	
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel') {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		if (task == 'save' || task == 'apply') {
			
			var form = document.adminForm;
			if (form.color_title.value == ""){
				alert( "<?php echo JText::_( 'PLEASE_ENTER_TITLE', true ); ?>" );
			} else {
	  			Joomla.submitform(task, document.getElementById('adminForm'));
	  		}
			
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'DETAILS' ); ?></legend>
		<table class="admintable">
		<tr>
			<td  align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'COLOR_TITLE' ); ?>:
				</label>
			</td>
			<td>
			 	<input class="inputbox" type="text" name="color_title" maxlength="50" size="50" value="<?php echo $row->color_title; ?>" />
			</td>
		</tr>		
		<tr>
			<td  align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'COLOR_ICON' ); ?>:
				</label>
			</td>
			<td>
			 	<?php echo $this->lists['color_icon']?>			 	
			</td>
		</tr>
		<tr>
			<td  align="right" class="key">				
			</td>
			<td>			 	
			 	<div id="color-icon-preview">
			 		<?php if($row->color_icon) {?>
			 		<img src="<?php echo JURI::Root();?>administrator/components/com_jmsvmcustom/assets/color_icons/<?php echo $row->color_icon;?>" />
			 		<?php } ?>
			 	</div>
			</td>
		</tr>
		<tr>
			<td  align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'Published' ); ?>:
				</label>
			</td>
			<td>
			 	<?php echo $this->lists['published']?>
			</td>
		</tr>
	</table>
	</fieldset>
<div class="clr"></div>
<input type="hidden" name="option" value="com_jmsvmcustom" />
<input type="hidden" name="controller" value="colors" />
<input type="hidden" name="ordering" value="<?php echo $row->ordering?>" />
<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
<input type="hidden" id="site_url" name="site_url" value="<?php echo JURI::root();?>" />
<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

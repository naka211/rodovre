<?php 
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

defined('_JEXEC') or die('Restricted access');
$colors = $this->colors; 
$this->_toolbarEdit();
JHTML::_('behavior.tooltip');
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();
$media_product_path = VmConfig::get('media_product_path'); 
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
	  		Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'DETAILS' ); ?></legend>
		<table class="admintable">
		<?php for($i=0;$i<count($colors);$i++) {
			$color = $colors[$i];
		?>
		<tr class="colorbox">
			<td  align="right" class="key">
				<label for="title">
					<?php echo JText::_( 'COLOR' ); ?> : <?php echo $color->color_title;?>
				</label>	
				<?php if($color->color_icon)?>
					<img width="16px" height="16px" src="<?php echo JURI::root()?>administrator/components/com_jmsvmcustom/assets/color_icons/<?php echo $color->color_icon;?>" />
					<input type="hidden" name="color_ids[]" value="<?php echo $color->id;?>" />	
					<input type="hidden" name="product_color_ids[]" value="<?php if(isset($color->product_color_id)) echo $color->product_color_id;?>" />				
			</td>
			<td>
				<table class="admintable">
					<tr>
						<td  align="right" class="key">
						 	<label for="title">
								<?php echo JText::_( 'PRICE' ); ?>
							</label>
						</td>
						<td>
							<input type="text" name="price<?php echo $color->id;?>" value="<?php if(isset($color->price)) echo $color->price;?>" />
							<?php echo $this->currency->getSymbol();?>	
						</td>	
					</tr>
					<tr>
						<td  align="right" class="key">
						 	<label for="title">
								<?php echo JText::_( 'IMAGES' ); ?>
							</label>
						</td>
						<td id="imgs-box<?php echo $color->id;?>">
							<?php echo $this->imgslist;?><a class="add-img" rel="<?php echo $color->id;?>"><?php echo JText::_( 'ADD_IMAGE' ); ?></a>	
						</td>	
					</tr>
					<tr>
						<td  align="right" class="key">						 	
						</td>
						<td>
							<ul class="imgs-list" id="imgs-list<?php echo $color->id;?>">
								<?php 
								if(isset($color->color_imgs)) {
									$imgs = explode(",",$color->color_imgs);
									for($k=0;$k<count($imgs);$k++) {
										$img_name = VmcustomHelper::getImageDetail($imgs[$k]);
										if($imgs[$k]) {
									?>
									<li><a class="hasTip" title="&lt;img src=&quot;<?php echo JURI::root().$media_product_path;?><?php echo $img_name;?>&quot; /&gt;"><?php echo $img_name;?></a><a class="delete-img" rel="<?php echo $imgs[$k];?>"></a></li>
									<?php } } ?>
								<?php } ?>								
							</ul>
							<div class="img-preview" id="img-preview<?php echo $color->id;?>">
							</div>
							<input type="hidden" id="color_imgs<?php echo $color->id;?>" name="color_imgs<?php echo $color->id;?>" value="<?php if(isset($color->color_imgs)) echo $color->color_imgs;?>" />	
						</td>	
					</tr>
				</table>	
			</td>
		</tr>
		<?php } ?>		
		
	</table>
	</fieldset>
<div class="clr"></div>
<input type="hidden" name="option" value="com_jmsvmcustom" />
<input type="hidden" name="controller" value="products" />
<input type="hidden" name="product_id" value="<?php echo $this->product_id;?>" />
<input type="hidden" id="mpp" name="mpp" value="<?php echo JURI::root().$media_product_path;?>" />

<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

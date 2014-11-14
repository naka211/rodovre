<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

defined( '_JEXEC' ) or die;
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();
$media_product_path = VmConfig::get('media_product_path'); 
$products = $this->products; 
?>
<script type="text/javascript" src="<?php echo JURI::Root();?>components/com_jmsvmcustom/assets/js/script.js"></script>
<h1><?php echo $this->category->category_name;?></h1>
<p><?php echo $this->category->category_description;?></p>
<div class="browse-view">
<?php
for($i=0;$i<count($products);$i++) {
	$product = $products[$i];
	$colors = $product->colors;	
	if($i%3==0) echo '<div class="row">';
?>
	<div class="product span33" id="product<?php echo $product->virtuemart_product_id;?>">
		<div class="product-image">
			<?php for($k=0;$k<count($colors);$k++) {
				$color = $colors[$k];
				
			?>
			<img width="220px" height="200px" style="<?php if($k==0){?>opacity:1;<?php } else {?>opacity:0;<?php }?>" class="img<?php echo $color->color_id;?>" src="<?php echo JURI::root().$media_product_path.$color->img;?>" />
			<img width="220px" height="200px" style="opacity:0;" class="img<?php echo $color->color_id;?>" src="<?php echo JURI::root().$media_product_path.$color->img2;?>" />
			<?php } ?>
			<input type="hidden" class="current_color" name="current_color" value="<?php echo $colors[0]->color_id?>" />
		</div>
		<div class="product-info">
			<div class="product-name">
				<span><?php echo JHTML::link ($product->link, $product->product_name); ?></span>
				<span>
				<?php
				echo $this->currency->createPriceDiv ('salesPrice', '', $product->prices);
				?>
				</span>
			</div>
			<div class="product-colors">
				<?php for($k=0;$k<count($colors);$k++) {
				$color = $colors[$k];
				
			?>
				<span><img width="16px" rel="<?php echo $color->color_id;?>" data-pid="<?php echo $product->virtuemart_product_id;?>" class="color-icon" src="<?php echo JURI::root().'administrator/components/com_jmsvmcustom/assets/color_icons/'.$color->color_icon;?>" /></span>
			<?php } ?>
			</div>
			
		</div>
	</div>
	<?php 
	if($i%3==2) echo '</div>';
	?>
<?php }
?>
</div>


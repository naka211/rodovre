<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
$app = JFactory::getApplication();
$tmpl = JURI::base().'templates/'.$app->getTemplate()."/";
?>
<div class="eachBox wrap-list-prod">
    <h2>ReLaterede produkter</h2>
        <ul class="listProd clearfix">
<?php
	foreach ($this->product->customfieldsRelatedProducts as $field) {
	$product = $this->product_model->getProduct($field->custom_value);
	$this->product_model->addImages($product);
	$link=JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );
?>
			<li>
				<div class="img_main"> <a href="<?php echo $product->link;?>">
					<?php // Product Image
					if ($product->images) {
						echo $product->images[0]->displayMediaThumb( '', false );
					}
                    ?>
				</a> </div>
				<h3><?php echo $product->product_name?></h3>
				<div class="wrap_price">
					<?php if(!empty($product->prices['discountAmount'])){?>
					<p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
					<p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
					<?php }?>
				</div>
				<h4><?php if (VmConfig::get ( 'show_prices' ) == '1') { echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );}?></h4>
				<a class="btnMore btn2" href="<?php echo $link?>">Vis detaljer</a>
			</li>
		<?php // echo $field->display ?>
	<?php } ?>
        </ul>
</div>

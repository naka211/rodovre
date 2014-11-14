<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JHTML::_( 'behavior.modal' );

$mobile = JURI::base()."templates/scheellarsen/mobile/";
?>

<div id="content" class="w-content"> {module Home Banners}
	<div class="eachBox news"> {article 14}{introtext}{/article} </div>
	<!--discount-stt-->
	<div class="eachBox wrap-list-prod clearfix">
		<h2>udvalgte produkter</h2>
		<ul class="listProd clearfix">
			<?php
			//foreach ($this->products as $type => $productList ) {
				foreach ( $this->products['featured'] as $product ) {
					$link = JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );
			?>
			<li>
				<div class="img_main"> <a href="<?php echo $link;?>">
					<?php // Product Image
                if ($product->images) {
                    echo $product->images[0]->displayMediaThumb( 'border="0"', false, '' );
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
				<h4>
					<?php
					if (VmConfig::get ( 'show_prices' ) == '1') {
						echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
					} ?>
				</h4>
				<a class="btnMore btn2" href="<?php echo $link;?>">Vis detaljer</a> </li>
			<?php 
				}
			//}?>
		</ul>
	</div>
	<!--eachBox wrap-list-prod-->
	
	<div class="eachBox  box_gavekort"> <a href="gavekort.html"><img src="<?php echo $mobile;?>img/gavekort.png"></a> </div>
	<div class="eachBox">
		<ul class="list-bn">
			<li><a href="cane-line.html"><img src="<?php echo $mobile;?>img/image01.jpg"></a></li>
			<li><a href="skind.html"><img src="<?php echo $mobile;?>img/image02.jpg"></a></li>
		</ul>
	</div>
</div>
<?php # Vendor Store Description
/*if (!empty($this->vendor->vendor_store_desc) and VmConfig::get('show_store_desc', 1)){?>
<?php echo $this->vendor->vendor_store_desc; ?>
<?php }*/

# load categories from front_categories if exist
//if ($this->categories and VmConfig::get('show_categories', 1)) echo $this->loadTemplate('categories');

# Show template for : topten,Featured, Latest Products if selected in config BE
//if (!empty($this->products) ) echo $this->loadTemplate('products');
return;
?>
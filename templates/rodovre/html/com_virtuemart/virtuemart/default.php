<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//Detect mobile
$config =& JFactory::getConfig();
$showPhone = $config->getValue( 'config.show_phone' );
$enablePhone = $config->getValue( 'config.enable_phone' );
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
if ( ($showPhone || $detect->isMobile()) && ($enablePhone) ) {
    include('default_mobile.php');
    return;
}
//Detect mobile end

JHTML::_( 'behavior.modal' );
//print_r($this->products);exit;
?>
<?php # Vendor Store Description
/*if (!empty($this->vendor->vendor_store_desc) and VmConfig::get('show_store_desc', 1)){?>
<?php echo $this->vendor->vendor_store_desc; ?>
<?php }*/

# load categories from front_categories if exist
//if ($this->categories and VmConfig::get('show_categories', 1)) echo $this->loadTemplate('categories');

# Show template for : topten,Featured, Latest Products if selected in config BE
//if (!empty($this->products) ) echo $this->loadTemplate('products');
?>
{module Home Banners}
{article 14}{introtext}{/article}
<div class="products">
    <h2><img alt="" src="templates/rodovre/img/title_product.png"></h2>
    <ul class="clearfix">
        <?php
//foreach ($this->products as $type => $productList ) {
// Calculating Products Per Row
/*$products_per_row = VmConfig::get ( 'homepage_products_per_row', 3 ) ;
$cellwidth = ' width'.floor ( 100 / $products_per_row );*/

// Category and Columns Counter

// Start the Output

foreach ( $this->products['featured'] as $product ) { //print_r($product);exit;

	$db = JFactory::getDBO();
	$db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_manufacturer_medias WHERE virtuemart_manufacturer_id = ".$product->virtuemart_manufacturer_id);
	$vmi = $db->loadResult();
	
	if($vmi){
		$db->setQuery("SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = ".$vmi);
		$file_url = $db->loadResult();
	}
	
	$link=JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );

		// Show Products
	// this is an indicator wether a row needs to be opened or not
?>
        <li>
            <div class="img_main">
                <?php // Product Image
					if ($product->images) {
						echo $product->images[0]->displayMediaThumb( 'border="0"', false, '' );
					}
					?>
            </div>
            <h3 style="text-align: center">
                <?php // Product Name
					echo $product->product_name?>
            </h3>
            <?php if(!empty($product->prices['discountAmount'])){?>
            <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
            <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
            <?php }?>
            <h4 class="price_2"><?php
					if (VmConfig::get ( 'show_prices' ) == '1') {
						echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
					} ?>
            </h4>
            <h6 class="w_brand">
				<img src="<?php echo JURI::base().'thumbnail/timthumb.php?src='.$file_url.'&q=100&h=31'; ?>" />
			</h6>
            <div class="pro-larg animated clearfix">
                <div class="img_main"> <a href="<?php echo $link;?>"><?php echo $product->images[0]->displayMediaThumb( 'border="0"', false, '' )?></a> </div>
                <h3><?php echo $product->product_name?></h3>
                <p class="no_number">Vare-nummer: <?php echo $product->product_sku?></p>
                <?php if(!empty($product->prices['discountAmount'])){?>
                <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
                <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
                <?php }?>
                <h4><?php echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );?></h4>
                <a class="btnMore btn2" href="<?php echo $link;?>">Vis detaljer</a>
            </div>            
        </li>
        <?php
    }
?>
    </ul>
</div>

<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

//Detect mobile
$config =& JFactory::getConfig();
$showPhone = $config->getValue( 'config.show_phone' );
$enablePhone = $config->getValue( 'config.enable_phone' );
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
if ( ($showPhone || $detect->isMobile()) && ($enablePhone) ) {
    include('default_relatedproducts_mobile.php');
    return;
}
//Detect mobile end

$app = JFactory::getApplication();
$tmpl = JURI::base().'templates/'.$app->getTemplate()."/";
?>
<div class="related_product">
    <h2><img src="<?php echo $tmpl; ?>img/title_relateproduct.png" alt=""><?php //echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h2>
    <div class="products">
        <ul class="clearfix">
<?php
	foreach ($this->product->customfieldsRelatedProducts as $field) {
	$product = $this->product_model->getProduct($field->custom_value);
	$this->product_model->addImages($product);
	$link=JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );
?>
            <li>
                <div class="img_main">
                    <a href="<?php echo $product->link?>">
                    <?php // Product Image
			if ($product->images) {
				echo $product->images[0]->displayMediaThumb( '', false );
			}
                    ?>
                    </a>
                </div>
                    <h3><?php echo $product->product_name?></h3>
                    <?php if(!empty($product->prices['discountAmount'])){?>
                        <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
                        <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay($product->prices['discountAmount'],0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>)</p>
                    <?php }?>
                    <h4>
                            <?php
                        if (VmConfig::get ( 'show_prices' ) == '1') {
                                echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
                        }?>
                    </h4>
                <div class="pro-larg animated clearfix">
                    <div class="img_main">
                      <a href="<?php echo $product->link?>">
                        <?php // Product Image
                            if ($product->images) {
                                    echo $product->images[0]->displayMediaThumb( '', false );
                            }
                        ?>
                      </a>
                    </div>
                    <h3><?php echo $product->product_name?></h3>
                    <p class="no_number">Vare-nummer: <?php echo $product->product_sku?></p>
                    <?php if(!empty($product->prices['discountAmount'])){?>
                        <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
                        <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay($product->prices['discountAmount'],0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>)</p>
                    <?php }?>
                    <h4>
                        <?php
                        if (VmConfig::get ( 'show_prices' ) == '1') {
                                echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
                        }?>
                    </h4>
                    <a class="btnMore btn2" href="<?php echo $link?>">Vis detaljer</a>
                </div>
            </li>
		<?php // echo $field->display ?>
	<?php } ?>
        </ul>
    </div>
</div>

<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$productModel = VmModel::getModel('product');
$ids = $productModel->sortSearchListQuery();
$products = $productModel->getProducts ($ids);
$productModel->addImages($products,1);
$pagination = $productModel->getPagination(4);
$id_manufactor=JRequest::getVar('virtuemart_manufacturer_id');
$box=$productModel->getOrderByList($id_manufactor);
//echo $box['orderby'];
//$box=$ids->orderByList['orderby'];
//echo $box;
if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
$currency = CurrencyDisplay::getInstance();
?>
<div class="template">
    <div class="product_page">
		{module Breadcrumbs}
        <h2 class="c505050"><?php echo $this->manufacturer->mf_name?></h2>

<div class="products">
    <ul class="clearfix">
<?php

	// Start the Output
	foreach($products as $product){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_manufacturer_medias WHERE virtuemart_manufacturer_id = ".$product->virtuemart_manufacturer_id);
		$vmi = $db->loadResult();
		
		if($vmi){
			$db->setQuery("SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = ".$vmi);
			$file_url = $db->loadResult();
		}
		
		$link=JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );
		// Show Products
		?>
		<li<?php echo $row_class?>>
			<div class="img_main">
<?php // Product Image
			if ($product->images) 
				echo $product->images[0]->displayMediaThumb( null, false );
?>
			</div>
			<h3>
				<?php echo (mb_strlen($product->product_name,"UTF-8") < 62) ? $product->product_name : mb_substr($product->product_name, 0, 61, "UTF-8")."…"?>
			</h3>
			<?php if(!empty($product->prices['discountAmount'])){?>
			<p class="price_before">Førpris: <?php echo $currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$currency->_priceConfig['basePrice'][1] );?></p>
			<?php //echo $currency->priceDisplay($product->prices['discountAmount'],0,1.0,false,$currency->_priceConfig['discountAmount'][1] ); ?>
				<p class="price_sale">(De sparer: <?php echo $currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$currency->_priceConfig['discountAmount'][1] );?>) </p>
			<?php }?>
			<h4 class="price_2">
				<?php
	//if (VmConfig::get ( 'show_prices' ))
		echo $currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$currency->_priceConfig['salesPrice'][1] );
				?>
			</h4>
			<h6 class="w_brand">
				<img src="<?php echo JURI::base().'thumbnail/timthumb.php?src='.$file_url.'&q=100&h=31'; ?>" />
			</h6>
			<div class="pro-larg animated clearfix">
				<div class="img_main">
					<a href="<?php echo $link?>"><?php echo $product->images[0]->displayMediaThumb( 'border="0"', false, '' )?></a>
				</div>
				<h3><?php echo $product->product_name?></h3>
				<p class="no_number">Vare-nummer: <?php echo $product->product_sku?></p>
				<?php if(!empty($product->prices['discountAmount'])){?>
				<p class="price_before">Førpris: <?php echo $currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$currency->_priceConfig['basePrice'][1] );?></p>
					<p class="price_sale">(De sparer: <?php echo $currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$currency->_priceConfig['discountAmount'][1] );?>) </p>
				<?php } ?>   
				<h4>
				<?php
					if (VmConfig::get ( 'show_prices' ))
							echo $currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$currency->_priceConfig['salesPrice'][1] );
				?>
				</h4>
				<a class="btnMore btn2" href="<?php echo $link?>">Vis detaljer</a>
			</div>
		</li>                        
		<?php
	} // end of foreach ( $this->products as $product )
?>
    		</ul>
		</div>
    </div>
</div>
<?php
return;
if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
$currency = CurrencyDisplay::getInstance();
JRequest::setVar("limitstart",0);
JRequest::setVar("limit",20);
//echo '<pre>',print_r($products),'</pre>';
?>
<div id="callout" class="banner-item">
	<div class="banner-item-img" style="text-align: center;line-height: 212px">
    <?php if($this->manufacturer->images[0]->file_url){?>
        <img src="<?php echo JURI::base().$this->manufacturer->images[0]->file_url;?>" />
    <?php 
        } else {
        echo $this->manufacturerImage;
        }
    ?>
	</div><!--.banner-item-img-->
	<div class="banner-item-content">
		<h2><?php echo $this->manufacturer->mf_name?></h2>
		<?php echo $this->manufacturer->mf_desc; ?>
	</div><!--.banner-item-content-->
</div>

	<?php // Manufacturer Email
	if(!empty($this->manufacturer->mf_email)) { ?>
		<div class="manufacturer-email">
		<?php // TO DO Make The Email Visible Within The Lightbox
		echo JHtml::_('email.cloak', $this->manufacturer->mf_email,true,JText::_('COM_VIRTUEMART_EMAIL'),false) ?>
		</div>
	<?php } ?>

	<?php // Manufacturer URL
	if(!empty($this->manufacturer->mf_url)) { ?>
		<div class="manufacturer-url">
			<a target="_blank" href="<?php echo $this->manufacturer->mf_url ?>"><?php echo JText::_('COM_VIRTUEMART_MANUFACTURER_PAGE') ?></a>
		</div>
	<?php }

/* Show products */

		if (!empty($products)){
?>

<div class="orderby-displaynumber">
	<div class="sorter">
		<div style="padding: 10px;border-bottom: 1px solid #CACACA">
		<?php echo $box['orderby']; ?>
		Visning <?php echo $pagination->getLimitBox (); ?>
		<div class="pagination"><?php echo $pagination->getPagesLinks (); ?></div>
		</div>
		<form id="mf_form_filters" action="<?php echo JURI::current()?>" method="post">
		<?php echo $this->orderByList['manufacturer']; ?>
		</form>
	</div>
</div>

<div class="product"><ul>
<?php
	// Category and Columns Counter
	$iBrowseCol = 1;

	// Calculating Products Per Row
	$ppr =4;

	// Start the Output
	foreach($products as $product){
		$link=JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );

		if($iBrowseCol == 1)
			echo '<div>';

		// Show the horizontal seperator
		if ($iBrowseCol == $ppr)
			$row_class=' class="no-mar"';
		else
				$row_class="";

		// Show Products
		?>
		<li<?php echo $row_class?>>
			<div class="img-pro" style="text-align:center">
			<?php // Product Image
			if ($product->images) {
				echo $product->images[0]->displayMediaThumb( '', false );
			}
			?>
			</div>
			<p class="title">
				<?php echo (mb_strlen($product->product_name,"UTF-8") < 62) ? $product->product_name : mb_substr($product->product_name, 0, 61, "UTF-8")."…"?>
			</p>

				<div class="price">
					<p class="new-price">
<?php
				if (VmConfig::get ( 'show_prices' ) == '1') {
					echo $currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$currency->_priceConfig['salesPrice'][1] );
				}?>
					</p>
				</div>
<?php if(!empty($product->prices['discountAmount'])){?>
					<div class="sale-off"><img src="templates/<?php echo $template?>/img/tilbud.png" width="67" height="67" alt=""></div>
<?php }?>
<div class="pro-larg fadeIn">
	<a href="<?php echo $link?>">
						<div class="img-pro-larg"><?php echo $product->images[0]->displayMediaThumb( 'border="0"', false, '' )?></div>
						
						<p class="title"><?php echo $product->product_name?></p>
						<p class="num">Varenr. <?php echo $product->product_sku?></p>
<?php if($product->product_delivery) echo "<p>VAREN KAN KUN AFHENTES!</p>"?>
						<div class="price">
					<?php if(!empty($product->prices['discountAmount'])){?>
						<p class="old-price-larg"><?php echo $currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$currency->_priceConfig['basePrice'][1] );?></p>

						<span class="sale">(SPAR <?php echo $currency->priceDisplay($product->prices['discountAmount'],0,1.0,false,$currency->_priceConfig['discountAmount'][1] );?>)</span>
					<?php }?>

						<p class="price-red"><?php echo $currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$currency->_priceConfig['salesPrice'][1] );?></p>

						<p class="v-detail">Vis detaljer</p>
						</div>
						<div class="add-cart"><?php if($product->product_in_stock - $product->product_ordered < 1){?>
						<span style="color: #F33;text-transform: uppercase;text-decoration: none;font-weight: bold;font-size: 16px;">UDSOLGT</span>
<?php }else{?>
	<?php if(!$product->product_delivery){?>
        <a rel="<?php echo $product->virtuemart_product_id?>">Læg i Kurv</a>
    <?php }?>
<?php }?></div>
					<?php if(!empty($product->prices['discountAmount'])){?>
						<div class="sale-off"><img src="templates/<?php echo $template?>/img/tilbud.png" width="67" height="67" alt=""></div>
					<?php }?>
	</a>
</div>
		</li> <!-- end of product -->
		<?php

		// Do we need to close the current row now?
		if ($iBrowseCol == $ppr){
			$iBrowseCol = 1;
			echo '<div class="clear"></div></div>';
		} else {
			$iBrowseCol++;
		}

	} // end of foreach ( $products as $product )
if($iBrowseCol != 1 AND $iBrowseCol != $ppr)
	echo '<div class="clear"></div></div>';
?>
</ul></div>
<div class="orderby-displaynumber">
	<div class="sorter">
		<div style="padding: 10px;border-bottom: 1px solid #CACACA">
			<?php echo $box['orderby']; ?>
			Visning <?php echo $pagination->getLimitBox (); ?>
			<div class="pagination"><?php echo $pagination->getPagesLinks (); ?></div>
			<div class="clear"></div>
		</div>
	</div>
</div>

<a id="btnAddItem" style="display:none;"></a>
<script type="text/javascript">
	jQuery(".add-cart a").click(function(e){
	jQuery.ajax( {
	type: "POST",
	url: "index.php?quantity%5B%5D=1&option=com_virtuemart&view=cart&virtuemart_product_id%5B%5D="+jQuery(this).attr("rel")+"&task=add",
	data: jQuery(this).serialize(),
	success: function( response ){
		cart_update();
		jQuery("#btnAddItem").click();
	}
	});
	return false;
});
</script>
<?php
		}
?>
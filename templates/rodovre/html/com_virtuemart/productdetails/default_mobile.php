<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
$tmpl = JURI::base().'templates/scheellarsen/mobile/';
/* Let's see if we found the product */
if (empty($this->product)) {
	echo JText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}
?>
<?php 
    if (!empty($this->product->customfieldsSorted['ontop'])) {
    $this->position = 'ontop';
    echo $this->loadTemplate('customfields');
    } // Product Custom ontop end

    // event onContentBeforeDisplay
    echo $this->product->event->beforeDisplayContent;
	
	if (!empty($this->product->images)) {
		$image = $this->product->images[0];
	}
?>
<?php if(in_array($this->product->virtuemart_category_id, $this->child_array)) {?>
<script type="text/javascript">
    jQuery('head').append('<link href="<?php echo $tmpl;?>css/styles-moblie-black.css" rel="stylesheet" />');
</script>
<?php }?>

<div id="content" class="w-content undepages productdetail_page">
	{module Breadcrumbs}
	<div class="eachBox boxProd_detail">
		<div class="product_img">
			<div class="img_larg"> <a id="btnLargeImage" class="fancybox" href="<?php echo $image->file_url;?>"><?php echo $image->displayMediaFull('width="430"',false,'');?></a> </div>
			<a id="btnZoomIcon" class="btnZoom fancybox" href="<?php echo $image->file_url;?>"><img src="<?php echo $tmpl;?>img/icon_zoom.png" alt=""></a> 
			
			<a href="javascript:void(0);" class="btnFb" id="facebookShare"><img src="<?php echo $tmpl;?>img/icon_face.png" alt=""></a>
			<?php if($this->product->video){?>
			<div class="video clearfix"> <a class="fancybox-media" href="<?php echo $this->product->video;?>"><img class="imgDemoVideo" src="<?php echo $tmpl;?>img/thumnail/img_small2.jpg" alt=""></a> </div>
			<?php }?>
		</div>
		<!--product_img-->
		<div class="product_content">
			<h2><?php echo $this->product->product_name ?></h2>
			<p><strong>Varenummer: <?php echo $this->product->product_sku?></strong></p>
			<div class="overview">
				<?php
				if (!empty($this->product->product_desc)) {
					echo $this->product->product_desc;
				}
				?>
			</div>
			<?php if($this->product->type_image){?>
			<a class="btn2 btn_sizeguide fancybox" href="#ppSizeguide">STØRRELSESGUIDE</a> 
			<!--Popup Size Guide-->
			<div id="ppSizeguide" style="display: none;">
				<div class="wrap-pp f_size">
					<h2>STØRRELSESGUIDE</h2>
					<div class="f_size_content">
						<div class="size_img"> <img src="images/type_images/<?php echo $this->product->type_image?>.jpg" alt=""> </div>
						<div class="size_detail">
							<table>
								<?php if($this->product->diameter){?>
								<tr>
									<td width="40%" class="black">Diameter:</td>
									<td><?php echo $this->product->diameter;?> cm</td>
								</tr>
								<?php }?>
								<?php if($this->product->width){?>
								<tr>
									<td class="black">Bredde:</td>
									<td><?php echo $this->product->width;?> cm</td>
								</tr>
								<?php }?>
								<?php if($this->product->length){?>
								<tr>
									<td class="black">Længde:</td>
									<td><?php echo $this->product->length;?> cm</td>
								</tr>
								<?php }?>
								<?php if($this->product->depth){?>
								<tr>
									<td class="black">Dybde:</td>
									<td><?php echo $this->product->depth;?> cm</td>
								</tr>
								<?php }?>
								<?php if($this->product->height){?>
								<tr>
									<td class="black">Højde:</td>
									<td><?php echo $this->product->height;?> cm</td>
								</tr>
								<?php }?>
								<?php if($this->product->seatheight){?>
								<tr>
									<td class="black">Sædehøjde:</td>
									<td><?php echo $this->product->seatheight;?> cm</td>
								</tr>
								<?php }?>
							</table>
						</div>
						<!--size_detail--> 
					</div>
					<!--f_size_content--> 
				</div>
				<!--wrap-pp--> 
			</div>
			<!--End#ppSizeguide-->
			<?php }?>
			<?php if(!empty($this->product->prices['discountAmount'])){?>
			<h3><span class="price_old">Førpris: <?php echo $this->currency->priceDisplay($this->product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></span> (De sparer: <?php echo $this->currency->priceDisplay(abs($this->product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>)</h3>
			<?php }?>
			<div class="rownumber clearfix">
				<div class="number">
					<label for="">ANTAL:</label>
					<input type="text" placeholder="<?php if (isset($this->product->min_order_level) && (int)$this->product->min_order_level > 0) {echo $this->product->min_order_level;} else {echo '1';} ?>" name="qtytmp" id="qtytmp" onblur="syncQty(this);">
				</div>
				<h2 class="price">
					<div class="product-price" id="productPrice<?php echo $this->product->virtuemart_product_id ?>">
					<?php if ($this->show_prices and (empty($this->product->images[0]) or $this->product->images[0]->file_is_downloadable == 0)) {
						if ($this->product->prices['discountedPriceWithoutTax'] != $this->product->prices['priceWithoutTax']) {
							echo $this->currency->createPriceDiv ('salesPrice', '', $this->product->prices);
						} else {
							echo $this->currency->createPriceDiv ('salesPrice', '', $this->product->prices);
						}
					}
					?>
					</div>
				</h2>
				<div class="stt_pro">
				<?php $stockhandle = VmConfig::get ('stockhandle', 'none');
                if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') || ($this->product->product_in_stock - $this->product->product_ordered) < 1) {
				?>
				<img src="<?php echo $tmpl?>img/icon_del.png" alt="">
				<?php }else{ ?>
				<img src="<?php echo $tmpl?>img/icon_checkgreen.png" alt="">
				<?php } ?>
				</div>
			</div>
			<?php 
            if (!empty($this->product->customfieldsSorted['normal'])){
				$this->position = 'normal';
				$int = 0;
				foreach ($this->product->customfieldsSorted[$this->position] as $field) {
					if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
						continue;
					if ($field->display) {
						if($field->field_type == "P")
							continue;
						if($field->field_type == "M"){
					?>
					<div class="image<?php echo $int;?>" style="display: none;"><?php echo $field->display ?></div>
					<?php }
					}
					$int++;
				}
            }
            ?>
			<!--<a class="btn2 btnAddcart" href="cart.php"><span><img src="<?php echo $tmpl;?>img/icon_bag.png"></span> Tilføj indkøbskurven</a>--> </div>
			<?php 
            if((($this->product->product_in_stock - $this->product->product_ordered) > 0) && (!$this->product->product_delivery))
			echo $this->loadTemplate('addtocart');
            ?>
		<!--product_content--> 
	</div>
	<!--eachBox wrapProd_detail-->
	<?php
	if (!empty($this->product->customfieldsRelatedProducts)) {
		echo $this->loadTemplate('relatedproducts');
	}
	?>
</div>
<?php return;?>
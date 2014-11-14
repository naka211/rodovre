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

$app = JFactory::getApplication();
$tmpl = JURI::base().'templates/'.$app->getTemplate()."/";
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
?>
<?php if(in_array($this->product->virtuemart_category_id, $this->child_array)) {?>
<script type="text/javascript">
    jQuery('head').append('<link href="<?php echo $tmpl;?>css/black_style.css" rel="stylesheet" />');
</script>
<?php }?>

<div class="template" style="margin-top:-30px;">
    <div class="productdetail_page clearfix">
        <div class="main_content frame clearfix"> {module Breadcrumbs}
            <div class="product_img">
                <?php
            echo $this->loadTemplate('images');
            ?>
                
                <!--<ul id="thumblist" class="thumail clearfix gallery">
              <li><a href="#"><img src="img/thumnail/img_larg.jpg" alt=""></a></li>
              <li><a href="#"><img src="img/thumnail/img_larg.jpg" alt=""></a></li>
              <li><a href="#"><img src="img/thumnail/img_larg.jpg" alt=""></a></li>
            </ul>-->
                <hr>
                <a href="javascript:void(0);" id="facebookShare"><img src="<?php echo $tmpl?>img/icon_face.png" alt=""></a>
                <div class="clear mb10"></div>
                <?php if($this->product->video){?>
                <div class="video clearfix"> <a class="fl imgZoom" href="<?php echo $this->product->video;?>"><img src="<?php echo $tmpl?>img/thumnail/img_small2.jpg" alt=""></a> </div>
                <?php }?>
            </div>
            <div class="product_content">
                <h2><?php echo $this->product->product_name ?></h2>
                <?php // afterDisplayTitle Event
                echo $this->product->event->afterDisplayTitle 
            ?>
                <p><strong>Varenummer: <?php echo $this->product->product_sku?></strong></p>
                <div id="scrollbar2">
                    <div class="scrollbar">
                        <div class="track">
                            <div class="thumb">
                                <div class="end"></div>
                            </div>
                        </div>
                    </div>
                    <div class="viewport">
                        <div class="overview">
                            <?php
                    if (!empty($this->product->product_desc)) {
                        echo $this->product->product_desc;
                    }
                    ?>
                        </div>
                    </div>
                </div>
                <?php if($this->product->type_image){
                ?>
                <div class="reveal-modal" id="f_size" style="top: 100px; opacity: 1; visibility: hidden;">
                    <div class="f_size clearfix">
                        <h2>STØRRELSESGUIDE</h2>
                        <div class="f_size_content">
                            <div class="size_img"> <img alt="" src="images/type_images/<?php echo $this->product->type_image?>.jpg"> </div>
                            <div class="size_detail">
                                <table>
                                    <tbody>
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
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <a class="close-reveal-modal" id="close-reveal-modal2"></a>
                </div>
                <a data-reveal-id="f_size" href="#" class="btn_sizeguide">STØRRELSESGUIDE</a>
                <?php }?>
                <h3>
                    <?php if(!empty($this->product->prices['discountAmount'])){?>
                    <span class="price_old">Førpris: <?php echo $this->currency->priceDisplay($this->product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></span>
                    <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($this->product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
                    <?php }?>
                </h3>
                <div class="number">
                    <label for="">Antal:</label>
                    <input type="text" name="qtytmp" id="qtytmp" value="<?php if (isset($this->product->min_order_level) && (int)$this->product->min_order_level > 0) {
			echo $this->product->min_order_level;
		} else {
			echo '1';
		} ?>" onblur="syncQty(this);"/>
                    <h2 class="price">
                        <?php if ($this->show_prices and (empty($this->product->images[0]) or $this->product->images[0]->file_is_downloadable == 0)) {
    echo $this->loadTemplate('showprices');
            } 
            ?>
                        <?php
//                    if (VmConfig::get ( 'show_prices' ))
//                            echo $this->currency->priceDisplay($this->product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
                ?>
                    </h2>
                </div>
                <div class="fr mt10">
                    <?php $stockhandle = VmConfig::get ('stockhandle', 'none');
                if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') || ($this->product->product_in_stock - $this->product->product_ordered) < 1) {
                    ?>
                    <img src="<?php echo $tmpl?>img/icon_del.png" alt="">
                    <?php }else{ ?>
                    <img src="<?php echo $tmpl?>img/icon_checkgreen.png" alt="">
                    <?php } ?>
                </div>
                <div class="clear mb20"></div>
                <?php 
            if (!empty($this->product->customfieldsSorted['normal'])){
            $this->position = 'normal';
            echo $this->loadTemplate('customfields');
            }
            ?>
                <?php 
            if((($this->product->product_in_stock - $this->product->product_ordered) > 0) && (!$this->product->product_delivery))
			echo $this->loadTemplate('addtocart');
            ?>
                <?php
	// Product Edit Link
	echo $this->edit_link;
	// Product Edit Link END
	?>
                <?php
	// PDF - Print - Email Icon
	if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_button_enable')) {
	?>
                <div class="icons">
                    <?php
		//$link = (JVM_VERSION===1) ? 'index2.php' : 'index.php';
		$link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;
		$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';

		if (VmConfig::get('pdf_icon', 1) == '1') {
		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_button_enable', false);
		}
		echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
		echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend');
		?>
                    <div class="clear"></div>
                </div>
                <?php } ?>
            </div>
            <?php
	// Product Navigation
	if (VmConfig::get('product_navigation', 1)) {
	?>
            <div class="product-neighbours">
                <?php
		if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_name'], array('class' => 'previous-page'));
		}
		if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id);
		echo JHTML::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('class' => 'next-page'));
		}
		?>
                <div class="clear"></div>
            </div>
            <?php }?>
            <?php
	// Back To Category Button
	/*if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id);
		$categoryName = $this->product->category_name ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = jText::_('COM_VIRTUEMART_SHOP_HOME') ;
	}

	<div class="back-to-category">
		<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo JText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	</div>*/
?>
            <?php
		// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
		/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
		  $text = JText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
		  echo '<span class="bold">'. JText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
		 */

		if ($this->showRating){
			$maxrating = VmConfig::get('vm_maximum_rating_scale', 5);

			if (empty($this->rating)) {
			?>
            <span class="vote"><?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . JText::_('COM_VIRTUEMART_UNRATED') ?></span>
            <?php
			} else {
				$ratingwidth = $this->rating->rating * 24; //I don't use round as percetntage with works perfect, as for me
				?>
            <span class="vote"> <?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . round($this->rating->rating) . '/' . $maxrating; ?><br/>
            <span title=" <?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE") . round($this->rating->rating) . '/' . $maxrating) ?>" class="ratingbox" style="display:inline-block;"> <span class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>"> </span> </span> </span>
            <?php
			}
		}
		if (is_array($this->productDisplayShipments)){
			foreach ($this->productDisplayShipments as $productDisplayShipment) {
			echo $productDisplayShipment . '<br />';
			}
		}
		if (is_array($this->productDisplayPayments)){
			foreach ($this->productDisplayPayments as $productDisplayPayment) {
			echo $productDisplayPayment . '<br />';
			}
		}


    if($this->product->price_of_number && $this->product->price_quantity_start){
        echo '<br /><br />Ved køb af '.$this->product->price_quantity_start.' eller flere er prisen '.str_replace('.', ',', number_format($this->product->price_of_number,2)).' kr. pr. stk.';
    }
	// Availability Image
	
	// Product Price ?>
            <div class="w-price-left">
                <?php
if ($this->show_prices and (empty($this->product->images[0]) or $this->product->images[0]->file_is_downloadable == 0)) {
//    echo $this->loadTemplate('showprices');
}

$model		= VmModel::getModel("shipmentmethod");
$shipment	= $model->getShipments();
$shipment = $shipment[1];
?>
            </div>
            <?php
		// Add To Cart Button
// 			if (!empty($this->product->prices) and !empty($this->product->images[0]) and $this->product->images[0]->file_is_downloadable==0 ) {
//		if (!VmConfig::get('use_as_catalog', 0) and !empty($this->product->prices['salesPrice'])) {
//		if((($this->product->product_in_stock - $this->product->product_ordered) > 0) && (!$this->product->product_delivery))
//			echo $this->loadTemplate('addtocart');
//		}

// Ask a question about this product
if (VmConfig::get('ask_question', 1) == 1){
	?>
            <div class="ask-a-question"> <a class="ask-a-question" href="<?php echo $this->askquestion_url ?>" ><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a> 
                <!--<a class="ask-a-question modal" rel="{handler: 'iframe', size: {x: 700, y: 550}}" href="<?php echo $this->askquestion_url ?>"><?php echo JText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>--> 
            </div>
            <?php }

		// Manufacturer of the Product
		if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
			//echo $this->loadTemplate('manufacturer');
		}
		?>
            <?php
//	if (!empty($this->product->customfieldsSorted['normal'])){
//	$this->position = 'normal';
//	echo $this->loadTemplate('customfields');
//	}
	// Product Packaging
	$product_packaging = '';
	if ($this->product->product_box) {
	?>
            <div class="product-box">
                <?php
			echo JText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
		?>
            </div>
            <?php } // Product Packaging END
	?>
            <?php if($this->product->product_delivery){
	$db = JFactory::getDBO();
	$db->setQuery("SELECT introtext FROM #__content WHERE id = 16");	
	$text = $db->loadResult();
?>
            <div class="w-frm-login reveal-modal" id="myModal1"> <a class="close-reveal-modal" href="javascript:void(0);"></a>
                <div class="frm-login">
                    <div class="logo2" style="text-align:left;border-bottom: none"><?php echo $text;?></div>
                </div>
            </div>
            <?php }?>
        </div>
        <?php
	// onContentAfterDisplay event
	echo $this->product->event->afterDisplayContent; ?>
        <?php
	// Product Files
	// foreach ($this->product->images as $fkey => $file) {
	// Todo add downloadable files again
	// if( $file->filesize > 0.5) $filesize_display = ' ('. number_format($file->filesize, 2,',','.')." MB)";
	// else $filesize_display = ' ('. number_format($file->filesize*1024, 2,',','.')." KB)";

	/* Show pdf in a new Window, other file types will be offered as download */
	// $target = stristr($file->file_mimetype, "pdf") ? "_blank" : "_self";
	// $link = JRoute::_('index.php?view=productdetails&task=getfile&virtuemart_media_id='.$file->virtuemart_media_id.'&virtuemart_product_id='.$this->product->virtuemart_product_id);
	// echo JHTMl::_('link', $link, $file->file_title.$filesize_display, array('target' => $target));
	// }
	if (!empty($this->product->customfieldsRelatedProducts)) {
	echo $this->loadTemplate('relatedproducts');
	} // Product customfieldsRelatedProducts END

	if (!empty($this->product->customfieldsSorted['onbot'])) {
		$this->position='onbot';
		echo $this->loadTemplate('customfields');
	} // Product Custom ontop end
	?>
    </div>
</div>

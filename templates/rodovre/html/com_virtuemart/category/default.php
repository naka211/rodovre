<?php
//vmdebug('$this->category',$this->category);
vmdebug ('$this->category ' . $this->category->category_name);
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

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

/*$edit_link = '';
if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
if (Permissions::getInstance()->check("admin,storeadmin")) {
	$edit_link = '<a href="'.JURI::root().'index.php?option=com_virtuemart&tmpl=component&view=category&task=edit&virtuemart_category_id='.$this->category->virtuemart_category_id.'">
		'.JHTML::_('image', 'images/M_images/edit.png', JText::_('COM_VIRTUEMART_PRODUCT_FORM_EDIT_PRODUCT'), array('width' => 16, 'height' => 16, 'border' => 0)).'</a>';
}

echo $edit_link; */
$app = JFactory::getApplication();
$tmpl = JURI::base().'templates/'.$app->getTemplate()."/";
if (empty($this->keyword)){
    $clcat = 29;
    $q = 'SELECT `category_parent_id` FROM `#__virtuemart_category_categories` WHERE `category_child_id`=' . $this->category->virtuemart_category_id;
    $db = JFactory::getDbo();
    $db->setQuery($q);
    $parentCategoryID = $db->loadResult();
    $parent = 0;
    if ($parentCategoryID != '0') {
       $categoryModel = VmModel::getModel('category');
       $parentCategory = $categoryModel->getCategory($parentCategoryID);

       $parent = $parentCategory->virtuemart_category_id;
    }

	?>
<div class="template">
    <div class="product_page">
        {module Breadcrumbs}
        <h2 class="c505050"><?php echo $this->category->category_name?></h2>
        <?php if($this->category->virtuemart_category_id == $clcat || $parent == $clcat) { ?>
<script type="text/javascript">
    $('head').append('<link href="<?php echo $tmpl;?>css/black_style.css" rel="stylesheet" />');
</script>
        <?php if($this->category->images){?>
        <div class="banner">
            <div class="html_carousel">
              <div class="shawdow-banner"></div>
              <div id="foo1">
                <?php foreach ($this->category->images as $pimage) { ?>
                <div class="slide">
                  <a href="<?php echo $pimage->file_custom_link ?>"><?php echo $pimage->displayMediaFull ('width="715" height="334"', FALSE); ?></a>
                  <div class="title"><h3><?php echo ($pimage->file_custom_text_head)? $pimage->file_custom_text_head : '';?></h3><p><?php echo $pimage->file_custom_text_content ?></p>
                  </div>
                </div>
                <?php } ?>
              </div>
              <div class="clearfix"></div>
              <div class="pagination" id="block1_pag"></div>
            </div>
        </div>
<?php }} ?>
        <h4 class="c505050"><?php echo $this->category->category_description; ?></h4>
        <p></p>
<!--<div id="callout" class="banner-item">
	<div class="banner-item-img">
	<?php //echo $this->category->images[0]->displayMediaFull ('width="336" height="212"', FALSE);?>
	</div>
	<div class="banner-item-content">
		<h2><?php //echo $this->category->category_name?></h2>
		<?php //echo $this->category->category_description; ?>
	</div>
	<a href="#"><span class="close"></span></a>
</div>-->
<?php
}

/* Show child categories */

if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
	if ($this->category->haschildren) {

		// Category and Columns Counter
		$iCol = 1;

		// Calculating Categories Per Row
		$categories_per_row = VmConfig::get ('categories_per_row', 3);
?>
<!--<div id="category">-->
<ul class="list_product clearfix">

<?php // Start the Output
		if (!empty($this->category->children)) {
			foreach ($this->category->children as $category){

				// Category Link
				$caturl = JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id);

				// Show Category

				// Show the horizontal seperator
				if ($iCol == $categories_per_row)
					$row_class=' class="n-m-r"';
				else
					$row_class="";
?>
	<li<?php echo $row_class?>>
            <div class="img_main">
                <a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>"><?php echo $category->images[0]->displayMediaThumb ('width="217" heigh="161"', FALSE);?></a>
            </div>
            <h3><?php echo $category->category_name ?></h3>
<!--        <a href="<?php //echo $caturl ?>" title="<?php //echo $category->category_name ?>">
		<div class="cate-img">
		<?php //echo $category->images[0]->displayMediaThumb ('width="115"', FALSE);?>
		</div>
		<p class="cate-title">
		<?php //echo $category->category_name ?>
		</p>
        </a>-->
	</li>
<?php
				// Do we need to close the current row now?
				if ($iCol == $categories_per_row) {
					$iCol = 1;
				} else {
					$iCol++;
				}
			}
		}
		// Do we need a final closing row tag?
		if ($iCol != 1) {
?>
	<div class="clear"></div>
<?php	}?>
</ul>
<!--</div>-->
<?php
	}else{

/* Show products */

		if (!empty($this->products)){
?>
<!--<div class="orderby-displaynumber">
	<div class="sorter">
		<div style="padding: 10px;border-bottom: 1px solid #CACACA">
		<?php //echo $this->orderByList['orderby']; ?>
		Visning <?php //echo $this->vmPagination->getLimitBox (); ?>
		<div class="pagination"><?php //echo $this->vmPagination->getPagesLinks (); ?></div>
		</div>
		<form id="mf_form_filters" action="<?php echo JURI::current()?>" method="post">
		<?php //echo $this->orderByList['manufacturer']; ?>
		</form>
	</div>
</div>-->
<div class="products">
    <ul class="clearfix">
<?php

	// Start the Output
	foreach($this->products as $product){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_manufacturer_medias WHERE virtuemart_manufacturer_id = ".$product->virtuemart_manufacturer_id);
		$vmi = $db->loadResult();
		
		if($vmi){
			$db->setQuery("SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = ".$vmi);
			$file_url = $db->loadResult();
		}
		
		if(!$file_url){
			$file_url = JURI::base()."components/com_virtuemart/assets/images/vmgeneral/noimage.gif";
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
                        <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
                        
                        <?php //echo $this->currency->priceDisplay($product->prices['discountAmount'],0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] ); ?>
                            <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
                        <?php }?>
                        <h4 class="price_2">
                            <?php
				if (VmConfig::get ( 'show_prices' ))
					echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
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
                            <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
                                <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
                            <?php } ?>   
                            <h4>
                            <?php
                                if (VmConfig::get ( 'show_prices' ))
                                        echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
                            ?>
                            </h4>
                            <a class="btnMore btn2" href="<?php echo $link?>">Vis detaljer</a>
                        
		<?php
	} // end of foreach ( $this->products as $product )
if($iBrowseCol != 1)
	echo '<div class="clear"></div></div>';
?>
    </ul>
</div>
<!--<div class="orderby-displaynumber">
	<div class="sorter">
		<div style="padding: 10px;border-bottom: 1px solid #CACACA">
			<?php //echo $this->orderByList['orderby']; ?>
			Visning <?php //echo $this->vmPagination->getLimitBox (); ?>
			<div class="pagination"><?php //echo $this->vmPagination->getPagesLinks (); ?></div>
		</div>
	</div>
</div>-->

<!--<a id="btnAddItem" style="display:none;"></a>-->
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
	}
        ?>
    </div>
</div>
<?php }


if (!empty($this->keyword)){
    if (!empty($this->products)){
?>
<div class="products">
    <ul class="clearfix">
<?php

	// Start the Output
	foreach($this->products as $product){
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
                        <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
                        <?php //echo $this->currency->priceDisplay($product->prices['discountAmount'],0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] ); ?>
                            <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
                        <?php }?>
                        <h4 class="price_2">
                            <?php
				if (VmConfig::get ( 'show_prices' ))
					echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
                            ?>
                        </h4>
                        <div class="pro-larg animated clearfix">
                            <div class="img_main">
                                <a href="<?php echo $link?>"><?php echo $product->images[0]->displayMediaThumb( 'border="0"', false, '' )?></a>
                            </div>
                            <h3><?php echo $product->product_name?></h3>
                            <p class="no_number">Vare-nummer: <?php echo $product->product_sku?></p>
                            <?php if(!empty($product->prices['discountAmount'])){?>
                            <p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
                                <p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
                            <?php } ?>   
                            <h4>
                            <?php
                                if (VmConfig::get ( 'show_prices' ))
                                        echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
                            ?>
                            </h4>
                            <a class="btnMore btn2" href="<?php echo $link?>">Vis detaljer</a>
                        
		<?php
	} // end of foreach ( $this->products as $product )
?>
    </ul>
</div>  
<?php
    } else {
        echo 'Intet resultat' . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
    }
}?>

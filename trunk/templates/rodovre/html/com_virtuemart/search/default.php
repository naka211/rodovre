<?php
//vmdebug('$this->category',$this->category);
vmdebug ('$this->category ' . $this->category->category_name);
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
JHTML::_ ('behavior.modal');
/* javascript for list Slide
  Only here for the order list
  can be changed by the template maker
*/
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";

$document = JFactory::getDocument ();
$document->addScriptDeclaration ($js);

/*$edit_link = '';
if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
if (Permissions::getInstance()->check("admin,storeadmin")) {
	$edit_link = '<a href="'.JURI::root().'index.php?option=com_virtuemart&tmpl=component&view=category&task=edit&virtuemart_category_id='.$this->category->virtuemart_category_id.'">
		'.JHTML::_('image', 'images/M_images/edit.png', JText::_('COM_VIRTUEMART_PRODUCT_FORM_EDIT_PRODUCT'), array('width' => 16, 'height' => 16, 'border' => 0)).'</a>';
}

echo $edit_link; */
if (empty($this->keyword)) {
	?>
<div class="category_description">
	<?php echo $this->category->category_description; ?>
</div>
<?php
}

/* Show child categories */

if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
	if ($this->category->haschildren) {

		// Category and Columns Counter
		$iCol = 1;
		$iCategory = 1;

		// Calculating Categories Per Row
		$categories_per_row = VmConfig::get ('categories_per_row', 3);
		$category_cellwidth = ' width' . floor (100 / $categories_per_row);

		// Separator
		$verticalseparator = " vertical-separator";
		?>

		<div class="category-view">

		<?php // Start the Output
		if (!empty($this->category->children)) {
			foreach ($this->category->children as $category) {

				// Show the horizontal seperator
				if ($iCol == 1 && $iCategory > $categories_per_row) {
					?>
					<div class="horizontal-separator"></div>
					<?php
				}

				// this is an indicator wether a row needs to be opened or not
				if ($iCol == 1) {
					?>
			<div class="row">
			<?php
				}

				// Show the vertical seperator
				if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
					$show_vertical_separator = ' ';
				} else {
					$show_vertical_separator = $verticalseparator;
				}

				// Category Link
				$caturl = JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id);

				// Show Category
				?>
				<div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
					<div class="spacer">
						<h2>
							<a href="<?php echo $caturl ?>" title="<?php echo $category->category_name ?>">
								<?php echo $category->category_name ?>
								<br/>
								<?php // if ($category->ids) {
								echo $category->images[0]->displayMediaThumb ("", FALSE);
								//} ?>
							</a>
						</h2>
					</div>
				</div>
				<?php
				$iCategory++;

				// Do we need to close the current row now?
				if ($iCol == $categories_per_row) {
					?>
					<div class="clear"></div>
		</div>
			<?php
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
		</div>
	<?php } ?>
	</div>

	<?php
	}
}

// Show child categories
if (!empty($this->products)) {/* Show products */
?>
<div class="product"><ul>
<?php
	// Category and Columns Counter
	$iBrowseCol = 1;

	// Calculating Products Per Row
	$ppr = $this->perRow;

	// Start the Output
	foreach($this->products as $product){
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
					echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );
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
						<p class="old-price-larg"><?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>

						<span class="sale">(SPAR <?php echo $this->currency->priceDisplay($product->prices['discountAmount'],0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>)</span>
					<?php }?>

						<p class="price-red"><?php echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );?></p>

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
		}else
			$iBrowseCol++;

	} // end of foreach ( $this->products as $product )
if($iBrowseCol != 1)
	echo '<div class="clear"></div></div>';
?>
</ul></div>

<div class="orderby-displaynumber">
	<div class="sorter">
		<div style="padding: 10px;border-bottom: 1px solid #CACACA">
			<div class="pagination"><?php echo $this->vmPagination->getPagesLinks (); ?></div>
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
		} elseif ($this->search !== NULL) {
	echo JText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
}
?>
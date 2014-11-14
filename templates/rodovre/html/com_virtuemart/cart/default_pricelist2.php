<?php defined ('_JEXEC') or die('Restricted access');
// Check to ensure this file is included in Joomla!

// jimport( 'joomla.application.component.view');
// $viewEscape = new JView();
// $viewEscape->setEscape('htmlspecialchars');
$tmplURL=JURI::base()."templates/".$template;
?>
<div class="title-cart-item">
	<div class="title-pro-item">
	<span><?php echo JText::_ ('COM_VIRTUEMART_CART_NAME') ?></span>
	</div>
	<div class="title-pro-no">
	<span><?php echo JText::_ ('COM_VIRTUEMART_CART_SKU') ?></span>
	</div>
	<div class="title-pro-num">
	<span><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY') ?></span>
	</div>
	<div class="title-pro-price">
	<span><?php echo JText::_ ('COM_VIRTUEMART_CART_PRICE') ?></span>
	</div>
	<div class="title-pro-total">
	<span><?php echo JText::_ ('COM_VIRTUEMART_CART_TOTAL') ?></span>
	</div>
</div>
<?php
//$i = 1;
// vmdebug('$this->cart->products',$this->cart->products);
//print_r($this->cart);exit;
foreach ($this->cart->products as $pkey => $prow) {
?>
<div class="pro-item">
	<div class="del-pro">
		<a class="vmicon vm2-remove_from_cart" title="<?php echo JText::_ ('COM_VIRTUEMART_CART_DELETE') ?>" align="middle" href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id=' . $prow->cart_item_id) ?>"><img src="<?php echo $tmplURL?>/img/delete.png" width="12" height="12" alt=""></a>
	</div>
	<div class="col1">
		<div class="img-pro-item" style="width: auto">
		<?php
			if ($prow->virtuemart_media_id AND !empty($prow->image)) {
				echo '<img src="'.$prow->image->file_url_thumb.'" height="72" alt="" />';
			}
		?>
		</div>
		<div class="pro-item-title" style="max-width: 248px">
		<p><?php echo $prow->product_name.$prow->customfields?></p>
		</div><!--.pro-item-title-->
	</div>
	<div class="col2">
		<p><?php echo $prow->product_sku ?></p>
	</div>
	<div class="col3">
	<form action="<?php echo JRoute::_ ('index.php'); ?>" method="post" class="inline">
		<input type="hidden" name="option" value="com_virtuemart"/>
		<p><input type="text" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputbox" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>"/></p>
		<input type="hidden" name="view" value="cart"/>
		<input type="hidden" name="task" value="update"/>
		<input type="hidden" name="cart_virtuemart_product_id" value="<?php echo $prow->cart_item_id  ?>"/>
		<input type="submit" class="vmicon vm2-add_quantity_cart" name="update" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" style="width:auto; cursor:pointer;" value="Opdatere"/>
	</form>
	</div>
	<div class="col4">
		<p><?php
		// vmdebug('$this->cart->pricesUnformatted[$pkey]',$this->cart->pricesUnformatted[$pkey]['priceBeforeTax']);
		echo $this->currencyDisplay->priceDisplay($this->cart->pricesUnformatted[$pkey]['salesPrice'],0,1.0,false,2);
		// echo $prow->salesPrice ;
		?></p>
	</div>
<div class="col5">
	<p><?php echo $this->currencyDisplay->priceDisplay($this->cart->pricesUnformatted[$pkey]['salesPrice'],0,$prow->quantity,false,2);?></p>
</div>

	
	
</div>
<?php
	//$i = ($i==1) ? 2 : 1;
} ?>
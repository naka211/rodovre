<?php
/**
 *
 * Show the product details page
 *
 * @package    VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen
 * @todo handle child products
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_addtocart.php 6433 2012-09-12 15:08:50Z openglobal $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
if (isset($this->product->step_order_level))
	$step=$this->product->step_order_level;
else
	$step=1;
if($step==0)
	$step=1;
$alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);
?>

<div class="addtocart-area">
	<form method="post" class="product js-recalculate" action="<?php echo JRoute::_ ('index.php',false); ?>">
		<input name="quantity" type="hidden" value="<?php echo $step ?>" />
		<?php // Product custom_fields
		if (!empty($this->product->customfieldsCart)) {
			?>
		<div class="product-fields">
			<?php foreach ($this->product->customfieldsCart as $field) { //print_r($field);exit;?>
			<ul class="option clearfix">
				<div class="product-field product-field-type-<?php echo $field->field_type ?>">
					<?php if ($field->show_title) { ?>
					<span class="product-fields-title-wrapper"><span class="product-fields-title"><strong><?php echo vmText::_ ($field->custom_title) ?></strong></span>
					<?php }
					if ($field->custom_tip) {
						echo JHTML::tooltip (vmText::_($field->custom_tip), vmText::_ ($field->custom_title), 'tooltip.png');
					} ?>
					</span> <span class="product-field-display"><?php echo $field->display ?></span> <span class="product-field-desc"><?php echo vmText::_($field->custom_field_desc) ?></span> </div>
			</ul>
			<?php 
                                } ?>
		</div>
		<?php
		}
		/* Product custom Childs
			 * to display a simple link use $field->virtuemart_product_id as link to child product_id
			 * custom_value is relation value to child
			 */

		if (!empty($this->product->customsChilds)) {
			?>
		<div class="product-fields">
			<?php foreach ($this->product->customsChilds as $field) { ?>
			<div class="product-field product-field-type-<?php echo $field->field->field_type ?>"> <span class="product-fields-title"><strong><?php echo JText::_ ($field->field->custom_title) ?></strong></span> <span class="product-field-desc"><?php echo JText::_ ($field->field->custom_value) ?></span> <span class="product-field-display"><?php echo $field->display ?></span> </div>
			<br/>
			<?php } ?>
		</div>
		<?php
		}

		if (!VmConfig::get('use_as_catalog', 0)  ) {
		?>
		<div class="addtocart-bar"> 
			<script type="text/javascript">
    
jQuery(document).ready( function(){
    var items = jQuery(".option.clearfix .product-field-type-S li");
    jQuery(".option.clearfix .product-field-type-S li").click(function() {
        var parent = jQuery(this).attr('parent-id');
        var index = items.index(this);
        var newurl = jQuery(".product-field.product-field-type-M .product-field-display li[parent-id='" + parent + "']").eq(index).find("img").attr("src");
        newurl = newurl.replace("resized/", "");
        newurl = newurl.substr(0, newurl.lastIndexOf("_"))+newurl.substr(newurl.lastIndexOf("."));
//        alert(newurl);
//        alert(jQuery(".product-fields .image"+index+" img").attr("src"));
        jQuery(".product_img .img_larg .imgZoom").attr("href",newurl);
        jQuery(".product_img #btnZoomIcon").attr("href",newurl);
        jQuery(".product_img .img_larg .imgZoom img").attr("src",newurl);
        jQuery(".product-field.product-field-type-M .product-field-display li[parent-id='" + parent + "']").eq(index).find("input").attr("checked","checked");
    });    
});
jQuery('.product-field-type-M').parent().hide();

    function syncQty(obj){
        jQuery(".quantity-input").val(obj.value);
    }
	function check(obj) {
		// use the modulus operator '%' to see if there is a remainder
		remainder=obj.value % <?php echo $step?>;
		quantity=obj.value;
		if (remainder  != 0) {
			alert('<?php echo $alert?>!');
			obj.value = quantity-remainder;
			return false;
		}
		return true;
	}
</script>
			<?php // Display the quantity box

			$stockhandle = VmConfig::get ('stockhandle', 'none');
			if (($stockhandle == 'disableit' or $stockhandle == 'disableadd') and ($this->product->product_in_stock - $this->product->product_ordered) < 1) {
				?>
			<a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&layout=notify&virtuemart_product_id=' . $this->product->virtuemart_product_id); ?>" class="notify"><?php echo JText::_ ('COM_VIRTUEMART_CART_NOTIFY') ?></a>
			<?php
			} else {
				$tmpPrice = (float) $this->product->prices['costPrice'];
				if (!( VmConfig::get('askprice', 0) and empty($tmpPrice) ) ) {
					?>
			<!-- <label for="quantity<?php echo $this->product->virtuemart_product_id; ?>" class="quantity_box"><?php echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY'); ?>: </label> --> 
			<span class="quantity-box" style="visibility:hidden">
			<input type="text" class="quantity-input js-recalculate" name="quantity[]" onblur="check(this);"
                                                   value="<?php if (isset($this->product->step_order_level) && (int)$this->product->step_order_level > 0) {
                                                                echo $this->product->step_order_level;
                                                        } else if(!empty($this->product->min_order_level)){
                                                                echo $this->product->min_order_level;
                                                        }else {
                                                                echo '1';
                                                        } ?>"/>
			</span>
			<?php // Display the quantity box END

                                // Display the add to cart button ?>
			<input type="submit" name="addtocart" class="btnAddcart btn2 addtocart-button" value="Tilføj indkøbskurven" title="Tilføj indkøbskurven" style="cursor:pointer; border:none;" />
			<!--          			<span class="addtocart-button">
          			<?php echo shopFunctionsF::getAddToCartButton ($this->product->orderable);
						// Display the add to cart button END  ?>
         			 </span>-->
			<noscript>
			<input type="hidden" name="task" value="add"/>
			</noscript>
			<?php
				}
				?>
			<?php
			}
			?>
			<div class="clear"></div>
		</div>
		<?php
		}
		?>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="view" value="cart"/>
		<input type="hidden" name="virtuemart_product_id[]" value="<?php echo $this->product->virtuemart_product_id ?>"/>
		<input type="hidden" class="pname" value="<?php echo htmlentities($this->product->product_name, ENT_QUOTES, 'utf-8') ?>"/>
	</form>
	<div class="clear"></div>
</div>

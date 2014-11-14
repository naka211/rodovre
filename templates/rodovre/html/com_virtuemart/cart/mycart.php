<?php
/**
*
* Layout for the shopping cart
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<?php 
$cart = VirtueMartCart::getCart(); 
$cart->prepareCartViewData();
?>
<?php foreach($cart->products as $pid => $product){
    $customhtml = $product->customfields;
//    preg_match_all("#<span class=\"product-field-type-S\"> ([\w\W]*?)</span>#", $product->customfields, $tmp);
//    $select1 = $tmp[1][0];
//    $select2 = $tmp[1][1];
//    preg_match("#src=\"([\w\W]*?)\" alt#", $product->customfields, $tmp1);
//    $img = $tmp1[1];
    preg_match("/<img .*?(?=src)src=\"([^\"]+)\" alt=\"([^\"]+)\"/si", $customhtml, $matches); 
    $cusfinal1 = str_replace($matches[0]."  />", "", $customhtml);
    $cusfinal = str_replace('<span class="product-field-type-M"> </span><br />', '', $cusfinal1);
?>
<li class="clearfix">
    <div class="list-cart-img"><a href="<?php echo $product->url; ?>">
        <?php 
            if(count($matches)>0){
                echo $matches[0].'/>';
            }else{
                echo $product->image->displayMediaThumb ('', FALSE);
            }
        ?>
        </a></div>
    <div class="list-cart-content">
      <h4><?php echo $product->product_name; ?></h4>
      <p>Vare-nummer: <?php echo $product->product_sku; ?></p>
      <p><?php echo $cusfinal ?></p>
    </div>
    <div class="count">
       <p><?php echo $product->quantity;?></p>
    </div>
    <p class="price">
        <?php if ($cart->pricesUnformatted[$pid]['discountedPriceWithoutTax']) {
                echo number_format($cart->pricesUnformatted[$pid]['discountedPriceWithoutTax'], 2,',','.'). ' DKK';
        } else {
                echo number_format($cart->pricesUnformatted[$pid]['basePriceVariant'], 2,',','.'). ' DKK';
        }
        ?>
    </p>
    <p class="price3"><?php echo number_format($cart->pricesUnformatted[$pid]['salesPrice']*$product->quantity, 2,',','.'). ' DKK'; ?></p>
    <a href="javascript:void(0);" onclick="deleteProduct('<?php echo $product->cart_item_id;?>')" rel="nofollow"" class="btnClose">close</a>
</li>
<?php } ?>
<?php if(count($cart)>0){
    $pri = $cart->pricesUnformatted['salesPrice'];
}
else{
    $pri = 0;
}
?>
<div class="my-tax" style="display: none"><?php echo number_format($cart->pricesUnformatted['salesPrice']*0.2,2,',','.');?> DKK</div>
<div class="my-total" style="display: none"><?php echo number_format($pri,2,',','.');?> DKK</div>
<div class="my-quantity" style="display: none"><?php echo count($cart->products);?> VARE(R) =</div>

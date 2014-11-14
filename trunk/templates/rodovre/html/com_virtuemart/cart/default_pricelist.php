<?php defined ('_JEXEC') or die('Restricted access');
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 * @author Patrick Kohl
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */
 
//T.Trung
if($this->cart->couponCode){
    $db= JFactory::getDBO();
    $query = "SELECT id, coupon_value FROM #__awocoupon WHERE coupon_code = '".$this->cart->couponCode."'";
    $db->setQuery($query);
    $coupon = $db->loadObject();
    
    $query = "SELECT coupon_discount, shipping_discount FROM #__awocoupon_history WHERE coupon_id = ".$coupon->id."";
    $db->setQuery($query);
    $discounts = $db->loadObjectList();
	
    $coupon_value = $coupon->coupon_value;
	foreach($discounts as $discount){
		$coupon_value = $coupon_value - $discount->coupon_discount - $discount->shipping_discount;
	}
}
//T.Trung end
?>

            <?php if(count($this->cart->products)> 0){ ?>
<table class="list_item_cart">
        <tr class="title">
            <th>Varebeskrivelse<?php // echo JText::_ ('COM_VIRTUEMART_CART_NAME') ?></th>
            <th>Antal<?php // echo JText::_ ('COM_VIRTUEMART_CART_QUANTITY') ?></th>
            <th>Pris pr stk.<?php // echo JText::_ ('COM_VIRTUEMART_CART_PRICE') ?></th>
            <th>Pris i alt<?php // echo JText::_ ('COM_VIRTUEMART_CART_TOTAL') ?></th>
        </tr>
<?php 
$i = 1;
$x = 0;
// 		vmdebug('$this->cart->products',$this->cart->products);
foreach ($this->cart->products as $pkey => $prow) {
	?>
<tr valign="top" class="sectiontableentry<?php echo $i ?>">
    <td>
        <div class="img_pro">
        <?php
            $customhtml = $prow->customfields;
//            $dom = new DOMDocument;
//$dom->loadHTML($customhtml);
//
//$images = $dom->getElementsByTagName('img');
//foreach ($images as $image) {
//    
//    $attrs = $imgages->attributes();
//    $src = $attrs->getNamedItem('src')->nodeValue;
//    print $src;
//}
            preg_match("/<img .*?(?=src)src=\"([^\"]+)\" alt=\"([^\"]+)\"/si", $customhtml, $matches); 
//            preg_match('/<img[^>]*src="([^"]*.jpg*>)"/i', $customhtml, $matches);
//            print_r($matches);

//            $optionimg = substr( $prow->customfields, strpos($prow->customfields, '<img src="'), strpos($prow->customfields, '.jpg"')-strpos($prow->customfields, '<img src="'));
        ?>
          <?php if ($prow->virtuemart_media_id) { ?>
                                             <?php
                    if (!empty($prow->image)) {
                        if(count($matches)>0){
                            echo $matches[0].'/>';
                        }else{
                            echo $prow->image->displayMediaThumb ('', FALSE);
                        }
                    }
                    ?>
            <?php } ?>
            <?php 
                $cusfinal = str_replace($matches[0]."  />", "", $prow->customfields);
                $cusfinal = str_replace('<span class="product-field-type-M"> </span><br />', '', $cusfinal);
            ?>
        </div>
        <div class="content_pro">
          <h4><?php echo JHTML::link ($prow->url, $prow->product_name);?></h4>
          <p>Vare-nummer: <?php  echo $prow->product_sku ?></p>
          <p><?php echo $cusfinal ?> </p>
        </div>
    </td>
    <td>
        <div class="relative number" style="margin-top:23px;">
        <?php
//				$step=$prow->min_order_level;
                                if ($prow->step_order_level)
                                        $step=$prow->step_order_level;
                                else
                                        $step=1;
                                if($step==0)
                                        $step=1;
                                $alert=JText::sprintf ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', $step);
                                ?>
                <script type="text/javascript">
				function check<?php echo $step?>(obj) {
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

				<!--<input type="text" title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputbox" size="3" maxlength="4" name="quantity" value="<?php echo $prow->quantity ?>" /> -->
            <input type="text"
				   onblur="check<?php echo $step?>(this);"
				   onclick="check<?php echo $step?>(this);"
				   onchange="check<?php echo $step?>(this);"
				   onsubmit="check<?php echo $step?>(this);"
				   title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="quantity-input js-recalculate mwc-qty<?php echo $x ?>" size="3" maxlength="4" name="quantity[<?php echo $prow->cart_item_id ?>]" value="<?php echo $prow->quantity ?>" />	
            <input type="submit" onclick="qty(<?php echo $x ?>, 'up');" class="vmicon vm2-add_quantity_cart add" name="update[<?php echo $prow->cart_item_id ?>]" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" align="middle" value=""/>
                        <input type="submit" onclick="qty(<?php echo $x ?>, 'down');" class="vmicon vm2-add_quantity_cart sub" name="update[<?php echo $prow->cart_item_id ?>]" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" align="middle" value=""/>
                        <script type="text/javascript">
                            function qty(item, opt){
                                if(opt == 'up'){
                                    var oldqty = jQuery('.mwc-qty'+item).val();
                                    jQuery('.mwc-qty'+item).val(parseInt(oldqty)+1);
                                }else{
                                    var oldqty = jQuery('.mwc-qty'+item).val();
                                    jQuery('.mwc-qty'+item).val(parseInt(oldqty)-1);
                                }
                            }
                        </script>
                    <!--<a class="vmicon vm2-remove_from_cart" title="<?php // echo JText::_ ('COM_VIRTUEMART_CART_DELETE') ?>" align="middle" href="<?php // echo JRoute::_ ('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id=' . $prow->cart_item_id) ?>" rel="nofollow"> </a>-->
                
            </div>
	</td>

	<td align="center">
		<?php
		/*if (VmConfig::get ('checkout_show_origprice', 1) && $this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax'] != $this->cart->pricesUnformatted[$pkey]['priceWithoutTax']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE) . '</span><br />';
		}*/
		if ($this->cart->pricesUnformatted[$pkey]['discountedPriceWithoutTax']) {
			//echo $this->currencyDisplay->createPriceDiv ('discountedPriceWithoutTax', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
            echo '<p style="margin-top:23px;">'.number_format($this->cart->pricesUnformatted[$pkey]['salesPrice'], 2,',','.'). ' DKK</p>';
		} else {
			echo $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE);
		}
		// 					echo $prow->salesPrice ;
		?>
	</td>
	
	<td>
		<?php
		/*if (VmConfig::get ('checkout_show_origprice', 1) && !empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceWithTax'] != $this->cart->pricesUnformatted[$pkey]['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceWithTax', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE, $prow->quantity) . '</span><br />';
		}
		elseif (VmConfig::get ('checkout_show_origprice', 1) && empty($this->cart->pricesUnformatted[$pkey]['basePriceWithTax']) && $this->cart->pricesUnformatted[$pkey]['basePriceVariant'] != $this->cart->pricesUnformatted[$pkey]['salesPrice']) {
			echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $this->cart->pricesUnformatted[$pkey], TRUE, FALSE, $prow->quantity) . '</span><br />';
		}*/
		//echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $this->cart->pricesUnformatted[$pkey], FALSE, FALSE, $prow->quantity);
        echo '<p style="margin-top:23px;">'.number_format($this->cart->pricesUnformatted[$pkey]['salesPrice']*$prow->quantity, 2,',','.'). ' DKK</p>';
        ?>
        <a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id=' . $prow->cart_item_id) ?>" class="btnDel">Delete</a></td>
</tr>
	<?php
	$i = ($i==1) ? 2 : 1;
        $x++;
} ?>
       <tr class="cf9f7f3">
                  <td colspan="4">
                    <table class="sub_order_Summary">
                      <tr>
                        <td colspan="2">
                          <ul>
                            <li>Ved køb af varer over 1.000 DKK. hos Scheel-Larsen.dk leverer og samler vi GRATIS..! på hele Sjælland.</li>
                            <li>Ved køb under 1.000 DKK. pålægges et fragtgebyr på 150 DKK.</li>
                            <li>Fragt til Jylland 350 DKK.</li>
                          </ul>
                        </td>
                        <td colspan="2" width="39%">
                          <table>
                            <tr><?php // print_r($this->cart->pricesUnformatted); ?>
                              <td>SUBTOTAL INKL. MOMS:</td>
                              <td><?php echo number_format($this->cart->pricesUnformatted['salesPrice'],2,',','.').' DKK'; ?></td>
                            </tr>
                            <?php if (!empty($this->cart->cartData['couponCode'])) { ?>
                            <tr>
                              <td>Gavekort kupon: </td>
                              <td><?php echo number_format($this->cart->pricesUnformatted['salesPriceCoupon'],2,',','.').' DKK'; ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                              <td><h4>AT BETALE INKL. MOMS:</h4></td>
                              <td><h4><?php echo number_format($this->cart->pricesUnformatted['billTotal'],2,',','.').' DKK'; ?></h4></td>
                            </tr>
                            <?php if (!empty($this->cart->cartData['couponCode'])) { ?>
                            <tr>
                              <td colspan="2" style="text-align:right;">(Gavekort restbeløb: <?php echo number_format($coupon_value + $this->cart->pricesUnformatted['salesPriceCoupon'],2,',','.').' DKK'; ?>)</td>
                            </tr>
                            <?php } ?>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr> 
        </table>
        
<?php }else{ ?>
        <div>Din indkøbskurv er tom</div>
<?php } ?>


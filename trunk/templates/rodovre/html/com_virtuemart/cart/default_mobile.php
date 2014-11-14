<?php
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
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
defined ('_JEXEC') or die('Restricted access');
if(VmConfig::get('usefancy',1)){
	vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
	vmJsApi::css('jquery.fancybox-1.3.4');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		var con = $('div#full-tos').html();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.fancybox ({ div: '#full-tos', content: con });
		});
	});

//]]>
";
} else {
	vmJsApi::js ('facebox');
	vmJsApi::css ('facebox');
	$box = "
//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.facebox( { div: '#full-tos' }, 'my-groovy-style');
		});
	});

//]]>
";
}

JHtml::_ ('behavior.formvalidation');
$document = JFactory::getDocument ();
$document->addScriptDeclaration ($box);
$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
	if ( $('#STsameAsBTjs').is(':checked') ) {
				$('#output-shipto-display').hide();
			} else {
				$('#output-shipto-display').show();
			}
		$('#STsameAsBTjs').click(function(event) {
			if($(this).is(':checked')){
				$('#STsameAsBT').val('1') ;
				$('#output-shipto-display').hide();
			} else {
				$('#STsameAsBT').val('0') ;
				$('#output-shipto-display').show();
			}
		});
	});

//]]>

");


$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
	$('#checkoutFormSubmit').click(function(e){
    $(this).attr('disabled', 'true');
    $(this).fadeIn( 400 );
    $('#checkoutForm').submit();
});
	});

//]]>

");

$document->addStyleDeclaration ('#facebox .content {display: block !important; height: 480px !important; overflow: auto; width: 560px !important; }');

//T.Trung
if($this->cart->couponCode){
    $db= JFactory::getDBO();
    $query = "SELECT id, coupon_value FROM #__awocoupon WHERE coupon_code = '".$this->cart->couponCode."'";
    $db->setQuery($query);
    $coupon = $db->loadObject();
    
    $query = "SELECT coupon_discount, shipping_discount FROM #__awocoupon_history WHERE coupon_id = ".$coupon->id."";
    $db->setQuery($query);
    $discount = $db->loadObject();
    if($discount){
        $coupon_value = $coupon->coupon_value - $discount->coupon_discount - $discount->shipping_discount;
    } else {
        $coupon_value = $coupon->coupon_value;
    }
}
//T.Trung end

?>

<div id="content" class="w-content undepages cart">
	<div class="eachBox boxCart">
		<h2>DIN INDKØBSKURV</h2>
		<?php if(count($this->cart->products)> 0){ ?>
		<div class="wrapTb clearfix">
			<div class="topbarcart clearfix">Varebeskrivelse</div>
			<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">
			<div class="wrapRowPro">
				
				<?php foreach ($this->cart->products as $pkey => $prow) {?>
				<div class="eachRowPro">
					<div class="proImg">
					<?php 
						$customhtml = $prow->customfields;
						preg_match("/<img .*?(?=src)src=\"([^\"]+)\" alt=\"([^\"]+)\"/si", $customhtml, $matches);
						if ($prow->virtuemart_media_id) {
							if (!empty($prow->image)) {
								if(count($matches)>0){
									echo $matches[0].'/>';
								} else {
									echo $prow->image->displayMediaThumb ('', FALSE);
								}
							}
						}
					?>
					</div>
					<?php 
						$cusfinal = str_replace($matches[0]."  />", "", $prow->customfields);
						$cusfinal = str_replace('<span class="product-field-type-M"> </span><br />', '', $cusfinal);
					?>
					<div class="row rowAbove">
						<div class="proName">
						  <h2><?php echo JHTML::link ($prow->url, $prow->product_name);?></h2>
						  <p> <span class="spanlb">Varenummer:</span><span class="spanvl"><?php  echo $prow->product_sku ?></span></p>
						  <div class="proSize"><span class="spanvl"><?php echo $cusfinal ?></span></div>  
						</div>
						<?php
						if ($prow->step_order_level)$step=$prow->step_order_level;else $step=1;
						if($step==0)$step=1;
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
						<div class="wrapedit"><span>Antal</span> 
							<input type="text"
							   onblur="check<?php echo $step?>(this);"
							   onclick="check<?php echo $step?>(this);"
							   onchange="check<?php echo $step?>(this);"
							   onsubmit="check<?php echo $step?>(this);"
							   title="<?php echo  JText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="inputNumber quantity-input js-recalculate mwc-qty<?php echo $x ?>" size="3" maxlength="4" name="quantity[<?php echo $prow->cart_item_id ?>]" value="<?php echo $prow->quantity ?>" />
							   <!--<a class="update" href="javscript:void(0);">Opdatere</a>-->
							 <input type="submit" class="vmicon vm2-add_quantity_cart update" name="update[<?php echo $prow->cart_item_id ?>]" title="<?php echo  JText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" align="middle" value="Opdatere"/>	
						</div> 
					</div> 
					<div class="row rowBelow"> 
						<div class="proPriceTT"><span class="spanlb">Pris i alt:</span><span class="spanvl"><?php echo number_format($this->cart->pricesUnformatted[$pkey]['salesPrice']*$prow->quantity, 2,',','.'). ' DKK';?></span></div> 
					</div>  
					<a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart&task=delete&cart_virtuemart_product_id=' . $prow->cart_item_id) ?>" class="proDel"><img src="templates/rodorve/mobile/img/btnDel.jpg" alt="Delete"></a>
				</div><!--eachRowPro-->
				<?php }?>
			</div>
			<!--wrapRowPro-->
			<div class="wrapTotalPrice clearfix">
				<div class="box boxright">
					<div class="eachRow r-nor clearfix"> <span class="lbNor">SUBTOTAL INKL. MOMS: </span> <span class="lbPrice"><?php echo number_format($this->cart->pricesUnformatted['salesPrice'],2,',','.').' DKK'; ?></span> </div>
					<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
					<div class="eachRow r-nor clearfix"> <span class="lbNor">GAVEKORT KUPON: </span> <span class="lbPrice"><?php echo $this->currencyDisplay->priceDisplay ($this->cart->pricesUnformatted['salesPriceCoupon']);?></span> </div>
					<?php }?>
					<div class="eachRow r-total clearfix"> <span class="lbTotal">AT BETALE INKL. MOMS:</span> <span class="totalPrice"><?php echo number_format($this->cart->pricesUnformatted['billTotal'],2,',','.').' DKK'; ?></span> </div>
					<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
					<div class="eachRow r-nor clearfix"> (Gavekort restbeløb: <?php echo number_format($coupon_value + $this->cart->pricesUnformatted['salesPriceCoupon'],2,',','.').' DKK'; ?>) </div>
					<?php }?>
				</div>
			</div>
			<input type="hidden" name="tosAccepted" value="1">		
			<input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
			<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
			<input type='hidden' name='task' value='<?php echo $this->checkout_task; ?>'/>
			<input type='hidden' name='option' value='com_virtuemart'/>
			<input type='hidden' name='view' value='cart'/>
			</form>
			<!--wrapTotalPrice-->
			<div class="graris">
				<p>Har du en værdikode?</p>
				<div class="frm_coupon clearfix">
					<form method="post" id="userForm" name="enterCouponCode" action="<?php echo JRoute::_('index.php'); ?>">
						<input name="coupon_code" placeholder="Indtast koden her ...">
						<a href="javascript:void(0);" class="btn2" onclick="document.getElementById('Send').click();">Aktiver</a>
						<input type="submit" style="display:none" name="Send" id="Send" value="Send">
						<input type="hidden" name="option" value="com_virtuemart" />
						<input type="hidden" name="view" value="cart" />
						<input type="hidden" name="task" value="setcoupon" />
						<input type="hidden" name="controller" value="cart" />
					</form>
				</div>
			</div>
			<?php if(count($this->cart->products)> 0){ ?>
			<?php $continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink); ?>
			<div class="wrap-button">
				<a class="btn2 btnGray btnBackShop" href="<?php echo $continue_link ?>"><span class="back"><<</span> Shop videre</a>
				<a class="btn2 btntoPayment" href="<?php echo JURI::base().'user/editaddresscheckoutBT.html';?>">Gå til kassen <span class="next">>></span></a>
			</div>
			<?php }?>
			<!--wrap-button --> 
		</div>
		<?php }?>
		<!--  wrapTb--> 
		
	</div>
	<!--eachBox boxCart-->
</div>
<?php return;?>
<div class="template2" style="margin-top:-30px;">
	<div class="cart_page clearfix">
		<h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h2>
		<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">
			<?php


	// This displays the pricelist MUST be done with tables, because it is also used for the emails
	echo $this->loadTemplate ('pricelist');

	// added in 2.0.8
	?>
			<input type="hidden" name="tosAccepted" value="1">
			<input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
			<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
			<input type='hidden' name='task' value='<?php echo $this->checkout_task; ?>'/>
			<input type='hidden' name='option' value='com_virtuemart'/>
			<input type='hidden' name='view' value='cart'/>
		</form>
		<?php if(count($this->cart->products)> 0){ ?>
		<?php $continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink); ?>
		<?php echo $this->loadTemplate ('coupon');?> <a class="btnShopvidere fl hover" href="<?php echo $continue_link ?>">Shop videre</a> <a class="btnCheckout fr hover" href="<?php echo JURI::base().'user/editaddresscheckoutBT.html';?>">Gå til kassen</a>
		<?php } ?>
	</div>
</div>
<?php vmTime('Cart view Finished task ','Start'); ?>

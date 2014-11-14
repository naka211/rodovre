<?php
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
//vmJsApi::js ('facebox');
//vmJsApi::css ('facebox');
JHtml::_ ('behavior.formvalidation');
//$document = JFactory::getDocument ();
/*$document->addScriptDeclaration ("

//<![CDATA[
	jQuery(document).ready(function($) {
		$('div#full-tos').hide();
		$('a#terms-of-service').click(function(event) {
			event.preventDefault();
			$.facebox( { div: '#full-tos' }, 'my-groovy-style');
		});
	});

//]]>
");*/
/*$document->addScriptDeclaration ("

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

");*/
//$document->addStyleDeclaration ('#facebox .content {display: block !important; height: 480px !important; overflow: auto; width: 560px !important; }');

//vmdebug('car7t pricesUnformatted',$this->cart->pricesUnformatted);
//vmdebug('cart cart',$this->cart->pricesUnformatted );
$tmplURL=JURI::base()."templates/".$template;
?>
<div id="cart-page">
<div class="w-cart-page">
	<div class="title-cart">
		<h2><?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h2>
		<?php if(!$this->cart->products) echo '<a href="javascript:history.back()" style="display:block;float:right">Tilbage</a>';
		else{?>
		<div class="bnt-secure-payment m-t">
        	<a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=user&layout=editaddresscheckoutBT')?>">Gå til sikker betaling</a>
		<?php
			//echo $this->checkout_link_html;
		?>
		</div>
		<?php }?>
	</div>
	<?php // Continue Shopping Button
	/*if ($this->continue_link_html != '') {
		echo $this->continue_link_html;
	}*/

	//echo shopFunctionsF::getLoginForm ($this->cart, FALSE);
        //
	// This displays the pricelist MUST be done with tables, because it is also used for the emails
	echo $this->loadTemplate ('pricelist');
	if ($this->checkout_task) {
		$taskRoute = '&task=' . $this->checkout_task;
	}
	else {
		$taskRoute = '';
	}
	?>
	<div class="info-payment">
		<div class="info-payment-top">
		<div class="func">
		<div class="w-135"> <img src="<?php echo $tmplURL?>/img/phone.png" width="17" height="16" alt=""> <span>3250 3611</span> </div>
		<div class="w-135"> <img src="<?php echo $tmplURL?>/img/times.png" width="14" height="17" alt=""> <span>Hurtig levering</span> </div>
		<div class="w-135" style="width:235px;"> <img src="<?php echo $tmplURL?>/img/truck.png" width="17" height="14" alt=""> <span>Gratis fragt ved køb over 500 DKK</span> </div>
		<div class="clear"></div>
		<div class="w-135"> <img src="<?php echo $tmplURL?>/img/sticker.png" width="15" height="16" alt=""> <span>Sikker betaling</span> </div>
		<div class="w-135"> <img src="<?php echo $tmplURL?>/img/star.png" width="13" height="16" alt=""> <span>14 dages returret</span> </div>
		<div class="w-135" style="width:235px;"> <img src="<?php echo $tmplURL?>/img/sitting.png" width="17" height="16" alt=""> <span>2 års reklamationsret</span> </div>
		</div>
		</div>
		<div class="info-payment-bot">
		<h3>Du kan betale med følgende betalingskort: </h3>
		<ul>
		<li><a href="#"><img src="<?php echo $tmplURL?>/img/icon-dk.png" width="37" height="19" alt=""></a></li>
		<li><a href="#"><img src="<?php echo $tmplURL?>/img/icon-mastercard.png" width="37" height="19" alt=""></a></li>
		<li><a href="#"><img src="<?php echo $tmplURL?>/img/icon-card2.png" width="37" height="19" alt=""></a></li>
		<li><a href="#"><img src="<?php echo $tmplURL?>/img/icon-visa.png" width="37" height="19" alt=""></a></li>
		<li><a href="#"><img src="<?php echo $tmplURL?>/img/visa2.png" width="37" height="19" alt=""></a></li>
		<li><a href="#"><img src="<?php echo $tmplURL?>/img/icon-ean.png" width="95" height="19" alt=""></a></li>
		</ul>
		</div>
	</div>

	<div class="total-vat">
	<?php
	$salesPrice=$this->currencyDisplay->priceDisplay($this->cart->pricesUnformatted['salesPrice'],0,1.0,false,2);
	$finalprice= $this->currencyDisplay->priceDisplay($this->cart->pricesUnformatted['billTotal'],0,1.0,false,2);
	
	//var_dump($this->cart);
	?>
		<div>
		<label>Subtotal inkl. moms:</label><span><?php echo $salesPrice?></span>
		</div>
		<div>
		<label>Heraf moms:</label><span><?php echo $this->currencyDisplay->priceDisplay($this->cart->pricesUnformatted['salesPrice']*.2,0,1.0,false,2)?></span>
		</div>
		
		
		<?php if (!empty($this->cart->cartData['couponCode'])) { ?>
		<div>
      	<label><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT');?>:</label><span><?php echo $this->currencyDisplay->priceDisplay ($this->cart->pricesUnformatted['salesPriceCoupon'])?></span>
      	</div>
      	<?php } ?>
		
		
		<div class="n-b-b2">
		<label class="black">TOTAL INKL. MOMS:</label><span class="black"><?php echo $finalprice?></span>
		</div>
	</div>
	<form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">

		<input type='hidden' id='STsameAsBT' name='STsameAsBT' value='<?php echo $this->cart->STsameAsBT; ?>'/>
		<input type='hidden' name='task' value='<?php echo $this->checkout_task; ?>'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
	</form>
</div>
		<?php if($this->cart->products){?>
		<?php if (empty($this->cart->cartData['couponCode'])) { ?>
	
	        <?php
					if (VmConfig::get ('coupons_enable')) {
						if (!empty($this->layoutName) && $this->layoutName == 'default') {
							echo $this->loadTemplate ('coupon');
						} 
					}
			?>
						
        <?php }?>
	<div class="bnt-secure-payment">
    	<a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=user&layout=editaddresscheckoutBT')?>">Gå til sikker betaling</a>
<?php
	//echo $this->checkout_link_html;
?>
	</div>
	<?php }?>
</div>
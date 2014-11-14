<?php //	THIS LAYOUT INCLUDE 4 PARTS, BECOME ONE PAGE CHECKOUT
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
// vmdebug('user edit address',$this->userFields['fields']);
// Implement Joomla's form validation
JHTML::_ ('behavior.formvalidation');

$cart = VirtueMartCart::getCart(); 
$cart->prepareCartViewData();
//print_r($cart);exit;

foreach($cart->products as $product){
    $category_arr[] = $product->virtuemart_category_id;
}
if($category_arr[0] == 14){
    $isGiftCard = true;
} else {
    $isGiftCard = false;
}

if($cart->couponCode){
    $db= JFactory::getDBO();
    $query = "SELECT id, coupon_value FROM #__awocoupon WHERE coupon_code = '".$cart->couponCode."'";
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
?>
<script type="text/javascript">
jQuery(document).ready(function(){

    $('#checkoutForm input[type=checkbox]').on('change invalid', function() {
        var textfield = $(this).get(0);
        textfield.setCustomValidity('');
        
        if (!textfield.validity.valid) {
          textfield.setCustomValidity('Venligst accepterer vores handelsbetingelser');  
        }
    });
    
	var company = '<input class="input required" type="text" placeholder="Firmanavn *" name="company" id="company"><input class="input required" type="text" placeholder="CVR-nr. *" name="cvr" id="cvr">';
	var public = '<input class="input required" type="text" placeholder="EAN-nr. *" name="ean" id="ean" maxlength="13"><input class="input required" type="text" placeholder="Myndighed/Institution *" name="authority" id="authority"><input class="input required" type="text" placeholder="Ordre- el. rekvisitionsnr. *" name="order1" id="order1"><input class="input required" type="text" placeholder="Personreference *" name="person" id="person">';
	
	jQuery("#choicemaker").change(function () {

		value = jQuery("#choicemaker").val();

		if(value == 1){
			jQuery("#addInfo").html('');
            jQuery("#pay1").attr("disabled", false);
            jQuery("#pay2").attr("disabled", false);
            
            jQuery("#pay3").attr("name", "");
            jQuery("#pay3").val(0);
		} else if(value == 2){
			jQuery("#addInfo").html(company);
            jQuery("#pay1").attr("disabled", false);
            jQuery("#pay2").attr("disabled", false);
            
            jQuery("#pay3").attr("name", "");
            jQuery("#pay3").val(0);
		} else {
			jQuery("#addInfo").html(public);
            jQuery("#pay1").attr("disabled", "disabled");
            jQuery("#pay2").attr("disabled", "disabled");
            
            jQuery("#pay3").attr("name", "virtuemart_paymentmethod_id");
            jQuery("#pay3").val(3);
		}

	});
	
	/*jQuery('.bnt-create-acc').click(function(){
		jQuery(".w-create-acc").slideToggle();
	});*/
    
	changeDelivery = function(val){
		if(val == 1){
			jQuery("#shipPriceLabel1").html("0,00 DKK");
            var totalValue = jQuery("#total").val();
			<?php if (!empty($cart->cartData['couponCode'])) { ?>
            var couponValue = jQuery("#coupon_value").val();
            var subTotalValue = jQuery("#subtotal").val();
            var tmp = parseFloat((subTotalValue)*0.1);
            
            var tmp1 = couponValue - subTotalValue;
            var tmp2 = couponValue - subTotalValue + tmp;
            
            if((tmp1 > 0)){
                jQuery("#payTotal").html("0,00 DKK");
                var couponText = formatMoney(parseFloat(couponValue) - parseFloat(subTotalValue) + tmp);
                jQuery("#balance").html(couponText + " DKK");
            } 
            
            if((tmp1 < 0) && (tmp2 < 0)){
                var totalText = formatMoney(parseFloat(subTotalValue) - parseFloat(couponValue) - tmp); 
                jQuery("#payTotal").html(totalText + " DKK");
                
                jQuery("#balance").html("0,00 DKK");
            }
            
            if((tmp1 < 0) && (tmp2 > 0)){
                jQuery("#payTotal").html("0,00 DKK");
                var couponText = formatMoney(tmp - (parseFloat(subTotalValue) - parseFloat(couponValue)));
                jQuery("#balance").html(couponText + " DKK");
            }
            <?php } else {?>
			var totalText = formatMoney(parseFloat(totalValue*0.9)); 
            jQuery("#payTotal").html(totalText + " DKK");
			<?php }?>
            jQuery("#deduct").show();
            
		} else {
            var totalValue = jQuery("#total").val();
            var couponValue = jQuery("#coupon_value").val();
            var subTotalValue = jQuery("#subtotal").val();
            var shipValue = jQuery("#shipFee").val();
            
            var tmp1 = couponValue - subTotalValue;
            var tmp2 = couponValue - subTotalValue - shipValue;
            
            if(tmp1 <= 0){
                var total = formatMoney(parseFloat(Number(subTotalValue) - Number(couponValue) + Number(shipValue)));
			    jQuery("#payTotal").html(total+" DKK");
            }
            
            if((tmp1 > 0) && (tmp2 > 0)){
                jQuery("#payTotal").html("0,00 DKK");
                var couponText = formatMoney(parseFloat(couponValue) - parseFloat(subTotalValue) -  Number(shipValue));
                jQuery("#balance").html(couponText + " DKK");
            }
            
            if((tmp1 > 0) && (tmp2 < 0)){
                var totalText = formatMoney(Number(shipValue) - (parseFloat(couponValue) - parseFloat(subTotalValue))); 
                jQuery("#payTotal").html(totalText + " DKK");
                jQuery("#balance").html("0,00 DKK");
            }
            
            jQuery("#shipPriceLabel1").html(shipValue + ",00 DKK");
			
            
            jQuery("#deduct").hide();
        }
	}

	formatMoney = function(num){
		var p = num.toFixed(2).split(".");
		return p[0].split("").reverse().reduce(function(acc, num, i, orig) {
			return  num + (i && !(i % 3) ? "." : "") + acc;
		}, "") + "," + p[1];
	}
	//isST process
    
	setDelivery = function(zipcode){
        if(zipcode){
            zipcode = Number(zipcode);
            if(zipcode < 5000) {
                jQuery("#ship1").val(2);
                var subtotal = Number(jQuery("#subtotal").val());
                if(subtotal <= 1000){
                    var fee = 150;
                } else {
                    var fee = 0;
                }
                var text = 'Leveret på Sjælland: ';
            } else {
                jQuery("#ship1").val(3);
                var fee = 350;
                var text = 'Leveret til døren for Fyn og Jylland: ';
            }
            <?php if($isGiftCard){?>
            fee = 0;
            <?php }?>
            jQuery("#shipFee").val(fee);
            jQuery("#shipPriceLabel").html(text+fee+" DKK");
            jQuery("#shipPriceLabel1").html(fee+" DKK");
            jQuery("#shipMethod").show();
            changeDelivery(jQuery('input[name=virtuemart_shipmentmethod_id]:checked', '#checkoutForm').val());
        }
	}
	
    var zip = jQuery("#zip").val();
	if(zip){
		setDelivery(zip);
	}
    
    jQuery("#zip").blur(function(){
		var zip = jQuery("#zip").val();
		jQuery.ajax({
            type: "POST",
            url: "<?php echo JURI::base();?>index.php?option=com_virtuemart&controller=cart&task=requestCity",
            data: {zip: zip}
        }).done(function(result) {
            if(result){
                if(result == 0){
					jQuery("#city").val('');
				} else {
					jQuery("#city").val(result);
				}
				
				if(!jQuery("#st_zip").val()){
					setDelivery(zip);
				}
            }
		});
    });
    
    shipTo = function(){
        if(jQuery(".w_Address").css("display")=="none"){
            jQuery(".w_Address").html("");
            jQuery("#STsameAsBT").val("1");
			setDelivery(jQuery("#zip").val());
        } else {
            <?php if($isGiftCard){?>
            var st_html = '<input class="input required" type="text" placeholder="Modtagerens fornavn*" name="st_first_name" id="st_first_name"><input class="input required" type="text" placeholder="Modtagerens efternavn*" name="st_last_name" id="st_last_name"><input class="input required" type="text" placeholder="Email på modtager* " name="st_email" id="st_email"><textarea class="textarea" placeholder="Evt. besked til modtageren: Her kan du skrive en lykønskning eller besked til modtageren" name="st_message1"></textarea>';
            <?php } else {?>
            var st_html = '<input class="input required" type="text" placeholder="Fornavn*" name="st_first_name" id="st_first_name"><input class="input required" type="text" placeholder="Efternavn*" name="st_last_name" id="st_last_name"><input class="input required" type="text" placeholder="Vejnavn*" name="st_street_name" id="st_street_name"><input class="input required" type="text" placeholder="Hus/gade nr.*" name="st_street_number" id="st_street_number"><input class="inputPostnr input required" type="text" placeholder="Postnr.*" name="st_zip" id="st_zip" maxlength="4"><input class="inputBynavn input" type="text" placeholder="Bynavn*" name="st_city" id="st_city"><input class="input required" type="text" placeholder="Telefon*" name="st_phone" id="st_phone">';
            <?php }?>
            jQuery(".w_Address").html(st_html);
            jQuery("#STsameAsBT").val("0");
            
            
            jQuery("#st_zip").blur(function(){
                var st_zip = jQuery("#st_zip").val();
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo JURI::base();?>index.php?option=com_virtuemart&controller=cart&task=requestCity",
                    data: {zip: st_zip}
                }).done(function(result) {
                    jQuery("#st_city").val(result);
					setDelivery(st_zip);
                });
            });
        }
    }

    <?php if($isGiftCard){?>
    jQuery(".w_Address").show();
    jQuery("#deduct").hide();
    jQuery("#shipPriceLabel1").html("0,00 DKK");
    <?php } else {?>
    jQuery(".w_Address").hide();
    <?php }?>
        
    shipTo();
        
    /*jQuery('#showDelivery').click(function(event){
        jQuery("#w_Address").slideToggle("1000","swing", function(){alert('2222');
            //shipTo();
        });
    });*/
});

function showDelivery(){
	jQuery(".w_Address").slideToggle("500","swing", function(){
		shipTo();
	});
}


</script>
<form method="post" id="checkoutForm" name="userForm" class="form-validate" style="padding:0;border-top:none" action="index.php">
<div id="content" class="w-content undepages checkout">
	<div class="eachBox boxCheckout">
		<div class="clearfix">
			<div class="colCK step col-left-checkout clearfix">
				<h2><span>1</span>Kundeoplysninger</h2>
				<div class="entryInfo">
					<h3>INDTAST DINE OPLYSNINGER:</h3>
					<label>Vælg kundetype *</label>
					<select id="choicemaker" name="mwctype" class="input select">
						<option value="1">Privat</option>
						<option value="2">Erhverv</option>
						<option value="3">Offentlig instans</option>
					</select>
					<div id="w_privat" class="clearfix">
						<span id="addInfo"></span>
						<input class="input required" type="text" placeholder="Fornavn *" name="first_name" id="first_name">
						<input class="input required" type="text" placeholder="Efternavn *" name="last_name" id="last_name">
						<input class="input required" type="text" placeholder="Vejnavn *" name="street_name" id="street_name">
						<input class="input required" type="text" placeholder="Hus/gade nr. *" name="street_number" id="street_number">
						<input class="input inputPostnr required" type="text" placeholder="Postnr. *" name="zip" id="zip">
						<input class="input inputBynavn required" type="text" placeholder="Bynavn *" name="city" id="city">
						<input class="input required" type="text" placeholder="Telefon *" name="phone_1" id="phone_1">
						<input class="input required validate-email" type="text" placeholder="E-mail adresse *" name="email" id="email">
						<textarea class="textarea" placeholder="Evt. din besked" name="message1"></textarea>
					</div>
				</div>
				<!--entryInfo-->
				<?php if(!$isGiftCard){?>
				<div class="btn2 bnt-another-add showDelivery" onClick="showDelivery();" ><span class="iconHome-ck"><img src="templates/scheellarsen/mobile/img/iconHome-ck.png"></span> Levering til anden adresse</div>
				<?php } else {?>
				<div class="btn2 bnt-another-add showDelivery" onClick="showDelivery();" ><span class="iconHome-ck"><img src="templates/scheellarsen/mobile/img/gift.png"></span> Modtageren af gavekortet</div>
				<?php }?>
				<div class="w-another-add w_Address"></div>
				<p>(Felter markeret med * skal udfyldes)</p>
			</div>
			<!--col-left-checkout-->
			
			<div class="colCK col-right-checkout clearfix">
				<div class="step step2 clearfix">
					<h2><span>2</span>Levering</h2>
					<?php if(!$isGiftCard){?>
					<div class="eachRow" id="shipMethod" style="display:none;">
						<label><input class="lbradio lb1" id="ship1" name="virtuemart_shipmentmethod_id" value="0" checked="checked" type="radio" onChange="changeDelivery(this.value)"><span id="shipPriceLabel"></span></label>
					</div>
					<div class="eachRow">
						<label><input class="lbradio lb1" id="ship2" name="virtuemart_shipmentmethod_id" value="1" checked="checked" type="radio" onChange="changeDelivery(this.value)">Ved afhentning på Hesselrødvej 26, 2980 Kokkedal, sparer du 10%, som vil blive fratrukket automatisk</label></div>
					<?php } else {?>
					<div class="eachRow">
						<label><input class="lbradio lb1" name="virtuemart_shipmentmethod_id" value="4" checked="checked" type="radio">Gavekort til modtageren vil blive leveret pr. mail</label></div>
					<?php }?>
				</div>
				<!--step2-->
				<div class="step step3 clearfix">
					<h2><span>3</span>BETALINGSMETODE</h2>
					<p>Du kan betale med følgende betalingskort:</p>
					<label>
						<input class="lbradio" name="virtuemart_paymentmethod_id" type="radio" checked="checked" value="1" id="pay1">
						<img class="cart-icon" src="templates/scheellarsen/mobile/img/cart-ck.png"></label>
					<label>
						<input class="lbradio" name="virtuemart_paymentmethod_id" type="radio" value="2" id="pay2">
						<a class="btnCartcredit fancybox" href="#ppCartcredit"><img src="templates/scheellarsen/mobile/img/icon_via.png" alt=""></a></label>
				</div>
				<!--step3-->
				<div class="step  step4 clearfix">
					<h2><span>4</span>Ordreoversigt</h2>
					<div class="wrapTb clearfix">
						<div class="topbarcart clearfix">Varebeskrivelse</div>
						<div class="wrapRowPro">
							<?php foreach($cart->products as $product){
								if(count($product->customPrices) > 1){
									preg_match_all("#<span class=\"product-field-type-S\"> [\w\W]*?</span>#", $product->customfields, $tmp);
									$select1 = $tmp[0][0];
									$select2 = $tmp[0][1];
									preg_match("#src=\"([\w\W]*?)\" alt#", $product->customfields, $tmp1);
									$img = $tmp1[1];
								} else {
									preg_match("#<span class=\"product-field-type-V\">[\w\W]*?</span>#", $product->customfields, $tmp);
									$select1 = $tmp[0];
									$select2 = '';
									$img = $product->image->file_url_thumb;
								}
							?>
							<div class="eachRowPro">
								<div class="proImg"><?php 
                                if(strlen($img)>4){
                                    echo '<img width="90" src="'.$img.'"> ';
                                }else{
                                    echo $product->image->displayMediaThumb ('', FALSE);
                                }
                                ?></div>
								<div class="row rowAbove">
									<div class="proName">
										<h2><?php echo $product->product_name;?></h2>
										<p> <span class="spanlb">Varenummer:</span><span class="spanvl"><?php echo $product->product_sku;?></span></p>
										<div class="proSize"><span class="spanvl">Højde 21 cm-Ø27 cm</span></div>
										<?php if($select1){?><div class="proSize"><span class="spanvl"><?php echo $select1;?></span></div><?php }?>
                                		<?php if($select2){?><div class="proSize"><span class="spanvl"><?php echo $select2;?></span></div><?php }?>
									</div>
									<div class="wrapedit"><span>Antal</span> <span><?php echo $product->quantity;?></span> </div>
								</div>
								<div class="row rowBelow">
									<div class="proPriceTT"><span class="spanlb">Pris i alt:</span><span class="spanvl"><?php echo number_format($product->prices['salesPrice']*$product->quantity,2,',','.').' DKK';?></span></div>
								</div>
							</div>
							<!--eachRowPro-->
							<?php }?>
						</div>
						<!--wrapRowPro-->
						<div class="wrapTotalPrice clearfix">
							<div class="box boxright">
								<div class="eachRow r-nor clearfix"> <span class="lbNor">SUBTOTAL INKL. MOMS: </span> <span class="lbPrice"><?php echo number_format($cart->pricesUnformatted['salesPrice'],2,',','.').' DKK'; ?></span> </div>
								<div class="eachRow r-nor clearfix"> <span class="lbNor">FRAGT: </span> <span class="lbPrice" id="shipPriceLabel1"></span> </div>
								<?php if (!empty($cart->cartData['couponCode'])) { ?>
								<div class="eachRow r-nor clearfix"> <span class="lbNor">GAVEKORT KUPON: </span> <span class="lbPrice" id="discountText"><?php echo number_format ($cart->pricesUnformatted['salesPriceCoupon'],2,',','.').' DKK'; ?></span> </div>
                                <?php } ?>
								<div class="eachRow r-nor clearfix" id="deduct"> <span class="lbNor">RABAT 10% VED AFHENTNING: </span> <span class="lbPrice"><?php echo '-'.number_format($cart->pricesUnformatted['salesPrice']*0.1,2,',','.').' DKK'; ?></span> </div>
								<div class="eachRow r-total clearfix"> <span class="lbTotal">AT BETALE INKL. MOMS:</span> <span class="totalPrice" id="payTotal"><?php echo number_format($cart->pricesUnformatted['billTotal'],2,',','.').' DKK'; ?></span> </div>
								<?php if (!empty($cart->cartData['couponCode'])) { ?>
								<div class="eachRow r-nor clearfix"> (Gavekort restbeløb: <span id="balance"><?php echo number_format($coupon_value + $cart->pricesUnformatted['salesPriceCoupon'],2,',','.').' DKK'; ?></span>) </div>
                                <?php } ?>
							</div>
						</div>
						<!--wrapTotalPrice--> 
					</div>
					<!-- wrapTb -->
					
					<div class="read-terms clearfix">
						<label>
							<input name="tosAccepted" id="tosAccepted" type="checkbox" value="1" class="required">
							Jeg bekræfter hermed at mine data er korrekte, samt at kurven indeholder de varer jeg ønsker. <a target="_blank" href="index.php?option=com_content&view=article&id=8&Itemid=131">Jeg accepterer Handelsbetingelser.</a></label>
					</div>
				</div>
				<!--step4--> 
			</div>
			<!--col-right --> 
		</div>
		<!-- clearfix -->
		<div class="wrap-button">
			<a class="btn2 btnGray btnBackShop" href="index.php"><span class="back"><<</span> Til Varekurv</a>
			<button type="submit" class="validate btn2 btntoPayment" style="cursor:pointer; border:none;">Til Betaling</button>
		</div>
		<!--wrap-button -->
		
		<input type="hidden" id="coupon_value" value="<?php echo $coupon_value;?>" />
		<input type="hidden" id="subtotal" value="<?php echo $cart->pricesUnformatted['salesPrice']?>" />
		<input type="hidden" id="total" value="<?php echo $cart->pricesUnformatted['billTotal']?>" />
		<input type="hidden" id="shipFee" value=""/>
		<input type="hidden" id="pay3" name="" value=""/>
		
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="view" value="cart"/>
		<input type="hidden" name="task" value="confirm"/>
		<input type='hidden' id='STsameAsBT' name='STsameAsBT' value=''/>
		<?php
        echo JHTML::_ ('form.token');
        ?>
		
	</div>
	<!--eachBox boxCheckout-->
	<div id="ppCartcredit" style="display: none;">
		<div class="wrap-pp wrap-cartcredit">
			<h4>ViaBill betingelser</h4>
			<p>I samarbejde med ViaBill kan vi tilbyde faktura eller delbetaling. Det betyder at du kan købe dine varer nu, og vente med at betale.</p>
			<p>Når du gennemfører din bestilling, skal du vælge blot vælge ” ViaBill" som betalingsmetode. Klik på ” Gå til betaling ”og du bliver ført til en sikker side som kun ViaBill har adgang til at se, her gennemføres et kredittjek. Du får svar med det samme, og derefter er bestillingen gennemført.</p>
			<p>Vi sender dig dine varer og en faktura som dokumentation på din ordre, sideløbende vil du modtage en opkrævning fra ViaBill, det er denne du skal indbetale efter. </p>
			<p>Har du spørgsmål, er du meget velkommen til at kontakte vores kundeservice på tlf. 4930 1699. </p>
			<p>Alle spørgsmål vedrørende betaling af en faktura, skal rettes til ViaBill på telefon 88 826 826, da det er dem der yder kreditten. </p>
		</div>
		<!--wrap-cartcredit--> 
	</div>
	<!--ppCartcredit--> 
	
</div>
</form>
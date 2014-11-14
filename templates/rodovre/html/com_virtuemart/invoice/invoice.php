<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
//print_r($this->orderDetails);exit;
if($this->orderDetails['details']['BT']->company){
    $type = "Erhverv";
} else if($this->orderDetails['details']['BT']->ean){
	$type = "Offentlig instans";
} else {
	$type = "Privat";
}

if(!$this->orderDetails['details']['ST']){
    $this->orderDetails['details']['ST'] = $this->orderDetails['details']['BT'];
}

if($this->orderDetails["items"][0]->virtuemart_category_id == 14){
    $isGiftCard = true;
} else {
    $isGiftCard = false;
}

//T.Trung
if($this->orderDetails['details']['BT']->coupon_code){
    $db= JFactory::getDBO();
    $query = "SELECT id, coupon_value FROM #__awocoupon WHERE coupon_code = '".$this->orderDetails['details']['BT']->coupon_code."'";
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
<table border="0" cellspacing="0" cellpadding="0" style="margin: 15px; background: #fff; border: 1px solid #646464;">
	<tr>
		<td colspan="4"><h2 style="color: #B48944; border-bottom: 1px dotted #CACACA; padding: 10px; margin: 0;">FAKTURA</h2></td>
	</tr>

	<tr>
		<td height="30" style="padding-left: 10px"><strong>Ordrenummer:</strong></td>
		<td height="30" style="padding-left: 10px"><strong><?php echo $this->orderDetails['details']['BT']->order_number;?></strong></td>
	</tr>

	<tr>
	<td colspan="2">&nbsp;</td>
	</tr>

	<tr style="padding-left: 20px">
		<td colspan="2">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				<td colspan="2" height="30" style="padding-left: 10px"><strong>Kundeoplysninger:</strong></td>
				</tr>
                <tr>
					<td width="50%" height="30" style="padding-left: 10px">Kundetype:</td>
					<td width="50%" height="30"><?php echo $type;?></td>
				</tr>

<?php if($this->orderDetails['details']['BT']->company){?>
				<tr>
					<td width="50%" height="30" style="padding-left: 10px">Firmanavn:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['BT']->company;?></td>
				</tr>

				<tr>
					<td width="50%" height="30" style="padding-left: 10px">CVR-nr.:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['BT']->cvr;?></td>
				</tr>
<?php } else if($this->orderDetails['details']['BT']->ean){?>
				<tr>
					<td width="50%" height="30" style="padding-left: 10px">EAN-nr.:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['BT']->ean;?></td>
				</tr>

				<tr>
					<td width="50%" height="30" style="padding-left: 10px">Myndighed/Institution:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['BT']->authority;?></td>
				</tr>

				<tr>
					<td width="50%" height="30" style="padding-left: 10px">Ordre- el. rekvisitionsnr.:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['BT']->order1;?></td>
				</tr>

				<tr>
					<td width="50%" height="30" style="padding-left: 10px">Personreference:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['BT']->person;?></td>
				</tr>
<?php }?>
				<tr>
					<td width="50%" height="30" style="padding-left: 10px">Fornavn:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['BT']->first_name;?></td>
				</tr>

				<tr>
					<td height="30" style="padding-left: 10px">Efternavn:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->last_name;?></td>
				</tr>

				<tr>
					<td height="30" style="padding-left: 10px">Vejnavn:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->street_name;?></td>
				</tr>
                
                <tr>
					<td height="30" style="padding-left: 10px">Hus/gade nr.:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->street_number;?></td>
				</tr>

				<tr>
					<td height="30" style="padding-left: 10px">Postnr.:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->zip;?></td>
				</tr>

				<tr>
					<td height="30" style="padding-left: 10px">By:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->city;?></td>
				</tr>

				<tr>
					<td height="30" style="padding-left: 10px">Telefon:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->phone_1;?></td>
				</tr>
                <tr>
					<td height="30" style="padding-left: 10px">E-mail:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->email;?></td>
				</tr>
                <tr>
					<td height="30" style="padding-left: 10px">Besked:</td>
					<td height="30"><?php echo $this->orderDetails['details']['BT']->message1;?></td>
				</tr>
			</table>
		</td>

		<td colspan="2" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="2" height="30"><strong>
                    <?php if($isGiftCard){?>
                        Modtageren af gavekortet:
                    <?php } else {?>
                        Leveringsadresse:
                    <?php }?>
                    </strong></td>
				</tr>

				<tr>
					<td width="50%" height="30">Fornavn:</td>
					<td width="50%" height="30"><?php echo $this->orderDetails['details']['ST']->first_name;?></td>
				</tr>

				<tr>
					<td height="30">Efternavn:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->last_name;?></td>
				</tr>
                <?php if($isGiftCard){?>
                <tr>
					<td height="30">E-mail:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->email1;?></td>
				</tr>

				<tr>
					<td height="30">Besked:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->message1;?></td>
				</tr>
                <?php } else {?>
				<tr>
					<td height="30">Vejnavn:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->street_name;?></td>
				</tr>
                
                <tr>
					<td height="30">Hus/gade nr.:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->street_number;?></td>
				</tr>

				<tr>
					<td height="30">Postnr.:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->zip;?></td>
				</tr>

				<tr>
					<td height="30">By:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->city;?></td>
				</tr>

				<tr>
					<td height="30">Telefon:</td>
					<td height="30"><?php echo $this->orderDetails['details']['ST']->phone_1;?></td>
				</tr>
                <?php }?>
                <?php if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
                <tr>
					<td height="30" colspan="2"><strong style="color: red; font-size:18px;">Bemærk! Vi kontakter jer, når varen er klar afhentning</strong></td>
				</tr>
                <?php }?>
			</table>
		</td>
	</tr>

	<tr>
	<td colspan="4">&nbsp;</td>
	</tr>

	<tr style="padding-left: 20px">
		<td colspan="2">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
			<td style="padding-left: 10px"><strong>Betaling: </strong></td>
			</tr>
			<tr>
			<td valign="top" style="padding-left: 10px">
            <?php if($this->orderDetails['details']['BT']->virtuemart_paymentmethod_id == 3){?>
            Efterbetalte
            <?php } else {?>
            Kortbetaling
            <?php }?>
            </td>
			</tr>
			</table>
		</td>
		<td colspan="2">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
			<td><strong>Levering:</strong></td>
			</tr>
			<tr>
				<td valign="top" height="30">
                <?php if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
                Afhentning på Hesselrødvej 26, 2980 Kokkedal
                <?php } else if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id == 2){?>
                Leveret på Sjælland
                <?php } else if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id == 3){?>
                Leveret til døren for Fyn og Jylland
                <?php } else {?>
                Leveret via e-mail
                <?php }?>
                </td>
			</tr>
			</table>
		</td>
	</tr>

	<tr>
	<td colspan="4">&nbsp;</td>
	</tr>

	<tr style="padding-left: 20px">
	<td colspan="4">
	<table border="0" cellspacing="0" cellpadding="0" style="border: 1px solid #CACACA;line-height: 1.8em">
		<tr align="right" style="background: #EFEFEF;">
		<td width="40%" style="text-align: left; padding-left: 10px">Produkt</td>
		<td width="20%">Antal</td>
		<td width="20%">Pris pr. enhed</td>
		<td width="20%" style="padding-right: 20px">Pris i alt</td>
		</tr>

	<?php foreach($this->orderDetails['items'] as $item){
        $item->product_attribute = preg_replace('#"\s*<span.*?<\\\/span>#','"',$item->product_attribute);
        $attrs = json_decode($item->product_attribute);
        $attrs = (array)$attrs;    
    ?>
		<tr style="text-align: right;">
		<td style="border-bottom: 1px solid #CACACA; color: #3A3A3A; text-align: left; padding-left: 10px">
        <strong><?php echo $item->order_item_name;?></strong><br />
        Varenummer: <?php echo $item->order_item_sku;?><br />
        <?php $i = 1;
        foreach($attrs as $attr){
          if($i != 2){
        ?>
        <?php echo $attr;?><br />
        <?php }
        $i++;
        }?>
        </td>
		<td style="border-bottom: 1px solid #CACACA; color: #3A3A3A;"><?php echo $item->product_quantity;?></td>
		<td style="border-bottom: 1px solid #CACACA; color: #3A3A3A;"><?php echo number_format($item->product_final_price,2,',','.');?> DKK</td>
		<td style="border-bottom: 1px solid #CACACA; color: #3A3A3A;padding-right: 10px;"><?php echo number_format($item->product_final_price*$item->product_quantity,2,',','.');?> DKK</td>
		</tr>
	<?php }?>

		<tr>
		<td style="text-transform: uppercase; color: red; padding-left: 10px"></td>
		<td width="400" colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="color: #3A3A3A;">SUBTOTAL INKL. MOMS:</td>
		</tr>
        <tr>
			<td style="color: #3A3A3A;">FRAGT:</td>
		</tr>
        
        <?php if($this->orderDetails['details']['BT']->coupon_code){?>
        <tr>
			<td style="color: #3A3A3A;">Gavekort rabat:</td>
		</tr>
        <?php }?>
        
        <?php if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
        <tr>
			<td style="color: #3A3A3A;">Rabat 10% ved afhentning:</td>
		</tr>
        <?php }?>
		<tr>
			<td><strong>AT BETALE INKL. MOMS:</strong></td>
		</tr>
        <?php if($this->orderDetails['details']['BT']->coupon_code){?>
        <tr>
			<td style="color: #3A3A3A;">Gavekort restbeløb: <?php echo number_format($coupon_value,2,',','.').' DKK'; ?></td>
		</tr>
        <?php }?>
		</table></td>

		<td><table width="100%" border="0" cellpadding="0" cellspacing="0" style="text-align: right;" >
		<tr>
			<td style="padding: 0 10px; color: #3A3A3A;" ><?php echo number_format($this->orderDetails['details']['BT']->order_salesPrice,2,',','.');?> DKK</td>
		</tr>
        <tr>
			<td style="padding: 0 10px; color: #3A3A3A;"><?php echo number_format($this->orderDetails['details']['BT']->order_shipment,2,',','.');?> DKK</td>
		</tr>
        
        <?php if($this->orderDetails['details']['BT']->coupon_code){?>
        <tr>
			<td style="padding: 0 10px; color: #3A3A3A;"><?php echo number_format($this->orderDetails['details']['BT']->coupon_discount,2,',','.');?> DKK</td>
		</tr>
        <?php }?>
        
        <?php if($this->orderDetails['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
        <tr>
			<td style="padding: 0 10px; color: #3A3A3A;"><?php echo '-'.number_format($this->orderDetails['details']['BT']->order_subtotal*0.1,2,',','.');?> DKK</td>
		</tr>
        <?php }?>
		<tr>
			<td style="padding: 0 10px;"><strong><?php echo number_format($this->orderDetails['details']['BT']->order_total,2,',','.');?> DKK</strong></td>
		</tr>
        <?php if($this->orderDetails['details']['BT']->coupon_code){?>
        <tr>
			<td style="padding: 0 10px; color: #3A3A3A;">&nbsp;</td>
		</tr>
        <?php }?>
		</table></td>
		</tr>
	</table>
	</td>
	</tr>

	<tr><td></td></tr>

	<tr style="margin-top: 20px;">
	<td colspan="4" style="padding: 30px 10px; line-height: 1.8em;">
		Krukker & Havemøbler ApS - Hesselrødvej 26 - 2980 Kokkedal<br />
		Mobil: 41628001 - info@scheel-larsen.dk - CVR 30711912<br />
	</td>
	</tr>
</table>
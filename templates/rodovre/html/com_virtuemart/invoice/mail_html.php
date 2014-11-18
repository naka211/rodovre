<?php
/**
 *
 * Layout for the shopper mail, when he confirmed an ordner
 *
 * The addresses are reachable with $this->BTaddress, take a look for an exampel at shopper_adresses.php
 *
 * With $this->orderDetails['shipmentName'] or paymentName, you get the name of the used paymentmethod/shippmentmethod
 *
 * In the array order you have details and items ($this->orderDetails['details']), the items gather the products, but that is done directly from the cart data
 *
 * $this->orderDetails['details'] contains the raw address data (use the formatted ones, like BTaddress). Interesting informatin here is,
 * order_number ($this->orderDetails['details']['BT']->order_number), order_pass, coupon_code, order_status, order_status_name,
 * user_currency_rate, created_on, customer_note, ip_address
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers, Valerie Isaksen
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
//print_r($this->orderDetails);exit;
$db = JFactory::getDBO();
$orderDetail = $this->orderDetails['details']['BT'];
$orderid = $orderDetail->virtuemart_order_id;

if($this->orderDetails['details']['BT']->company){
    $type = "Erhverv";
} else if($this->orderDetails['details']['BT']->ean){
    $type = "Offentlig instans";
} else {
	$type = "Privat";
}

$query = "SELECT * FROM #__virtuemart_order_userinfos WHERE address_type = 'BT' AND virtuemart_order_id = ".$orderid;
$db->setQuery($query);
$BT_info = $db->loadObject();

$query = "SELECT * FROM #__virtuemart_order_userinfos WHERE address_type = 'ST' AND virtuemart_order_id = ".$orderid;
$db->setQuery($query);
$ST_info = $db->loadObject();

if(!$ST_info){
    $ST_info = $BT_info;
}

if($this->orderDetails["items"][0]->virtuemart_category_id == 71){
    $isGiftCard = true;
} else {
    $isGiftCard = false;
}

//T.Trung
if($orderDetail->coupon_code){
    $db= JFactory::getDBO();
    $query = "SELECT id, coupon_value FROM #__awocoupon WHERE coupon_code = '".$orderDetail->coupon_code."'";
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
<html lang="en">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>Email Template</title>
<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 13px;
    color: #505050;
    background-color: #fff;
}
a {
    color: #505050;
    text-decoration: none;
}
h2 {
    text-transform: uppercase;
}
h4 {
    margin: 5px 0;
    text-transform: uppercase;
    color: #000;
}
p {
    margin: 5px 0;
}
.page {
    width: 50%;
    margin: 0 auto;
    border: 1px solid #c6c6c6;
}
.red {
    color: red;
    font-size: 16px;
}
table tr td {
    padding: 5px;
}
table.list_SP {
    margin-top: 10px;
    padding: 0;
}
table.list_SP tr th {
    padding: 10px 5px;
    background-color: #EFECE1;
}
table.list_SP tr td {
    border-bottom: 1px solid #c6c6c6;
    padding: 5px 10px;
}
table.subtotal {
    background-color: #F9F7F3;
    padding: 0;
}
table.subtotal tr td {
    text-transform: uppercase;
    color: #000;
    padding: 10px;
    border: none;
}
table tr td table.top_info {
    padding: 0;
}
</style>
</head>

<body>
<div class="page">
	<div style="margin-top:20px;"><img src="<?php echo JURI::base().'templates/rodovre/img/logo.jpg'?>" /></div>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="padding:10px;">
        <tbody>
            <tr>
                <td colspan="4"><h2>ORDREOVERSIGT</h2></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Ordrenummer: <?php echo $orderDetail->order_number;?></strong></td>
            </tr>
            <tr>
                <td colspan="4"><table width="100%" class="top_info">
                        <tbody>
                            <tr>
                                <td width="50%"><table width="100%">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"><strong>Kundeoplysninger:</strong></td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Kundetype:</td>
                                                <td><?php echo $type;?></td>
                                            </tr>
                                            <?php if($orderDetail->company){?>
                                            <tr>
                                                <td width="30%">Firmanavn:</td>
                                                <td><?php echo $BT_info->company;?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%">CVR-nr.:</td>
                                                <td><?php echo $BT_info->cvr;?></td>
                                            </tr>
                                            <?php }?>
                                            <?php if($orderDetail->ean){?>
                                            <tr>
                                                <td width="30%">EAN-nr.:</td>
                                                <td><?php echo $BT_info->ean;?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Myndighed/Institution:</td>
                                                <td><?php echo $BT_info->authority;?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Ordre- el. rekvisitionsnr.:</td>
                                                <td><?php echo $BT_info->order1;?></td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Personreference:</td>
                                                <td><?php echo $BT_info->person;?></td>
                                            </tr>
                                            <?php }?>
                                            <tr>
                                                <td>Fornavn:</td>
                                                <td><?php echo $BT_info->first_name;?></td>
                                            </tr>
                                            <tr>
                                                <td>Efternavn:</td>
                                                <td><?php echo $BT_info->last_name;?></td>
                                            </tr>
                                            <tr>
                                                <td>Vejnavn:</td>
                                                <td><?php echo $BT_info->street_name;?></td>
                                            </tr>
                                            <tr>
                                                <td>Hus/gade nr.:</td>
                                                <td><?php echo $BT_info->street_number;?></td>
                                            </tr>
                                            <tr>
                                                <td>Postnr.:</td>
                                                <td><?php echo $BT_info->zip;?></td>
                                            </tr>
                                            <tr>
                                                <td>Bynavn:</td>
                                                <td><?php echo $BT_info->city;?></td>
                                            </tr>
                                            <tr>
                                                <td>Telefonnummer:</td>
                                                <td><?php echo $BT_info->phone_1;?></td>
                                            </tr>
                                            <tr>
                                                <td>E-mail:</td>
                                                <td><?php echo $BT_info->email;?></td>
                                            </tr>
                                            <tr>
                                                <td valign="top">Besked:</td>
                                                <td><?php echo $BT_info->message1;?></td>
                                            </tr>
                                            <tr>
                                                <td>Betalingsmetode:</td>
                                                <td>
                                                <?php if($orderDetail->virtuemart_paymentmethod_id == 3){?>
                                                Efterbetalte
                                                <?php } else {?>
                                                Kortbetaling
                                                <?php }?>
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                    </table></td>
                                <td width="50%" valign="top"><table width="100%">
                                        <tbody>
                                            <tr>
                                                <td colspan="4"><strong>
                                                <?php if($isGiftCard){?>
                                                    Modtageren af gavekortet:
                                                <?php } else {?>
                                                    Leveringsadresse:
                                                <?php }?>
                                                </strong></td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Fornavn:</td>
                                                <td><?php echo $ST_info->first_name;?></td>
                                            </tr>
                                            <tr>
                                                <td>Efternavn:</td>
                                                <td><?php echo $ST_info->last_name;?></td>
                                            </tr>
                                            <?php if($isGiftCard){?>
                                            <tr>
                                                <td>E-mail:</td>
                                                <td><?php echo $ST_info->email1;?></td>
                                            </tr>
                                            <tr>
                                                <td>Besked:</td>
                                                <td><?php echo $ST_info->message1;?></td>
                                            </tr>
                                            <?php } else {?>
                                            <tr>
                                                <td>Vejnavn:</td>
                                                <td><?php echo $ST_info->street_name;?></td>
                                            </tr>
                                            <tr>
                                                <td>Hus/gade nr.:</td>
                                                <td><?php echo $ST_info->street_number;?></td>
                                            </tr>
                                            <tr>
                                                <td>Postnr.:</td>
                                                <td><?php echo $ST_info->zip;?></td>
                                            </tr>
                                            <tr>
                                                <td>Bynavn:</td>
                                                <td><?php echo $ST_info->city;?></td>
                                            </tr>
                                            <tr>
                                                <td>Telefonnummer:</td>
                                                <td><?php echo $ST_info->phone_1;?></td>
                                            </tr>
                                            <?php }?>
                                            <?php if($orderDetail->virtuemart_shipmentmethod_id == 1){?>
                                            <tr>
                                                <td valign="top" colspan="2"><strong style="color: red; font-size:18px;">Bemærk! Vi kontakter jer, når varen er klar afhentning</strong></td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                <table width="100%">
                                    <tbody><tr>
                                        <td width="14%" valign="top" align="left">Levering:</td>
                                        <td width="86%">
                                        <?php if($orderDetail->virtuemart_shipmentmethod_id == 1){?>
                                        <span>Afhentning i Rødovre Centrum</span>
                                        <?php } else if($orderDetail->virtuemart_shipmentmethod_id == 2){?>
                                        <span>Levering til døren ved PostDanmark</span>
                                        <?php } else {?>
                                        <span>Leveret via e-mail</span>
                                        <?php }?>
                                        </td>
                                    </tr>
                                </tbody></table>
                                </td>
                            </tr>
                        </tbody>
                    </table></td>
            </tr>
            <tr>
                <td valign="top"></td>
                <td></td>
                <td width="50%" valign="top" colspan="2"></td>
            </tr>
            <tr>
                <td style="border-top: 1px dotted #c6c6c6;" colspan="4"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="padding:0; border: 1px solid #c6c6c6;" class="list_SP">
                        <tbody>
                            <tr>
                                <th align="left">Varebeskrivelse</th>
                                <th>Antal</th>
                                <th>Pris pr stk.</th>
                                <th>Pris i alt</th>
                            </tr>
                            <?php foreach($this->orderDetails["items"] as $item){
                                $item->product_attribute = preg_replace('#"\s*<span.*?<\\\/span>#','"',$item->product_attribute);
                                $attrs = json_decode($item->product_attribute);
                                $attrs = (array)$attrs;
                            ?>
                            <tr>
                                <td><h4><?php echo $item->order_item_name;?></h4>
                                    <p>Varenummer: <?php echo $item->order_item_sku;?></p>
                                      <?php 
                                      $i = 1;
                                      foreach($attrs as $attr){
                                          if($i != 2){
                                      ?>
                                      <p><?php echo $attr;?></p>
                                      <?php }
                                      $i++;
                                      }?>
                                </td>
                                <td align="center"><?php echo $item->product_quantity;?></td>
                                <td align="center"><?php echo number_format($item->product_final_price,2,',','.').' DKK'; ?></td>
                                <td align="center"><?php echo number_format($item->product_final_price*$item->product_quantity,2,',','.').' DKK'; ?></td>
                            </tr>
                            <?php }?>
                            <tr>
                                <td style="padding: 0px;" colspan="4"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="subtotal">
                                        <tbody>
                                            <tr>
                                                <td width="74%" align="right">SUBTOTAL INKL. MOMS: </td>
                                                <td width="26%" align="right"><?php echo number_format($orderDetail->order_salesPrice,2,',','.').' DKK'; ?></td>
                                            </tr>
                                            <tr>
                                                <td align="right">FRAGT:</td>
                                                <td align="right"><?php 
                                                    if($orderDetail->virtuemart_shipmentmethod_id != 1)
                                                        echo number_format($orderDetail->order_shipment,2,',','.').' DKK'; 
                                                    else
                                                        echo 0 . ' DKK';
                                                  ?>
                                                </td>
                                            </tr>
                                            <?php if($orderDetail->coupon_code){?>
                                            <tr>
                                                <td align="right">Gavekort rabat: </td>
                                                <td align="right"><?php echo number_format($orderDetail->coupon_discount,2,',','.').' DKK'; ?></td>
                                            </tr>
                                            <?php }?>
                                            <?php 
                                                if($orderDetail->virtuemart_shipmentmethod_id == 1){
                                            ?>
                                            <tr>
                                                <td align="right">Rabat 10% ved afhentning: </td>
                                                <td align="right"><?php echo '-'.number_format($orderDetail->order_subtotal*0.1,2,',','.').' DKK'; ?></td>
                                            </tr>
                                            <?php }?>
                                            <tr>
                                                <td align="right" style="font-size: 18px;">AT BETALE INKL. MOMS:</td>
                                                <td align="right" style="font-size: 18px;"><?php echo number_format($orderDetail->order_total,2,',','.').' DKK'; ?></td>
                                            </tr>
                                            <?php if($orderDetail->coupon_code){?>
                                            <tr>
                                                <td align="right" colspan="2">(Gavekort restbeløb: <?php echo number_format($coupon_value,2,',','.').' DKK'; ?>)</td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table></td>
                            </tr>
                        </tbody>
                    </table><br />Isenkramshoppen - Rødovre Centrum 132 - 2610 Rødovre - Tlf. 36 41 11 24 - info@isenkramshoppen.dk</td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
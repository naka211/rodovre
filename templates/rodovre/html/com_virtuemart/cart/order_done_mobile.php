<?php
defined('_JEXEC') or die('');

/**
*
* Template for the shopping cart
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
*/


/*if ($this->display_title) {
	echo "<h3>".JText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU')."</h3>";
}
	echo $this->html;*/

$admin = JFactory::getUser('62');
$cart = $this->cart;
//print_r($cart);
/*if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
$config =& JFactory::getConfig();
$fromName = $config->getValue( 'config.sitename' );
$fromMail = $config->getValue( 'config.mailfrom' );
$vars['user'] = array('name' => $fromName, 'email' => $fromMail);
$vars['vendor'] = array('vendor_store_name' => $fromName );*/

$db = JFactory::getDBO();
$orderid = JRequest::getVar('virtuemart_order_id');

$query = "SELECT virtuemart_order_id, order_shipment, order_total, order_salesPrice, order_number FROM #__virtuemart_orders WHERE virtuemart_order_id = ".$orderid;
$db->setQuery($query);
$order_info = $db->loadObject();

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');
$orderModel=VmModel::getModel('orders');
$order = $orderModel->getOrder($orderid);
//print_r($order);exit;
/*$vars['orderDetails']=$order;
shopFunctionsF::renderMail('invoice', $admin->email, $vars);
shopFunctionsF::renderMail('invoice', $order->email, $vars);*/

if (!class_exists('VirtueMartModelOrders')) require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
$modelOrder = VmModel::getModel('orders');	
$order1 = array();		
$order1['order_status'] = "C";
$order1['customer_notified'] =1;
$modelOrder->updateStatusForOneOrder($orderid, $order1, true);

$query = "SELECT * FROM #__virtuemart_order_userinfos WHERE address_type = 'BT' AND virtuemart_order_id = ".$order_info->virtuemart_order_id;
$db->setQuery($query);
$BT_info = $db->loadObject();

$query = "SELECT * FROM #__virtuemart_order_userinfos WHERE address_type = 'ST' AND virtuemart_order_id = ".$order_info->virtuemart_order_id;
$db->setQuery($query);
$ST_info = $db->loadObject();

if(!$ST_info){
    $ST_info = $BT_info;
}

if($BT_info->ean){
	$type = "Offentlig instans";
} else if($BT_info->company){
	$type = "Erhverv";
} else {
	$type = "Privat";
}

if($order['items'][0]->virtuemart_category_id == 14){
    $isGiftCard = true;
} else {
    $isGiftCard = false;
}

//T.Trung
if($order['details']['BT']->coupon_code){
    $db= JFactory::getDBO();
    $query = "SELECT id, coupon_value FROM #__awocoupon WHERE coupon_code = '".$order['details']['BT']->coupon_code."'";
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

$cart = VirtueMartCart::getCart();
$cart->emptyCart();
?>

<div id="content" class="w-content undepages thankyou">
	<div class="eachBox boxThankyou">
		<h2 class="titleRight">Tak for din ordre </h2>
		<h5>Ordrenummer: <?php echo $order['details']['BT']->order_number;?> </h5>
		<p class="infohead">En ordrebekræftelse vil blive sendt til <a href="mailto:<?php echo $order['details']['BT']->email;?>"><?php echo $order['details']['BT']->email;?></a><br>
			Har du spørgsmål, kan du kontakte os på +45 4162 8001</p>
		<div class="order-list clearfix">
			<div class="clearfix">
				<div class="cusBox cus-info">
					<h5>Kundeoplysninger:</h5>
					<div class="eachRow">
						<label>Kundetype:</label>
						<span><?php echo $type;?></span> </div>
					<?php if($BT_info->company){?>
					<div class="eachRow">
						<label>Firmanavn:</label>
						<span><?php echo $BT_info->company;?></span>
					</div>
					<div class="eachRow">
						<label>CVR-nr.:</label>
						<span><?php echo $BT_info->cvr;?></span>
					</div>
					<?php } else if($BT_info->ean){?>
					<div class="eachRow">
						<label>EAN-nr.:</label>
						<span><?php echo $BT_info->ean;?></span>
					</div>
					<div class="eachRow">
						<label>Myndighed/Institution:</label>
						<span><?php echo $BT_info->authority;?></span>
					</div>
					<div class="eachRow">
						<label>Ordre- el. rekvisitionsnr.:</label>
						<span><?php echo $BT_info->order1;?></span>
					</div>
					<div class="eachRow">
						<label>Personreference:</label>
						<span><?php echo $BT_info->person;?></span>
					</div>
					<?php }?>
					<div class="eachRow">
						<label>Fornavn:</label>
						<span><?php echo $BT_info->first_name;?></span> </div>
					<div class="eachRow">
						<label>Efternavn:</label>
						<span><?php echo $BT_info->last_name;?></span> </div>
					<div class="eachRow">
						<label>Vejnavn:</label>
						<span><?php echo $BT_info->street_name;?></span> </div>
					<div class="eachRow">
						<label>Hus/gade nr.:</label>
						<span><?php echo $BT_info->street_number;?></span> </div>
					<div class="eachRow">
						<label>Postnr.:</label>
						<span><?php echo $BT_info->zip;?></span> </div>
					<div class="eachRow">
						<label>Bynavn:</label>
						<span><?php echo $BT_info->city;?></span> </div>
					<div class="eachRow">
						<label>Telefon:</label>
						<span><?php echo $BT_info->phone_1;?></span> </div>
					<div class="eachRow">
						<label>E-mail adresse:</label>
						<span><?php echo $BT_info->email;?></span> </div>
					<div class="eachRow">
						<label>Besked:</label>
						<span><?php echo $BT_info->message1;?></span> </div>
				</div>
				<!--.cus-info-->
				<div class="cusBox delivery-address">
					<h5><?php if($isGiftCard){?>
						Modtageren af gavekortet:
					<?php } else {?>
						Leveringsadresse:
					<?php }?>
					</h5>
					<div class="eachRow">
						<label>Fornavn:</label>
						<span><?php echo $ST_info->first_name;?></span> </div>
					<div class="eachRow">
						<label>Efternavn:</label>
						<span><?php echo $ST_info->last_name;?></span> </div>
					<?php if($isGiftCard){?>
					<div class="eachRow">
						<label>E-mail:</label>
						<span><?php echo $ST_info->email1;?></span> </div>
					<div class="eachRow">
						<label>Besked:</label>
						<span><?php echo $ST_info->message1;?></span> </div>
					<?php } else {?>
					<div class="eachRow">
						<label>Vejnavn:</label>
						<span><?php echo $ST_info->street_name;?></span> </div>
					<div class="eachRow">
						<label>Hus/gade nr.:</label>
						<span><?php echo $ST_info->street_number;?></span><br>
					</div>
					<div class="eachRow">
						<label>Postnr:</label>
						<span><?php echo $ST_info->zip;?></span> </div>
					<div class="eachRow">
						<label>Bynavn:</label>
						<span><?php echo $ST_info->city;?></span> </div>
					<div class="eachRow">
						<label>Telefon:</label>
						<span><?php echo $ST_info->phone_1;?></span> </div>
					<?php }?>
				</div>
				<!--.delivery-address--> 
			</div>
			<div class="oneRow clearfix">
				<div class="cusBox cus-info">
					<h5>Betalingsmetode:</h5>
					<div class="eachRow">
						<label>
						<?php if($order['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
						Efterbetalte
						<?php } else {?>
						Kortbetaling
						<?php }?>
						</label>
					</div>
				</div>
				<!--.cus-info-->
				<div class="cusBox delivery-address">
					<h5>Levering:</h5>
					<div class="eachRow"><span>
					<?php if($order['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
					<span>Afhentning på Hesselrødvej 26, 2980 Kokkedal</span>
					<?php } else if($order['details']['BT']->virtuemart_shipmentmethod_id == 2){?>
					<span>Leveret på Sjælland</span>
					<?php } else if($order['details']['BT']->virtuemart_shipmentmethod_id == 3){?>
					<span>Leveret til døren for Fyn og Jylland</span>
					<?php } else {?>
					<span>Leveret via e-mail</span>
					<?php }?>
					</span></div>
				</div>
				<!--.delivery-address--> 
			</div>
			<!--oneRow--> 
		</div>
		<!--.order-list-->
		<?php if($order['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
		<p class="red">Bemærk! Vi kontakter jer, når varen er klar afhentning</p>
		<?php }?>
		<div class="wrapTb step4 clearfix">
			<div class="topbarcart clearfix">Varebeskrivelse</div>
			<div class="wrapRowPro">
				<?php foreach($order['items'] as $item){
					$attrs = json_decode($item->product_attribute);
					//preg_match_all("#\">[\w\W]*?</span>#", $item->product_attribute, $tmp);
					$attrs = (array)$attrs;
				?>
				<div class="eachRowPro">
					<div class="proImg"><img src="img/img04.jpg" alt=""></div>
					<div class="row rowAbove">
						<div class="proName">
							<h2><?php echo $item->order_item_name;?></h2>
							<p> <span class="spanlb">Varenummer:</span><span class="spanvl"><?php echo $item->order_item_sku;?></span></p>
							<div class="proSize"><span class="spanvl">
							<?php 
							  $i = 1;
							  foreach($attrs as $attr){
								  if($i != 2){
							  ?>
							  <?php echo $attr;?>
							  <?php }
							  $i++;
							  }?>
			  				</span></div>
						</div>
						<div class="wrapedit"><span>Antal</span> <span><?php echo $item->product_quantity;?></span> </div>
					</div>
					<div class="row rowBelow">
						<div class="proPriceTT"><span class="spanlb">Pris i alt:</span><span class="spanvl"><?php echo number_format($item->product_final_price*$item->product_quantity,2,',','.').' DKK'; ?></span></div>
					</div>
				</div>
				<!--eachRowPro-->
				<?php }?>
			</div>
			<!--wrapRowPro-->
			<div class="wrapTotalPrice clearfix">
				<div class="box boxright">
					<div class="eachRow r-nor clearfix"> <span class="lbNor">SUBTOTAL INKL. MOMS: </span> <span class="lbPrice"><?php echo number_format($order['details']['BT']->order_salesPrice,2,',','.').' DKK'; ?></span> </div>
					<div class="eachRow r-nor clearfix"> <span class="lbNor">FRAGT: </span> <span class="lbPrice"><?php                     if($order['details']['BT']->virtuemart_shipmentmethod_id != 1) echo number_format($order['details']['BT']->order_shipment,2,',','.').' DKK'; else echo 0 . ' DKK';?></span> </div>
					<?php if($order['details']['BT']->coupon_code){?>
					<div class="eachRow r-nor clearfix"> <span class="lbNor">GAVEKORT KUPON: </span> <span class="lbPrice"><?php echo number_format($order['details']['BT']->coupon_discount,2,',','.').' DKK'; ?></span> </div>
					<?php }?>
					<?php if($order['details']['BT']->virtuemart_shipmentmethod_id == 1){?>
					<div class="eachRow r-nor clearfix"> <span class="lbNor">RABAT 10% VED AFHENTNING: </span> <span class="lbPrice"><?php echo '-'.number_format($order['details']['BT']->order_subtotal*0.1,2,',','.').' DKK'; ?></span> </div>
					<?php }?>
					<div class="eachRow r-total clearfix"> <span class="lbTotal">AT BETALE INKL. MOMS:</span> <span class="totalPrice"><?php echo number_format($order['details']['BT']->order_total,2,',','.').' DKK'; ?></span> </div>
					<?php if($order['details']['BT']->coupon_code){?>
					<div class="eachRow r-nor clearfix"> (Gavekort restbeløb: <?php echo number_format($coupon_value,2,',','.').' DKK'; ?>) </div>
					<?php }?>
				</div>
			</div>
			<!--wrapTotalPrice--> 
		</div>
		<!-- wrapTb -->
		
		<h5>Sådan returnerer du en vare</h5>
		<p>Vi ønsker at du er tilfreds hver gang du handler hos os, derfor er vi også opmærksomme på at du lejlighedsvis ønsker at returnere en vare. Klik her for at læse mere om vores returpolitik.</p>
		<h5>Har du brug for hjælp?</h5>
		<p>Se vores almindelige spørgsmål. Her finder du svar på spørgsmål om vores onlineshop.<br>
			Tak for din bestilling. </p>
		<p> <b>Helle Scheel-Larsen</b><br>
			Hesselrødvej 26, Karlebo<br>
			2980 Kokkedal<br>
			Mobil: 41628001<br>
			E-mail: <a class="link" href="mailto:info@scheel-larsen.dk">info@scheel-larsen.dk</a></p>
		<div class="wrap-button"> <a class="btn2 btnGray btnHome" href="index.php">Til forside</a> <a class="btn2 btnPrint" href="index.php?option=com_virtuemart&view=invoice&layout=invoice&tmpl=component&virtuemart_order_id=<?php echo $order['details']['BT']->virtuemart_order_id;?>&order_number=<?php echo $order['details']['BT']->order_number;?>&order_pass=<?php echo $order['details']['BT']->order_pass;?>">PRINT KVITTERING</a> </div>
		<!--wrap-button --> 
		
	</div>
	<!--eachBox boxThankyou--> 
</div>
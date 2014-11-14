<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$user = JFactory::getUser();

if (count($this->orderlist) == 0) {
	//echo JText::_('COM_VIRTUEMART_ACC_NO_ORDER');
	 echo shopFunctionsF::getLoginForm(false,true);
} else {
	$orderModel = VmModel::getModel('orders');
 ?>
<script type="text/javascript">
jQuery(document).ready(function(){
	
//Set default open/close settings
jQuery('.w-over-title-gray').hide(); //Hide/close all containers
jQuery('.over-title-gray:first').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container

//On Click
jQuery('.over-title-gray').click(function(){
	if( jQuery(this).next().is(':hidden') ) { //If immediate next container is closed...
		jQuery('.over-title-gray').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
		jQuery(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
	}
	return false; //Prevent the browser jump to the link anchor
});

});
</script>
<div id="overview-page">
	<div class="overview-left">
		<div class="bnt-overview"><a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.$user->id); ?>"></a></div><!--.bnt-overview-->
		<div class="bnt-my-profile active3"><a href="#"></a></div><!--.bnt-my-profile-->
	</div>
	<div class="overview-right">
<?php
		$k = 0;
		foreach ($this->orderlist as $row) {
			//$editlink = JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $row->order_number);
			$orderDetails = $orderModel->getOrder($row->virtuemart_order_id);
?>
		<div class="over-title-gray">
		<label>Ordrenummer: <?php echo $row->order_number?></label><span>Ordredato: <?php echo vmJsApi::date($row->created_on,'LC4',true);?></span>
		</div>
		<div class="w-over-title-gray">
			<div class="over-info">
				<label>Kundetype:</label>
				<span><?php
				$tmp="";
				switch($orderDetails["details"]["BT"]->address_type_name){
					case 1: echo "Privat";break;
					case 2: $tmp="<label>Firmanavn:</label><span>".$orderDetails["details"]["BT"]->company."</span><br>
					<label>CVR-nr.:</label><span>".$orderDetails["details"]["BT"]->cvr."</span><br>";
					echo "Erhverv";break;
					case 3: $tmp="<label>EAN-nr.:</label><span>".$orderDetails["details"]["BT"]->ean."</span><br>
					<label>Myndighed/Institution:</label><span>".$orderDetails["details"]["BT"]->authority."</span><br>
					<label>Rekvisitionsnr.:</label><span>".$orderDetails["details"]["BT"]->order1."</span><br>
					<label>Personreference:</label><span>".$orderDetails["details"]["BT"]->person."</span><br>";
					echo "Offentlig instans";break;
				}
				?></span>
<br>			<label>E-mail: </label>
				<span><a href="mailto:<?php echo $orderDetails["details"]["BT"]->email?>"><?php echo $orderDetails["details"]["BT"]->email?></a></span>
			</div>
			<div class="w-over-top">
				<div class="over-cus-info">
					<h4>Kundeoplysninger:</h4>
					<?php echo $tmp?>
					<label>Fornavn:</label><span><?php echo $orderDetails["details"]["BT"]->first_name?></span><br>
					<label>Efternavn:</label><span><?php echo $orderDetails["details"]["BT"]->last_name?></span><br>
					<label>Adresse:</label><span><?php echo $orderDetails["details"]["BT"]->address_1?></span><br>
					<label>Postnr.:</label><span><?php echo $orderDetails["details"]["BT"]->zip?></span><br>
					<label>By:</label><span><?php echo $orderDetails["details"]["BT"]->city?></span><br>
					<label>Telefon:</label><span><?php echo $orderDetails["details"]["BT"]->phone_1?></span><br><br>
					<h4>Betalingsmetode: </h4>
					<label>Kortbetaling</label>
				</div>
				<div class="over-delivery-address">
					<h4>Leveringsadresse:</h4>
					<label>Fornavn:</label><span><?php echo $orderDetails["details"]["ST"]->first_name?></span><br>
					<label>Efternavn:</label><span><?php echo $orderDetails["details"]["ST"]->last_name?></span><br>
					<label>Adresse:</label><span><?php echo $orderDetails["details"]["ST"]->address_1?></span><br>
					<label>Postnr.:</label><span><?php echo $orderDetails["details"]["ST"]->zip?></span><br>
					<label>By:</label><span><?php echo $orderDetails["details"]["ST"]->city?></span><br>
					<label>Telefon:</label><span><?php echo $orderDetails["details"]["ST"]->phone_1?></span><br><br>
					<h4>Levering: </h4>
					<span><?php
					$tmp=$orderDetails["details"]["BT"]->address_2;
					echo ($tmp) ? "Afhentning: ".$tmp : "Forsendelse"?></span>
				</div>
			</div>
			<div class="table-pro">
				<div class="table-pro-title">
				<div class="col-1-">
				<p>Produkt</p>
				</div><!--.col-1-->
				<div class="col-2-">
				<p>Vare-nr</p>
				</div><!--.col-2-->
				<div class="col-3-">
				<p>Antal</p>
				</div><!--.col-3-->
                <div class="col-4-">
				<p>Pris pr. enhed</p>
				</div>
				<div class="col-4-" style="text-align:right">
				<p>Pris i alt</p>
				</div><!--.col-4-->
				</div><!--.table-pro-title-->
<?php
// Display orders
			foreach($orderDetails['items'] as $item) {
				//print_r($item);exit;
		//$qtt = $item->product_quantity ;
		//$_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $item->virtuemart_category_id . '&virtuemart_product_id=' . $item->virtuemart_product_id);
?>
	
				<div class="table-pro-content">
					<div class="col-1-content-">
					<p><?php echo $item->order_item_name?></p>
					</div><!--.col-1-content-->
					<div class="col-2-content-">
					<p><?php echo $item->order_item_sku?></p>
					</div><!--.col-2-content-->
					<div class="col-3-content-">
					<p><?php echo $item->product_quantity?></p>
					</div><!--.col-3-content-->
                    <div class="col-4-content-">
					<p><?php echo $this->currency->priceDisplay($item->product_final_price,$this->currency)?></p>
					</div><!--.col-4-content-->
					<div class="col-4-content-" style="text-align:right">
					<p><?php echo $this->currency->priceDisplay($item->product_subtotal_with_tax,$this->currency)?></p>
					</div><!--.col-4-content-->
				</div><!--.table-pro-content-->
         	<?php
			/// Display orders
				}
			?>
			<div class="table-pro-content b-t-2">
				<div class="sum-total">
					<div>
					<label>Forsendelse:</label><span><?php echo number_format($orderDetails['details']['BT']->order_shipment,2,',','.');?> DKK</span>
					</div>
					<div>
					<label>Subtotal inkl. moms:</label><span><?php echo number_format($orderDetails['details']['BT']->order_salesPrice,2,',','.');?> DKK</span>
					</div>
					<div>
					<label>Heraf moms:</label><span><?php echo number_format($orderDetails['details']['BT']->order_salesPrice*0.2,2,',','.');?> DKK</span>
					</div>
					<div class="black">
					<label>TOTAL INKL. MOMS:</label><span><?php echo number_format($orderDetails['details']['BT']->order_total,2,',','.');?> DKK</span>
					</div>
				</div><!--.sum-total-->
			</div><!--.table-pro-content-->

			</div>
            <div class="w-print-down">            
                <div class="bnt-download">
                	<?php 
					$invoiceNumberDate=array();
					$orderModel->createInvoiceNumber($orderDetails['details']['BT'], $invoiceNumberDate);?>
                   <a href="_/invoices/vminvoice_<?php echo $invoiceNumberDate[0];?>.pdf">Download PDF-file</a>
                </div><!--.bnt-download-->
                <div class="bnt-over-print">
                    <a href="index.php?option=com_virtuemart&view=pluginresponse&layout=printOrder&orderid=<?php echo $orderDetails['details']['BT']->order_number;?>&tmpl=component" target="_blank">Print</a>
                </div><!--.bnt-over-print-->
            </div>
		</div>
<?php
			$k = 1 - $k;
		}
?>
	</div>
</div>
<?php } ?>
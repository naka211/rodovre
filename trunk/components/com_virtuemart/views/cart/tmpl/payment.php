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

$orderid = JRequest::getVar('virtuemart_order_id');

if(!class_exists('VmModel'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmmodel.php');
$orderModel=VmModel::getModel('orders');
$order = $orderModel->getOrder($orderid);
print_r($order);exit;

$siteURL = JURI::base();

//LDC EPAY for form submit data
$accepturl = $siteURL . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=1';
$callbackurl = $siteURL . 'index.php?callback=1&option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&pm=1';
$declineurl = $siteURL . 'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&on='.$cart->order_number . '&pm=1&HTTP_COOKIE='.getenv("HTTP_COOKIE");

$order_number = $order['details']['BT']->order_number;
$order_amount = $order['details']['BT']->order_total * 100;
?>

<div class="template2">
    <div class="checkout_page clearfix">
    Din ordre overfører til Quickpay.....
    </div>
    <form action="https://secure.quickpay.dk/form/" method="post" id="QuickpayForm">
    <input type="hidden" name="protocol" value="7" />
    <input type="hidden" name="msgtype" value="authorize" />
    <input type="hidden" name="merchant" value="21218146" />
    <input type="hidden" name="language" value="da" />
    <input type="hidden" name="ordernumber" value="<?php echo $order_number;?>" />
    <input type="hidden" name="amount" value="<?php echo $order_amount;?>" />
    <input type="hidden" name="currency" value="DKK" />
    <input type="hidden" name="continueurl" value="<?php echo $accepturl;?>" />
    <input type="hidden" name="cancelurl" value="<?php echo $declineurl;?>" />
    <input type="hidden" name="callbackurl" value="<?php echo $callbackurl;?>" />
    <input type="hidden" name="autocapture" value="0" />
    <input type="hidden" name="cardtypelock" value="" />
    <input type="hidden" name="splitpayment" value="1" />
    <input type="hidden" name="md5check" value="24a9b705ee7d64f5bbd26033ab3bb25e1990aab9a3c4137a9e8d9efc6fbd9526" />
    </form>
</div>
<script language="javascript">
//jQuery("#QuickpayForm").submit();
</script>

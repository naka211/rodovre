<?php
/**
 *
 * Layout for the edit coupon
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Oscar van Eijk
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 2458 2010-06-30 18:23:28Z milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>


 <div class="graris">
	<p>Har du en værdikode?</p>
	<div class="frm_coupon clearfix">
		<form method="post" id="userForm" name="enterCouponCode" action="<?php echo JRoute::_('index.php'); ?>">
			<input type="text" name="coupon_code" maxlength="50" class="coupon" value="<?php echo $this->coupon_text; ?>" />
			<a href="#" onclick="document.getElementById('Send').click();">Aktiver</a>
			<input type="submit" value="Send" id="Send" name="Send" style="display:none">
			<input type="hidden" name="option" value="com_virtuemart" />
		    <input type="hidden" name="view" value="cart" />
		    <input type="hidden" name="task" value="setcoupon" />
		    <input type="hidden" name="controller" value="cart" />
		</form>
	</div>                
</div>         
        
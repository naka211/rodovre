<?php

/**
 *
 * Controller for the cart
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author RolandD
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 6502 2012-10-04 13:19:26Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * Controller for the cart view
 *
 * @package VirtueMart
 * @subpackage Cart
 * @author RolandD
 * @author Max Milbers
 */
class VirtueMartControllerCart extends JController {

	/**
	 * Construct the cart
	 *
	 * @access public
	 * @author Max Milbers
	 */
	public function __construct() {
		parent::__construct();
		if (VmConfig::get('use_as_catalog', 0)) {
			$app = JFactory::getApplication();
			$app->redirect('index.php');
		} else {
			if (!class_exists('VirtueMartCart'))
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
			if (!class_exists('calculationHelper'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
		}
		$this->useSSL = VmConfig::get('useSSL', 0);
		$this->useXHTML = false;
	}

	/**
	 * Override of display
	 *
	 * @return  JController  A JController object to support chaining.
	 *
	 * @since   11.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = JRequest::getCmd('view', $this->default_view);
		$viewLayout = JRequest::getCmd('layout', 'default');

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

		$view->assignRef('document', $document);

		$view->display();

		return $this;
	}

	/**
	 * Add the product to the cart
	 *
	 * @author RolandD
	 * @author Max Milbers
	 * @access public
	 */
	public function add() {
		$mainframe = JFactory::getApplication();
		if (VmConfig::get('use_as_catalog', 0)) {
			$msg = JText::_('COM_VIRTUEMART_PRODUCT_NOT_ADDED_SUCCESSFULLY');
			$type = 'error';
			$mainframe->redirect('index.php', $msg, $type);
		}
		$cart = VirtueMartCart::getCart();
		if ($cart) {
			$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array');
			$success = true;
			if ($cart->add($virtuemart_product_ids,$success)) {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_ADDED_SUCCESSFULLY');
				$type = '';
			} else {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_NOT_ADDED_SUCCESSFULLY');
				$type = 'error';
			}

			$mainframe->enqueueMessage($msg, $type);
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));

		} else {
			$mainframe->enqueueMessage('Cart does not exist?', 'error');
		}
	}
	
	function add_ipaper(){
        $db = JFactory::getDBO();
        $xml = simplexml_load_string(preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $_POST['basket']),'SimpleXMLElement', LIBXML_NOCDATA);
        $virtuemart_product_ids = array();
        $quantities = array();
        $pro_str = '';
        foreach($xml->item as $item){
            $query = "SELECT virtuemart_product_id, product_in_stock FROM #__virtuemart_products WHERE product_sku = '".$item->productid."'";
            $db->setQuery($query);
            $product = $db->loadObject(); 
            if(($product->virtuemart_product_id) && ($product->product_in_stock > $item->amount)){
                array_push($virtuemart_product_ids, $product->virtuemart_product_id);
                array_push($quantities, $item->amount);
            } else {
                $pro_str .= $product->virtuemart_product_id.',';
            }
        }
        $pro_str = rtrim($pro_str, ",");
        if($pro_str){
            $add_false = '?add_fail='.$pro_str;
        } else {
            $add_false = '';
        }
        if($virtuemart_product_ids){
            $cart = VirtueMartCart::getCart();
            $success = true;
            $cart->add($virtuemart_product_ids, $success, $quantities);
            $mainframe = JFactory::getApplication();
            $mainframe->redirect('index.php/user/editaddresscheckoutBT.html'.$add_false);
        } else {
            $mainframe = JFactory::getApplication();
            $mainframe->redirect('index.php'.$add_false);
        }
    }

	/**
	 * Add the product to the cart, with JS
	 *
	 * @author Max Milbers
	 * @access public
	 */
	public function addJS() {

		$this->json = new stdClass();
		$cart = VirtueMartCart::getCart(false);
		if ($cart) {
            
			$virtuemart_product_ids = JRequest::getVar('virtuemart_product_id', array(), 'default', 'array');
            //T.Trung
            $this->checkGiftCard($virtuemart_product_ids, $cart);
            //T.Trung end
			$view = $this->getView ('cart', 'json');
			$errorMsg = 0;//JText::_('COM_VIRTUEMART_CART_PRODUCT_ADDED');
			$products = $cart->add($virtuemart_product_ids, $errorMsg );
			if ($products) {
				if(is_array($products) and isset($products[0])){
					$view->assignRef('product',$products[0]);
				}
				$view->setLayout('padded');
				$this->json->stat = '1';
			} else {
				$view->setLayout('perror');
				$this->json->stat = '2';
				$tmp = false;
				$view->assignRef('product',$tmp);
			}
			$view->assignRef('products',$products);
			$view->assignRef('errorMsg',$errorMsg);
			ob_start();
			$view->display ();
			$this->json->msg = ob_get_clean();
		} else {
			$this->json->msg = '<a href="' . JRoute::_('index.php?option=com_virtuemart', FALSE) . '" >' . JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
			$this->json->msg .= '<p>' . JText::_('COM_VIRTUEMART_MINICART_ERROR') . '</p>';
			$this->json->stat = '0';
		}
		echo json_encode($this->json);
		jExit();
	}

	/**
	 * Add the product to the cart, with JS
	 *
	 * @author Max Milbers
	 * @access public
	 */
	public function viewJS() {

		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart(false);
		$this->data = $cart->prepareAjaxData();

		$extension = 'com_virtuemart';
		VmConfig::loadJLang($extension); //  when AJAX it needs to be loaded manually here >> in case you are outside virtuemart !
 
		if ($this->data->totalProduct > 1)
		$this->data->totalProductTxt = JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', $this->data->totalProduct);
		else if ($this->data->totalProduct == 1)
		$this->data->totalProductTxt = JText::_('COM_VIRTUEMART_CART_ONE_PRODUCT');
		else
		$this->data->totalProductTxt = JText::_('COM_VIRTUEMART_EMPTY_CART');
		if ($this->data->dataValidated == true) {
			$taskRoute = '&task=confirm';
			$linkName = JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU');
		} else {
			$taskRoute = '';
			$linkName = JText::_('COM_VIRTUEMART_CART_SHOW');
		}
		$this->data->cart_show = '<a class="floatright" href="' . JRoute::_("index.php?option=com_virtuemart&view=cart" . $taskRoute, $this->useXHTML, $this->useSSL) . '" rel="nofollow">' . $linkName . '</a>';
		$this->data->billTotal = vmText::_('COM_VIRTUEMART_CART_TOTAL') . ' : <strong>' . $this->data->billTotal . '</strong>';
		echo json_encode($this->data);
		Jexit();
	}

	/**
	 * For selecting couponcode to use, opens a new layout
	 *
	 * @author Max Milbers
	 */
	public function edit_coupon() {

		$view = $this->getView('cart', 'html');
		$view->setLayout('edit_coupon');

		// Display it all
		$view->display();
	}

	/**
	 * Store the coupon code in the cart
	 * @author Max Milbers
	 */
	public function setcoupon() {

		/* Get the coupon_code of the cart */
		$coupon_code = JRequest::getVar('coupon_code', ''); //TODO VAR OR INT OR WORD?
		if ($coupon_code) {

			$cart = VirtueMartCart::getCart();
			if ($cart) {
				$app = JFactory::getApplication();
				$msg = $cart->setCouponCode($coupon_code);

				//$cart->setDataValidation(); //Not needed already done in the getCart function
				if ($cart->getInCheckOut()) {
					$app = JFactory::getApplication();
					$app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', FALSE),$msg);
				} else {
					$app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE),$msg);
				}
			}
		}
		$this->display();

	}

	/**
	 * For selecting shipment, opens a new layout
	 *
	 * @author Max Milbers
	 */
	public function edit_shipment() {


		$view = $this->getView('cart', 'html');
		$view->setLayout('select_shipment');

		// Display it all
		$view->display();
	}

	/**
	 * Sets a selected shipment to the cart
	 *
	 * @author Max Milbers
	 */
	public function setshipment() {

		/* Get the shipment ID from the cart */

		$cart = VirtueMartCart::getCart();
		if ($cart) {
			$virtuemart_shipmentmethod_id = JRequest::getInt('virtuemart_shipmentmethod_id', $cart->virtuemart_shipmentmethod_id);
			//Now set the shipment ID into the cart
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$cart->setShipment($virtuemart_shipmentmethod_id);
			//Add a hook here for other payment methods, checking the data of the choosed plugin
			$_dispatcher = JDispatcher::getInstance();
			$_retValues = $_dispatcher->trigger('plgVmOnSelectCheckShipment', array(   &$cart));
			$dataValid = true;
			foreach ($_retValues as $_retVal) {
				if ($_retVal === true ) {
					// Plugin completed successfull; nothing else to do
					$cart->setCartIntoSession();
					break;
				} else if ($_retVal === false ) {
					$mainframe = JFactory::getApplication();
					$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=edit_shipment',$this->useXHTML,$this->useSSL), $_retVal);
					break;
				}
			}

			if ($cart->getInCheckOut() && !VmConfig::get('oncheckout_opc', 1)) {

				$mainframe = JFactory::getApplication();
				$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', FALSE) );
			}
		}
		// 	self::Cart();
		$this->display();
	}

	/**
	 * To select a payment method
	 *
	 * @author Max Milbers
	 */
	public function editpayment() {

		$view = $this->getView('cart', 'html');
		$view->setLayout('select_payment');

		// Display it all
		$view->display();
	}

	/**
	 * To set a payment method
	 *
	 * @author Max Milbers
	 * @author Oscar van Eijk
	 * @author Valerie Isaksen
	 */
	function setpayment() {

		// Get the payment id of the cart
		//Now set the payment rate into the cart
		$cart = VirtueMartCart::getCart();
		if ($cart) {
			if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
			JPluginHelper::importPlugin('vmpayment');
			//Some Paymentmethods needs extra Information like
			$virtuemart_paymentmethod_id = JRequest::getInt('virtuemart_paymentmethod_id', $cart->virtuemart_paymentmethod_id);
			$cart->setPaymentMethod($virtuemart_paymentmethod_id);

			//Add a hook here for other payment methods, checking the data of the choosed plugin
			$msg = '';
			$_dispatcher = JDispatcher::getInstance();
			$_retValues = $_dispatcher->trigger('plgVmOnSelectCheckPayment', array( $cart, &$msg));
			$dataValid = true;
			foreach ($_retValues as $_retVal) {
				if ($_retVal === true ) {
					// Plugin completed succesfull; nothing else to do
					$cart->setCartIntoSession();
					break;
				} else if ($_retVal === false ) {
		   		$app = JFactory::getApplication();
		   		$app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=editpayment',$this->useXHTML,$this->useSSL), $msg);
		   		break;
				}
			}
			//			$cart->setDataValidation();	//Not needed already done in the getCart function
// 			vmdebug('setpayment $cart',$cart);
			if ($cart->getInCheckOut() && !VmConfig::get('oncheckout_opc', 1)) {
				$app = JFactory::getApplication();
				$app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout', FALSE), $msg);
			}
		}

		$this->display();
	}

	/**
	 * Delete a product from the cart
	 *
	 * @author RolandD
	 * @access public
	 */
	public function delete() {
		$mainframe = JFactory::getApplication();
		/* Load the cart helper */
		$cart = VirtueMartCart::getCart();
		if ($cart->removeProductCart())
		$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_REMOVED_SUCCESSFULLY'));
		else
		$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_NOT_REMOVED_SUCCESSFULLY'), 'error');

		$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));
	}
    
    public function deleteAjax() {
		$mainframe = JFactory::getApplication();
		/* Load the cart helper */
		$cart = VirtueMartCart::getCart();
		if ($cart->removeProductCart()){
		    echo 1;exit;
        } else {
		    echo 0;exit;
        }
	}

	/**
	 * Delete a product from the cart
	 *
	 * @author RolandD
	 * @access public
	 */
	public function update() {
		$mainframe = JFactory::getApplication();
		/* Load the cart helper */
		$cartModel = VirtueMartCart::getCart();
		if ($cartModel->updateProductCart())
		$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_UPDATED_SUCCESSFULLY'));
		else
		$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_PRODUCT_NOT_UPDATED_SUCCESSFULLY'), 'error');

		$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));
	}

	/**
	 * Change the shopper
	 *
	 * @author Maik K�nnemann
	 *
	 */
	public function changeShopper() {

		JRequest::checkToken () or jexit ('Invalid Token');

		//get data of current and new user
		$usermodel = VmModel::getModel('user');
		$user = $usermodel->getCurrentUser();

		//check for permissions
		if(!JFactory::getUser(JFactory::getSession()->get('vmAdminID'))->authorise('core.admin', 'com_virtuemart') || !VmConfig::get ('oncheckout_change_shopper')){
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::sprintf('COM_VIRTUEMART_CART_CHANGE_SHOPPER_NO_PERMISSIONS', $user->name .' ('.$user->username.')'), 'error');
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'));
		}

		$newUser = JFactory::getUser(JRequest::getCmd('userID'));

		//update session
		$session = JFactory::getSession();
		$adminID = $session->get('vmAdminID');
		if(!isset($adminID)) $session->set('vmAdminID', $user->virtuemart_user_id);
		$session->set('user', $newUser);

		//update cart data
		$cart = VirtueMartCart::getCart();
		$data = $usermodel->getUserAddressList(JRequest::getCmd('userID'), 'BT');
		foreach($data[0] as $k => $v) {
			$data[$k] = $v;
		}
		$cart->BT['email'] = $newUser->email;
		unset($cart->ST);
		$cart->saveAddressInCart($data, 'BT');

		$mainframe = JFactory::getApplication();
		$mainframe->enqueueMessage(JText::sprintf('COM_VIRTUEMART_CART_CHANGED_SHOPPER_SUCCESSFULLY', $newUser->name .' ('.$newUser->username.')'), 'info');
		$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'));
	}

	/**
	 * Checks for the data that is needed to process the order
	 *
	 * @author Max Milbers
	 *
	 */
	public function checkout() {


		$cart = VirtueMartCart::getCart();
		$cart->getFilterCustomerComment();
		$cart->tosAccepted = JRequest::getInt('tosAccepted', $cart->tosAccepted);
		$task = JRequest::getString('task');

		$update = vRequest::getString('update',false);

		if(($update and is_array($update)) or $task=='update'){
			reset($update);
			$key = key($update);
			$quantity = vRequest::getInt('quantity');
			$cart->updateProductCart(key($update),$quantity[$key]);
			$this->display();
		} else if(isset($_POST['setcoupon']) or $task=='setcoupon'){
			$this->setcoupon();
		} else if(isset($_POST['setshipment']) or $task=='setshipment'){
			$this->setshipment();
		} else if(isset($_POST['setpayment']) or $task=='setpayment'){
			$this->setpayment();
		} else {
			if (VmConfig::get('oncheckout_opc', 1) && $cart->virtuemart_shipmentmethod_id != JRequest::getInt('virtuemart_shipmentmethod_id')) {
				$this->setshipment();
			}
			if (VmConfig::get('oncheckout_opc', 1) && $cart->virtuemart_paymentmethod_id != JRequest::getInt('virtuemart_paymentmethod_id')) {
				$this->setpayment();
			}
			if ($cart && !VmConfig::get('use_as_catalog', 0)) {
				$cart->checkout();
			}
		}


	}

	/**
	 * Executes the confirmDone task,
	 * cart object checks itself, if the data is valid
	 *
	 * @author Max Milbers
	 *
	 */
	public function confirm() {

		vmdebug('confirm my post, get and so on',$_POST,$_GET);
		$cart = VirtueMartCart::getCart();
		$cart->getFilterCustomerComment();
		$cart->tosAccepted = JRequest::getInt('tosAccepted', $cart->tosAccepted);
		$task = JRequest::getString('task');
		$update = vRequest::getString('update',false);

		if(($update and is_array($update)) or $task=='update'){
			reset($update);
			$key = key($update);
			$quantity = vRequest::getInt('quantity');
			$cart->updateProductCart(key($update),$quantity[$key]);
			$this->display();
		} else if(isset($_POST['setcoupon']) or $task=='setcoupon'){
			$this->setcoupon();
		} else if(isset($_POST['setshipment']) or $task=='setshipment'){
			$this->setshipment();
		} else if(isset($_POST['setpayment']) or $task=='setpayment'){
			$this->setpayment();
		} else if($task=='confirm'){
			//T.Trung
			$cart = VirtueMartCart::getCart();
			
			$cart->BT = array();
			
			$cart->BT['ean'] = JRequest::getVar('ean');
			$cart->BT['authority'] = JRequest::getVar('authority');
			$cart->BT['order1'] = JRequest::getVar('order1');
			$cart->BT['person'] = JRequest::getVar('person');
			
			$cart->BT['company'] = JRequest::getVar('company');
			$cart->BT['cvr'] = JRequest::getVar('cvr');
				
			$cart->BT['email'] = JRequest::getVar('email');
			$cart->BT['first_name'] = JRequest::getVar('first_name');
			$cart->BT['last_name'] = JRequest::getVar('last_name');
			$cart->BT['street_name'] = JRequest::getVar('street_name');
			$cart->BT['street_number'] = JRequest::getVar('street_number');
			$cart->BT['zip'] = JRequest::getVar('zip');
			$cart->BT['city'] = JRequest::getVar('city');
			$cart->BT['phone_1'] = JRequest::getVar('phone_1');
            $cart->BT['message1'] = JRequest::getVar('message1');
			
			$cart->virtuemart_shipmentmethod_id = JRequest::getVar('virtuemart_shipmentmethod_id');
            $cart->virtuemart_paymentmethod_id = JRequest::getVar('virtuemart_paymentmethod_id');
			$cart->STsameAsBT = JRequest::getVar('STsameAsBT');
			$cart->tosAccepted = 1;
			
			if(JRequest::getVar('STsameAsBT')){
				$cart->ST = array();
				$cart->ST['first_name'] = $cart->BT['first_name'];
				$cart->ST['last_name'] = $cart->BT['last_name'];
				$cart->ST['street_name'] = $cart->BT['street_name'];
				$cart->ST['street_number'] = $cart->BT['street_number'];
				$cart->ST['zip'] = $cart->BT['zip'];
				$cart->ST['city'] = $cart->BT['city'];
				$cart->ST['phone_1'] = $cart->BT['phone_1'];
				$cart->ST['email'] = $cart->BT['email'];
				$cart->ST['message1'] = $cart->BT['message1'];
			} else {
				$cart->ST = array();
				if(JRequest::getVar('isGiftCard')){
					$cart->ST['first_name'] = JRequest::getVar('st_first_name');
					$cart->ST['last_name'] = JRequest::getVar('st_last_name');
					$cart->ST['email1'] = JRequest::getVar('st_email');
					$cart->ST['message1'] = JRequest::getVar('st_message1');
				} else {
					$cart->ST['first_name'] = JRequest::getVar('st_first_name');
					$cart->ST['last_name'] = JRequest::getVar('st_last_name');
					$cart->ST['street_name'] = JRequest::getVar('st_street_name');
					$cart->ST['street_number'] = JRequest::getVar('st_street_number');
					$cart->ST['zip'] = JRequest::getVar('st_zip');
					$cart->ST['city'] = JRequest::getVar('st_city');
					$cart->ST['phone_1'] = JRequest::getVar('st_phone');
				}
			}
			//T.Trung end
            //print_r($cart);exit;
			$cart->confirmDone();
            
			//T.Trung
            $orderModel=VmModel::getModel('orders');
            $order = $orderModel->getOrder($cart->virtuemart_order_id);
            //print_r($order);exit;
            $siteURL = JURI::base();
            if($order['details']['BT']->order_total == 0){
                $this->setRedirect( $siteURL . 'index.php?option=com_virtuemart&view=cart&layout=order_done&virtuemart_order_id='.$cart->virtuemart_order_id);
            } else {
                if(JRequest::getVar('mwctype') == 3){
                    $this->setRedirect( $siteURL . 'index.php?option=com_virtuemart&view=cart&layout=order_done&virtuemart_order_id='.$cart->virtuemart_order_id);
                } else {
                    $viabill = "";
                    if(JRequest::getVar('virtuemart_paymentmethod_id') == 2){
                        $viabill = "&viabill=1";
                    }
                    
                    $this->setRedirect($siteURL . 'index.php?option=com_virtuemart&view=cart&layout=payment&virtuemart_order_id='.$cart->virtuemart_order_id.$viabill);
                }
            }
			//T.Trung end
			/*$view = $this->getView('cart', 'html');
			$view->setLayout('order_done');
			$view->display();*/
		}

	}

	function cancel() {

		$cart = VirtueMartCart::getCart();
		if ($cart) {
			$cart->setOutOfCheckout();
		}
		$mainframe = JFactory::getApplication();
		$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE), 'Cancelled');
	}
	
	function requestCity(){
		$zip = JRequest::getVar('zip');
		$db = JFactory::getDBO();
		$db->setQuery('SELECT city FROM #__postnumber WHERE number = '.$zip);
		
		if($db->loadResult()){
			echo $db->loadResult();
			exit;
		} else {
			echo 0;
			exit;
		}
	}
    
    function checkGiftCard($virtuemart_product_ids, $cart){
        
        if(!empty($cart->products)){
            $product_model = VmModel::getModel('product');
            $virtuemart_category_ids = $product_model->getProductCategories($virtuemart_product_ids[0], true);
            $virtuemart_category_id = $virtuemart_category_ids[0];
            
            foreach($cart->products as $product){
                $category_arr[] = $product->virtuemart_category_id;
            }
            
           
            if(($virtuemart_category_id == 71) && ($category_arr[0] != 71)){
                $this->json->status = '1';
                echo json_encode($this->json);
                exit;
            }
            
            if(($virtuemart_category_id != 71) && ($category_arr[0] == 71)){
                $this->json->status = '1';
                echo json_encode($this->json);
                exit;
            }
            
        }
    }

}

//pure php no Tag

<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

define('AWOCOUPONPRO',    						'2');

define('com_awocoupon_ASSETS',    				JURI::base().'components/com_awocoupon/assets');
define('com_awocoupon_GIFTCERT_IMAGES',    	JPATH_ADMINISTRATOR.'/components/com_awocoupon/assets/giftcert/images');
define('com_awocoupon_GIFTCERT_FONTS',    		JPATH_ADMINISTRATOR.'/components/com_awocoupon/assets/giftcert/fonts');
define('com_awocoupon_GIFTCERT_TEMP',    		JPATH_ROOT.'/tmp');

define('AWOCOUPON',    						'awocoupon');
define('AWOCOUPON_OPTION',    					'com_awocoupon');

$estore = __get_awocoupon_estore__();
if(!empty($estore)) {
	define('AWOCOUPON_ESTORE',								$estore);
	define('AWOCOUPON_ESTOREHELPER',						'Awocoupon'.$estore.'Helper');
}


//default values

//variables
$AWOCOUPON_lang = array(
	'estore'=>array(
		'hikashop'=>'Hikashop',
		'redshop'=>'redShop',
		'virtuemart'=>'Virtuemart',
		'virtuemart1'=>'Virtuemart 1',
	),
	'function_type' => array(
		'coupon'=>JText::_( 'COM_AWOCOUPON_CP_COUPON' ),
		'giftcert'=>JText::_( 'COM_AWOCOUPON_GC_GIFTCERT' ),
		'shipping'=>JText::_( 'COM_AWOCOUPON_CP_SHIPPING' ),
		'buy_x_get_y'=>JText::_( 'COM_AWOCOUPON_CP_BUY_X_GET_Y' ),
		'parent'=>JText::_( 'COM_AWOCOUPON_CP_PARENT' ),
	),
	'asset_mode' => array(
		'include'=>JText::_( 'COM_AWOCOUPON_CP_INCLUDE' ),
		'exclude'=>JText::_( 'COM_AWOCOUPON_CP_EXCLUDE' ),
	),
	'asset_type' => array(
		'product'=>JText::_( 'COM_AWOCOUPON_CP_PRODUCT' ),
		'category'=>JText::_( 'COM_AWOCOUPON_CP_CATEGORY' ),
		'manufacturer'=>JText::_( 'COM_AWOCOUPON_CP_MANUFACTURER' ),
		'vendor'=>JText::_( 'COM_AWOCOUPON_CP_VENDOR' ),
		'shipping'=>JText::_( 'COM_AWOCOUPON_CP_SHIPPING' ),
		'coupon'=>JText::_( 'COM_AWOCOUPON_CP_COUPON' ),
	),
	'parent_type' => array(
		'first'=>JText::_( 'COM_AWOCOUPON_CP_PARENT_FIRST' ),
		'lowest'=>JText::_( 'COM_AWOCOUPON_CP_PARENT_LOWEST' ),
		'highest'=>JText::_( 'COM_AWOCOUPON_CP_PARENT_HIGHEST' ),
		'all'=>JText::_( 'COM_AWOCOUPON_CP_PARENT_ALL' ),
		'allonly'=>JText::_( 'COM_AWOCOUPON_CP_PARENT_ALL_ONLY' ),
	),
	'buy_xy_process_type' => array(
		'first'=>JText::_( 'COM_AWOCOUPON_CP_PARENT_FIRST' ),
		'lowest'=>JText::_( 'COM_AWOCOUPON_GBL_LOWEST_VALUE' ),
		'highest'=>JText::_( 'COM_AWOCOUPON_GBL_HIGHEST_VALUE' ),
	),
	'published' => array(
		'1'=>JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ),
		'-1'=>JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ),
		'-2'=>JText::_( 'COM_AWOCOUPON_CP_TEMPLATE' ),
	),
	'coupon_value_type' => array(
		'percent'=>JText::_( 'COM_AWOCOUPON_CP_PERCENTAGE' ),
		'amount'=>JText::_( 'COM_AWOCOUPON_CP_AMOUNT' ),
	),
	'discount_type' => array(
		'overall'=>JText::_( 'COM_AWOCOUPON_CP_OVERALL' ),
		'specific'=>JText::_( 'COM_AWOCOUPON_CP_SPECIFIC' ),
	),
	'min_value_type' => array(
		'overall'=>JText::_( 'COM_AWOCOUPON_CP_OVERALL' ),
		'specific'=>JText::_( 'COM_AWOCOUPON_CP_SPECIFIC' ),
	),
	'user_type' => array(
		'user'=>JText::_( 'COM_AWOCOUPON_CP_CUSTOMER' ),
		'usergroup'=>JText::_( 'COM_AWOCOUPON_CP_SHOPPER_GROUP' ),
	),
	'num_of_uses_type' => array(
		'total'=>JText::_( 'COM_AWOCOUPON_GBL_TOTAL' ),
		'per_user'=>JText::_( 'COM_AWOCOUPON_CP_PER_CUSTOMER' ),
	),	
	'expiration_type' => array(
		'day'=>JText::_('COM_AWOCOUPON_GC_DAY'),
		'month'=>JText::_('COM_AWOCOUPON_GC_MONTHS'),
		'year'=>JText::_('COM_AWOCOUPON_GC_YEARS'),
	),
	'giftcert_message_type' => array(
		'text'=>JText::_( 'COM_AWOCOUPON_PF_TEXT' ),
		'html'=>JText::_( 'COM_AWOCOUPON_PF_HTML' ),
	),
	'status' => array(
		'active'=>JText::_( 'COM_AWOCOUPON_GBL_ACTIVE' ),
		'inactive'=>JText::_( 'COM_AWOCOUPON_GBL_INACTIVE' ),
		'used'=>JText::_( 'COM_AWOCOUPON_GC_VALUE_USED' ),
	),
	
);

function __get_awocoupon_estore__() {
	$estore = JFactory::getApplication()->getUserStateFromRequest( 'com_awocoupon.global.estore', 	'estore', 	'', 'cmd' );
	if(empty($estore)) {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
		$params = new awoParams();
		$estore = $params->get('estore') ;
		if(empty($estore)) {
			require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
			$estores = awoLibrary::getInstalledEstores();
			if(!empty($estores)) {
				$estore = current($estores);
				JFactory::getApplication()->setUserState('com_awocoupon.global.estore',$estore);
				$params->set('estore',$estore);
			}
		}
	}
	return $estore;
}

<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelImport extends AwoCouponModel {

	var $_entry	 		= null;
	var $_id 			= null;
	var $_sections 		= null;

	/**
	 * Constructor
	 **/
	function __construct() {
		$this->_type = 'import';
		parent::__construct();

	}
	
	function map_keys($row) {
		$rtn = array();
		$keys = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF');
		return array_combine($keys,$row);
	}

	/**
	 * Method to store the entry
	 **/
	function store($data, $store_none_errors) {
	
		$number_of_rows = 32;
		
		$controller = new AwoCouponController( ); 
		$model_coupon = $controller->getModel('coupon'); 
		$error_check_only = $store_none_errors ? false : true;
		
		$arrdistinctcodes = array();
		foreach($data  as $row) {
			@$arrdistinctcodes[$row[1]]++;
		}
		

		$_map = array(
			'function_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_COUPON' )))=>'coupon',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GC_GIFTCERT' )))=>'giftcert',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SHIPPING' )))=>'shipping',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PARENT' )))=>'parent',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_BUY_X_GET_Y' )))=>'buy_x_get_y',
			),
			'asset_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PRODUCT' )))=>'product',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_CATEGORY' )))=>'category',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_MANUFACTURER' )))=>'manufacturer',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_VENDOR' )))=>'vendor',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SHIPPING' )))=>'shipping',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_COUPON' )))=>'coupon',
			),
			'parent_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PARENT_FIRST' )))=>'first',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PARENT_ALL' )))=>'all',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PARENT_LOWEST' )))=>'lowest',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PARENT_HIGHEST' )))=>'highest',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PARENT_ALL_ONLY' )))=>'allonly',
			),
			'buy_xy_process_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PARENT_FIRST' )))=>'first',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_LOWEST_VALUE' )))=>'lowest',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_HIGHEST_VALUE' )))=>'highest',
			),
			'asset_mode' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_INCLUDE' )))=>'include',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_EXCLUDE' )))=>'exclude',
			),
			'published' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' )))=>1,
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' )))=>-1,
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_TEMPLATE' )))=>-2,
			),
			'coupon_value_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PERCENTAGE' )))=>'percent',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_AMOUNT' )))=>'amount',
			),
			'discount_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_OVERALL' )))=>'overall',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SPECIFIC' )))=>'specific',
			),
			'min_value_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_OVERALL' )))=>'overall',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SPECIFIC' )))=>'specific',
			),
			'user_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_CUSTOMER' )))=>'user',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SHOPPER_GROUP' )))=>'usergroup',
			),
			'num_of_uses_type' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_TOTAL' )))=>'total',
				trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PER_CUSTOMER' )))=>'per_user',
			),
			'exclude_special' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_YES' )))=>1,
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_NO' )))=>0,
			),
			'exclude_giftcert' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_YES' )))=>1,
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_NO' )))=>0,
			),
			'product_match' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_YES' )))=>1,
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_NO' )))=>0,
			),
			'addtocart' => array(
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_YES' )))=>1,
				trim(strtolower(JText::_( 'COM_AWOCOUPON_GBL_NO' )))=>0,
			),
		);

		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		
		$_map_user = array_keys(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreUser')));
		$_map_usergroup = array_keys(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShopperGroup')));
		$_map_product = array_keys(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct')));
		$this->_db->setQuery('SELECT id FROM #__awocoupon');
		$_map_coupon = $this->_db->loadResultArray();
		$_map_category = array_keys(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreCategory'),null,null,100000));
		$_map_manufacturer = array_keys(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreManufacturer')));
		$_map_vendor = array_keys(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreVendor')));
		$_map_shipping = array_keys(call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShipping')));
		

		$errors = array();
		$datadistinct = array();
		foreach($data as $row) {
			if(count($row)>$number_of_rows) $row = array_slice($row,0,$number_of_rows);
		
		// first level error checking
			$id = $row[0];
			if(trim($id) == '') {
				$errors['          '][] = JText::_('COM_AWOCOUPON_IMP_NO_ID');
				continue;
			}
			if(isset($datadistinct[$id])) {
				$errors[$id][] = JText::_('COM_AWOCOUPON_IMP_DUPLICATE_ID');
				continue;
			}
			if($arrdistinctcodes[$row[1]]>1) {
				$errors[$id][] = JText::_('COM_AWOCOUPON_ERR_DUPLICATE_CODE');
				//continue;
			}
			if(count($row)<8) {
				$errors[$id][] = JText::_('COM_AWOCOUPON_IMP_COLUMNS_INVALID');
				continue;
			}
			$row = array_pad($row, $number_of_rows, '');
			
			
			$row = $this->map_keys($row);

			$datadistinct[$id] = $row;

			if(empty($row['B'])) $errors[$id][] = '[B] '.JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM');
			$datadistinct[$id]['C'] = $row['C'] = trim(strtolower($row['C']));
			if(empty($_map['published'][$row['C']])) $errors[$id][] = '[C] '.JText::_('COM_AWOCOUPON_CP_PUBLISHED').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			$datadistinct[$id]['D'] = $row['D'] = trim(strtolower($row['D']));
			if(empty($_map['function_type'][$row['D']])) $errors[$id][] = '[D] '.JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			$datadistinct[$id]['I'] = $row['I'] = trim(strtolower($row['I']));
			if(!empty($row['I']) && empty($_map['num_of_uses_type'][$row['I']])) $errors[$id][] = '[I] '.JText::_('COM_AWOCOUPON_CP_NUMBER_USES_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			if(!empty($row['J']) && !ctype_digit($row['J'])) $errors[$id][] = '[J] '.JText::_('COM_AWOCOUPON_CP_NUMBER_USES').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			
			if($_map['function_type'][$row['D']] == 'parent') {
				$datadistinct[$id]['AC'] = $row['AC'] = trim(strtolower($row['AC']));
				if(empty($row['AC']) || empty($_map['parent_type'][$row['AC']])) $errors[$id][] = '[AC] '.JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE').' 2: '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			} 
			else {
				$datadistinct[$id]['E'] = $row['E'] = trim(strtolower($row['E']));
				if(!empty($row['E']) && empty($_map['coupon_value_type'][$row['E']])) $errors[$id][] = '[E] '.JText::_('COM_AWOCOUPON_CP_VALUE_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				$datadistinct[$id]['F'] = $row['F'] = trim(strtolower($row['F']));
				if(!empty($row['F']) && empty($_map['discount_type'][$row['F']])) $errors[$id][] = '[F] '.JText::_('COM_AWOCOUPON_CP_DISCOUNT_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(!empty($row['G']) && (!is_numeric($row['G']) || $row['G']<0)) $errors[$id][] = '[G] '.JText::_('COM_AWOCOUPON_CP_VALUE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');

				// extra rows
				if(!empty($row['H']) && !preg_match("/^(\d+\-\d+;)+$/",$row['H'])) $errors[$id][] = '[H] '.JText::_('COM_AWOCOUPON_CP_VALUE_DEFINITION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				$datadistinct[$id]['K'] = $row['K'] = trim(strtolower($row['K']));
				if(!empty($row['K']) && empty($_map['min_value_type'][$row['K']])) $errors[$id][] = '[K] '.JText::_('COM_AWOCOUPON_CP_VALUE_MIN').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(!empty($row['L']) && (!is_numeric($row['L']) || $row['L']<0)) $errors[$id][] = '[L] '.JText::_('COM_AWOCOUPON_CP_VALUE_MIN').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(!empty($row['M'])) {
					if(strlen($row['M'])==8)
						if(!ctype_digit($row['M']))  $errors[$id][] = '[M] '.JText::_('COM_AWOCOUPON_CP_DATE_START').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
					elseif(strlen($row['M'])==15)
						if((!ctype_digit(substr($row['M'],0,8)) || !ctype_digit(substr($row['M'],9,6))))  $errors[$id][] = '[M] '.JText::_('COM_AWOCOUPON_CP_DATE_START').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
					else $errors[$id][] = '[M] '.JText::_('COM_AWOCOUPON_CP_DATE_START').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				}
				if(!empty($row['N'])) {
					if(strlen($row['N'])==8)
						if(!ctype_digit($row['N']))  $errors[$id][] = '[N] '.JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
					elseif(strlen($row['N'])==15)
						if((!ctype_digit(substr($row['N'],0,8)) || !ctype_digit(substr($row['N'],9,6))))  $errors[$id][] = '[N] '.JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
					else $errors[$id][] = '[N] '.JText::_('COM_AWOCOUPON_CP_EXPIRATION').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				}

				$datadistinct[$id]['O'] = $row['O'] = trim(strtolower($row['O']));
				if(!empty($row['Q']) && !empty($_map['user_type'][$row['O']])) {
					if(!preg_match('/^\s*\d+\s*(,\s*\d+)*\s*$/',$row['Q'])) $errors[$id][] = '[Q] '.JText::_('COM_AWOCOUPON_CP_ASSET').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
					else {
						$map = null;
						switch($row['O']) {
							case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_CUSTOMER' ))) : $map = $_map_user; break;
							case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SHOPPER_GROUP' ))) : $map = $_map_usergroup; break;
						}
						$users = explode(',',$row['Q']);
						if(!empty($map)) {
							$err = array_diff($users, $map);
							if(!empty($err)) $errors[$id][] =  '[Q] '.JText::_('COM_AWOCOUPON_CP_ASSET').': '.JText::_('COM_AWOCOUPON_ERR_NO_EXIST');
							$datadistinct[$id]['userlist'] = $users;
						}
						$datadistinct[$id]['P'] = $row['P'] = trim(strtolower($row['P']));
						if(!empty($row['P']) && empty($_map['asset_mode'][$row['P']])) $errors[$id][] = '[P] '.JText::_('COM_AWOCOUPON_GBL_USER').': '.JText::_('COM_AWOCOUPON_CP_ERR_SELECT_MODE');
					}		
				}		
				
				
				$datadistinct[$id]['S'] = $row['S'] = trim(strtolower($row['S']));
				if(!empty($row['S']) && empty($_map['asset_mode'][$row['S']])) $errors[$id][] = '[S] '.JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE_MODE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				$datadistinct[$id]['W'] = $row['W'] = trim(strtolower($row['W']));
				if(!empty($row['W']) && empty($_map['asset_mode'][$row['W']])) $errors[$id][] = '[W] '.JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE_MODE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				
				$datadistinct[$id]['Z'] = $row['Z'] = trim(strtolower($row['Z']));
				if(!empty($row['Z']) && !isset($_map['exclude_special'][$row['Z']])) $errors[$id][] = '[Z] '.JText::_('COM_AWOCOUPON_CP_EXCLUDE_SPECIAL').': '.JText::_('COM_AWOCOUPON_GBL_INVALID');
				
				$datadistinct[$id]['AA'] = $row['AA'] = trim(strtolower($row['AA']));
				if(!empty($row['AA']) && !isset($_map['exclude_giftcert'][$row['AA']])) $errors[$id][] = '[AA] '.JText::_('COM_AWOCOUPON_CP_EXCLUDE_GIFTCERT').': '.JText::_('COM_AWOCOUPON_GBL_INVALID');
				$datadistinct[$id]['AB'] = trim($row['AB']);
			}


			if($_map['function_type'][$row['D']] == 'buy_x_get_y') {

				if(empty($row['U']) || !preg_match('/^\s*\d+\s*(,\s*\d+)*\s*$/',$row['U'])) $errors[$id][] = '[U] '.JText::_('COM_AWOCOUPON_CP_ASSET').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');

				$map = null;
				$datadistinct[$id]['R'] = $row['R'] = trim(strtolower($row['R']));
				if(empty($_map['asset_type'][$row['R']])) $errors[$id][] = '[R] '.JText::_('COM_AWOCOUPON_CP_ASSET').' - '.JText::_('COM_AWOCOUPON_GBL_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(empty($row['T']) || !ctype_digit($row['T'])) $errors[$id][] = '[T] '.JText::_('COM_AWOCOUPON_CP_BUY_X').' - '.JText::_('COM_AWOCOUPON_GBL_NUMBER').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');

				switch($row['R']) {
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PRODUCT' ))) : $map = $_map_product; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_CATEGORY' ))) : $map = $_map_category;  break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_MANUFACTURER' ))) : $map = $_map_manufacturer; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_VENDOR' ))) : $map = $_map_vendor; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SHIPPING' ))) : $map = $_map_shipping; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_COUPON' ))) : $map = $_map_coupon; break;
				}
				$assets = explode(',',$row['U']);
				if(!empty($map)) {
					$err = array_diff($assets, $map);
					if(!empty($err)) $errors[$id][] =  '[U] '.JText::_('COM_AWOCOUPON_CP_ASSET').': '.JText::_('COM_AWOCOUPON_ERR_NO_EXIST');
					$datadistinct[$id]['assetlist'] = $assets;
				}
				
				$map = null;
				$datadistinct[$id]['V'] = $row['V'] = trim(strtolower($row['V']));
				if(empty($_map['asset_type'][$row['V']])) $errors[$id][] = '[V] '.JText::_('COM_AWOCOUPON_CP_ASSET').' - '.JText::_('COM_AWOCOUPON_GBL_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				if(empty($row['X']) || !ctype_digit($row['X'])) $errors[$id][] = '[X] '.JText::_('COM_AWOCOUPON_CP_GET_Y').' - '.JText::_('COM_AWOCOUPON_GBL_NUMBER').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');

				switch($row['V']) {
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PRODUCT' ))) : $map = $_map_product; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_CATEGORY' ))) : $map = $_map_category; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_MANUFACTURER' ))) : $map = $_map_manufacturer; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_VENDOR' ))) : $map = $_map_vendor; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_SHIPPING' ))) : $map = $_map_shipping; break;
					case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_COUPON' ))) : $map = $_map_coupon; break;
				}
				$assets = explode(',',$row['Y']);
				if(!empty($map)) {
					$err = array_diff($assets, $map);
					if(!empty($err)) $errors[$id][] =  '[Y] '.JText::_('COM_AWOCOUPON_CP_ASSET').' 2: '.JText::_('COM_AWOCOUPON_ERR_NO_EXIST');
					$datadistinct[$id]['assetlist2'] = $assets;
				}
				
				$datadistinct[$id]['AC'] = $row['AC'] = trim(strtolower($row['AC']));
				if(empty($row['AC']) || empty($_map['buy_xy_process_type'][$row['AC']])) $errors[$id][] = '[AC] '.JText::_('COM_AWOCOUPON_CP_PARENT_TYPE').' '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				
				if(!empty($row['AD']) && !ctype_digit($row['AD'])) $errors[$id][] = '[AD] '.JText::_('COM_AWOCOUPON_CP_MAX_DISCOUNT_QTY').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				$datadistinct[$id]['AE'] = $row['AE'] = trim(strtolower($row['AE']));
				if(!empty($row['AE']) && !isset($_map['product_match'][$row['AE']])) $errors[$id][] = '[AE] '.JText::_('COM_AWOCOUPON_CP_DONT_MIX_PRODUCTS').': '.JText::_('COM_AWOCOUPON_GBL_INVALID');
				$datadistinct[$id]['AF'] = $row['AF'] = trim(strtolower($row['AF']));
				if(!empty($row['AF']) && !isset($_map['addtocart'][$row['AF']])) $errors[$id][] = '[AF] '.JText::_('COM_AWOCOUPON_CP_ADDTOCART_GETY').': '.JText::_('COM_AWOCOUPON_GBL_INVALID');
			}



			if(!empty($row['U'])) {
				if(!preg_match('/^\s*\d+\s*(,\s*\d+)*\s*$/',$row['U'])) $errors[$id][] = '[U] '.JText::_('COM_AWOCOUPON_CP_ASSET').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				else {
					if($_map['function_type'][$row['D']] == 'giftcert' || $_map['function_type'][$row['D']] == 'coupon') {
						$datadistinct[$id]['R'] = $row['R'] = trim(strtolower($row['R']));
						if(empty($_map['asset_type'][$row['R']])) $errors[$id][] = '[R] '.JText::_('COM_AWOCOUPON_CP_ASSET').' - '.JText::_('COM_AWOCOUPON_GBL_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
						$map = null;
						switch($row['R']) {
							case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_PRODUCT' ))) : $map = $_map_product; break;
							case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_CATEGORY' ))) : $map = $_map_category; break;
							case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_MANUFACTURER' ))) : $map = $_map_manufacturer; break;
							case trim(strtolower(JText::_( 'COM_AWOCOUPON_CP_VENDOR' ))) : $map = $_map_vendor; break;
						}
					}
					elseif($_map['function_type'][$row['D']] == 'shipping') $map = $_map_shipping;
					elseif($_map['function_type'][$row['D']] == 'parent') $map = $_map_coupon;
					$assets = explode(',',$row['U']);
					if(!empty($map)) {
						$err = array_diff($assets, $map);
						if(!empty($err)) $errors[$id][] =  '[U] '.JText::_('COM_AWOCOUPON_CP_ASSET').': '.JText::_('COM_AWOCOUPON_ERR_NO_EXIST');
						$datadistinct[$id]['assetlist'] = $assets;
					}
							
				}	
			}

			if($_map['function_type'][$row['D']] == 'shipping' && !empty($row['Y'])) {
				if(!preg_match('/^\s*\d+\s*(,\s*\d+)*\s*$/',$row['Y'])) $errors[$id][] = '[Y] '.JText::_('COM_AWOCOUPON_CP_ASSET').' 2: '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				else {
					$products = explode(',',$row['Y']);
					$err = array_diff($products, $_map_product);
					if(!empty($err)) $errors[$id][] =  '[Y] '.JText::_('COM_AWOCOUPON_CP_ASSET').' 2: '.JText::_('COM_AWOCOUPON_ERR_NO_EXIST');
					$datadistinct[$id]['assetlist2'] = $products;
				}
			}
			if($_map['function_type'][$row['D']] == 'giftcert' && !empty($row['Y'])) {
				if(!preg_match('/^\s*\d+\s*(,\s*\d+)*\s*$/',$row['Y'])) $errors[$id][] = '[Y] '.JText::_('COM_AWOCOUPON_CP_ASSET').' 2: '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				else {
					$shippings = explode(',',$row['Y']);
					$err = array_diff($shippings, $_map_shipping);
					if(!empty($err)) $errors[$id][] =  '[Y] '.JText::_('COM_AWOCOUPON_CP_ASSET').' 2: '.JText::_('COM_AWOCOUPON_ERR_NO_EXIST');
					$datadistinct[$id]['assetlist2'] = $shippings;
				}
			}
	
		}

//printr($datadistinct);
		$dbdata = array();
		foreach($datadistinct as $id=>$row) {
			if(empty($errors[$id])) {
				$startdate_time = substr($row['M'],9);
				$expiration_time = substr($row['N'],9);
				$mydata = array(
					'function_type'=>$_map['function_type'][$row['D']],
					'parent_type'=>$_map['function_type'][$row['D']]=='parent' && isset($_map['parent_type'][$row['AC']]) ? $_map['parent_type'][$row['AC']] : '',
					'coupon_code'=>$row['B'],
					'published'=>$_map['published'][$row['C']],
					'coupon_value_type'=>isset($_map['coupon_value_type'][$row['E']]) ? $_map['coupon_value_type'][$row['E']] : '',
					'discount_type'=>isset($_map['discount_type'][$row['F']]) ? $_map['discount_type'][$row['F']] : '',
					'coupon_value'=>$row['G'],
					'coupon_value_def'=>$row['H'],
					'num_of_uses_type'=>isset($_map['num_of_uses_type'][$row['I']]) ? $_map['num_of_uses_type'][$row['I']] : '',
					'num_of_uses'=>$row['J'],
					'min_value'=>$row['L'],
					'startdate_date'=>!empty($row['M']) ? substr($row['M'],0,4).'-'.substr($row['M'],4,2).'-'.substr($row['M'],6,2) : '',
					'startdate_time'=>!empty($startdate_time) ? substr($row['M'],9,2).':'.substr($row['M'],11,2).':'.substr($row['M'],13,2) : '',
					'expiration_date'=>!empty($row['N']) ? substr($row['N'],0,4).'-'.substr($row['N'],4,2).'-'.substr($row['N'],6,2) : '',
					'expiration_time'=>!empty($expiration_time) ? substr($row['N'],9,2).':'.substr($row['N'],11,2).':'.substr($row['N'],13,2) : '',
					
					'exclude_special'=>!empty($_map['exclude_special'][$row['Z']]) ? $_map['exclude_special'][$row['Z']] : null,
					'exclude_giftcert'=>!empty($_map['exclude_giftcert'][$row['AA']]) ? $_map['exclude_giftcert'][$row['AA']] : null,
					
					'user_type'=>isset($_map['user_type'][$row['O']]) ? $_map['user_type'][$row['O']] : 'user',
					'userlist'=>!empty($row['userlist']) ? $row['userlist'] : array(),
					'user_mode'=>isset($_map['asset_mode'][$row['P']]) ? $_map['asset_mode'][$row['P']] : '',
					
					'note'=>$row['AB'],
					
					'asset1_function_type'=>isset($_map['asset_type'][$row['R']]) ? $_map['asset_type'][$row['R']] : '',
					'asset2_function_type'=>isset($_map['asset_type'][$row['V']]) ? $_map['asset_type'][$row['V']] : '',
					'asset1_qty'=>$row['T'],
					'asset2_qty'=>$row['X'],
					'assetlist'=>!empty($row['assetlist']) ? $row['assetlist'] : array(),
					'assetlist2'=>!empty($row['assetlist2']) ? $row['assetlist2'] : array(),
					'asset1_mode'=>isset($_map['asset_mode'][$row['S']]) ? $_map['asset_mode'][$row['S']] : '',
					'asset2_mode'=>isset($_map['asset_mode'][$row['W']]) ? $_map['asset_mode'][$row['W']] : '',
					
					'buy_xy_process_type'=>$_map['function_type'][$row['D']]=='buy_x_get_y' && isset($_map['buy_xy_process_type'][$row['AC']]) ? $_map['buy_xy_process_type'][$row['AC']] : '',
					'max_discount_qty'=>$row['AD'],
					'min_value_type'=>$row['K'],
					'product_match'=>!empty($_map['product_match'][$row['AE']]) ? $_map['product_match'][$row['AE']] : null,
					'addtocart'=>!empty($_map['addtocart'][$row['AF']]) ? $_map['addtocart'][$row['AF']] : null,
					
				);

				// check or insert into database
				$rtnErrors = $model_coupon->storeEach($mydata,$error_check_only);
				if(!empty($rtnErrors)) $errors[$id] = $rtnErrors;
				else $dbdata[] = $mydata;
			}
			if($error_check_only && count($dbdata)==count($data)) {
			// if just check and there are no errors, insert everything
				foreach($dbdata as $mydata) {
					$rtnErrors = $model_coupon->storeEach($mydata);
					if(!empty($rtnErrors)) $errors[$id] = $rtnErrors;
				}
			}
		}	
		return $errors;		
	}
}

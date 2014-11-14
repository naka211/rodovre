<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelCoupon extends AwoCouponModel {

	var $_entry	 		= null;
	var $_id 			= null;
	var $_sections 		= null;

	/**
	 * Constructor
	 **/
	function __construct() {
		$this->_type = 'coupon';
		parent::__construct();

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$this->setId($cid[0]);
	}

	/**
	 * Method to set the identifier
	 **/
	function setId($id) {
		// Set entry id and wipe data
		$this->_id	    = $id;
		$this->_entry	= null;
	}
	
	/**
	 * Overridden get method to get properties from the entry
	 **/
	function get($property, $default=null) {
		if ($this->_loadEntry()) {
			if(isset($this->_entry->$property)) {
				return $this->_entry->$property;
			}
		}
		return $default;
	}

	/**
	 * Method to get entry data
	 **/
	function &getEntry() {
	
		if ($this->_loadEntry()) {
			if (!class_exists( AWOCOUPON_ESTOREHELPER)) require JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';

			$this->_entry->userlist  = $this->_entry->assetlist  = $this->_entry->assetlist2 = array();		
			$this->_entry->startdate_date = !empty($this->_entry->startdate) ? substr($this->_entry->startdate,0,10) : '';
			$this->_entry->startdate_time = !empty($this->_entry->startdate) ? substr($this->_entry->startdate,11,8) : '';
			$this->_entry->expiration_date = !empty($this->_entry->expiration) ? substr($this->_entry->expiration,0,10) : '';
			$this->_entry->expiration_time = !empty($this->_entry->expiration) ? substr($this->_entry->expiration,11,8) : '';
			$this->_entry->asset1_mode = null;
			$this->_entry->asset2_mode = null;
			$this->_entry->min_value_type = null;
			
			$this->_entry->buy_xy_process_type = null;
			$this->_entry->asset1_function_type = null;
			$this->_entry->asset2_function_type = null;
			$this->_entry->max_discount_qty = null;
			$this->_entry->asset1_qty = null;
			$this->_entry->asset2_qty = null;
			$this->_entry->product_match = null;
			$this->_entry->addtocart = null;

			$this->_entry->params = json_decode($this->_entry->params);
			if(!empty($this->_entry->params->asset1_mode)) $this->_entry->asset1_mode = $this->_entry->params->asset1_mode;
			if(!empty($this->_entry->params->asset2_mode)) $this->_entry->asset2_mode = $this->_entry->params->asset2_mode;
			if(!empty($this->_entry->params->asset1_type)) $this->_entry->asset1_function_type = $this->_entry->params->asset1_type;
			if(!empty($this->_entry->params->asset2_type)) $this->_entry->asset2_function_type = $this->_entry->params->asset2_type;
			if(!empty($this->_entry->params->min_value_type)) $this->_entry->min_value_type = $this->_entry->params->min_value_type;
			if(!empty($this->_entry->params->exclude_special)) $this->_entry->exclude_special = $this->_entry->params->exclude_special;
			if(!empty($this->_entry->params->exclude_giftcert)) $this->_entry->exclude_giftcert = $this->_entry->params->exclude_giftcert;


			if($this->_entry->user_type == 'user') $this->_entry->userlist = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoUser'),$this->_id);
			elseif($this->_entry->user_type == 'usergroup') $this->_entry->userlist = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoShopperGroup'),$this->_id);
			$this->_entry->user_mode = !empty($this->_entry->params->user_mode) ? $this->_entry->params->user_mode : '';

			
			$this->_entry->assetlist = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'1',$this->_id);

				

			if($this->_entry->function_type == 'giftcert' && !empty($this->_entry->params->asset2_mode)) {
				$this->_entry->assetlist2 = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'2',$this->_id);
			}
			if($this->_entry->function_type == 'shipping' && !empty($this->_entry->params->asset2_mode)) {
				$this->_entry->assetlist2 = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'2',$this->_id);
			}
				
			if($this->_entry->function_type == 'buy_x_get_y') {
				$this->_entry->assetlist2 = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoItem'),'2',$this->_id);
					
				$this->_entry->buy_xy_process_type = $this->_entry->params->buy_xy_process_type;
				$this->_entry->max_discount_qty = $this->_entry->params->max_discount_qty;
				$this->_entry->asset1_qty = $this->_entry->params->asset1_qty;
				$this->_entry->asset2_qty = $this->_entry->params->asset2_qty;
				@$this->_entry->product_match = $this->_entry->params->product_match;
				@$this->_entry->addtocart = $this->_entry->params->addtocart;
					
			}
		
		}
		else  $this->_initEntry();

		return $this->_entry;
	}


	/**
	 * Method to load entry data
	 **/
	function _loadEntry() {
		// Lets load the entry if it doesn't already exist
		if (empty($this->_entry)) {
			$query = 'SELECT c.*
					   FROM #__awocoupon c 
					   WHERE estore="'.AWOCOUPON_ESTORE.'" AND c.id = '.$this->_id.'
					   GROUP BY c.id';
			$this->_db->setQuery($query);
			$this->_entry = $this->_db->loadObject();
			
			
			return (boolean) $this->_entry;
		}
		return true;
	}

	/**
	 * Method to initialise the entry data
	 **/
	function _initEntry() {
		// Lets load the entry if it doesn't already exist
		if (empty($this->_entry)) {
			
			$entry = new stdClass();

			$this->_entry					= JTable::getInstance('coupons', 'Table');
			$this->_entry->expiration_date = '';
			$this->_entry->expiration_time = '';
			$this->_entry->startdate_date = '';
			$this->_entry->startdate_time = '';
			
			$this->_entry->userlist = $this->_entry->assetlist = $this->_entry->assetlist2 = array();		
			$this->_entry->user_mode = '';
			$this->_entry->asset1_mode = '';
			$this->_entry->asset2_mode = '';
			$this->_entry->min_value_type = null;
			
			$this->_entry->buy_xy_process_type = null;
			$this->_entry->asset1_function_type = null;
			$this->_entry->asset2_function_type = null;
			$this->_entry->max_discount_qty = null;
			$this->_entry->asset1_qty = null;
			$this->_entry->asset2_qty = null;
			$this->_entry->exclude_special = null;
			$this->_entry->exclude_giftcert = null;
			$this->_entry->product_match = null;
			$this->_entry->addtocart = null;
			return (boolean) $this->_entry;
		}
		return true;
	}

	/**
	 * Method to store the entry
	 **/
	function store($data) {

		$errors = $this->storeEach($data);
		if(!empty($errors)) {
			foreach($errors as $error) JFactory::getApplication()->enqueueMessage($error, 'error');
			return false;
		}
		return true;
	
	}
	function storeEach($data,$error_check_only = false) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$errors = array();
//printrx($data);
		
		// set null fields
		$data['params'] = null;
		if(empty($data['asset1_mode'])) $data['asset1_mode'] = null;
		if(empty($data['asset2_mode'])) $data['asset2_mode'] = null;
		$data['product_match'] = empty($data['product_match']) ? 0 : 1;
		$data['addtocart'] = empty($data['addtocart']) ? 0 : 1;

				
		$row 		= JTable::getInstance('coupons', 'Table');
		if(!empty($data['id'])) $row->load((int)$data['id']);	
		$user		= JFactory::getUser();
		$details	= JRequest::getVar( 'details', array(), 'post', 'array');
		$nullDate	= $this->_db->getNullDate();
		
		// bind it to the table
		if (!$row->bind($data)) {
			$errors[] = $this->_db->getErrorMsg();
		}

		if(!isset($data['coupon_value']) || !is_numeric($data['coupon_value']) || $data['coupon_value']<0) $row->coupon_value = null;
		if(empty($data['coupon_value_def'])) $row->coupon_value_def = null;
		if(empty($data['num_of_uses_type'])) $row->num_of_uses_type = null;
		if(empty($data['num_of_uses'])) $row->num_of_uses = null;
		if(empty($data['min_value'])) $row->min_value = null;
		if(empty($data['discount_type'])) $row->discount_type = null;
		$row->startdate = $row->expiration = null;
		if(!empty($data['startdate_date']) && $data['startdate_date']!='YYYY-MM-DD')
			$row->startdate = $data['startdate_date'].' '.trim(!empty($data['startdate_time']) && $data['startdate_time']!='hh:mm:ss' ? $data['startdate_time'] : '00:00:00');
		if(!empty($data['expiration_date']) && $data['expiration_date']!='YYYY-MM-DD') 
			$row->expiration = $data['expiration_date'].' '.trim(!empty($data['expiration_time']) && $data['expiration_time']!='hh:mm:ss' ? $data['expiration_time'] : '23:59:59');
		$row->exclude_special = empty($data['exclude_special']) ? null : 1;
		$row->exclude_giftcert = empty($data['exclude_giftcert']) ? null : 1;
		if(empty($data['order_id'])) $row->order_id = null;
		if(empty($data['template_id'])) $row->template_id = null;
		if(empty($data['note'])) $row->note = null;


		// sanitise fields
		$row->id 			= (int) $row->id;
		$row->estore = AWOCOUPON_ESTORE;
		$row->exclude_special = empty($data['exclude_special']) ? null : 1;
		$row->exclude_giftcert = empty($data['exclude_giftcert']) ? null : 1;

		// Make sure the data is valid
		if (!$row->check()) {
			foreach($row->getErrors() as $err) $errors[] = $err;
		}

		if(empty($row->id)) {
		//Error: That coupon code already exists. Please try again.
			$sql = 'SELECT id FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'" AND coupon_code = \''.$row->coupon_code.'\'';
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadObjectList();
			if(!empty($tmp)) {
				$errors[] = JText::_('COM_AWOCOUPON_ERR_DUPLICATE_CODE');
			}

		} else {
			$sql = 'SELECT id FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'" AND coupon_code = \''.$row->coupon_code.'\' AND id NOT IN ('.$row->id.')';
			$this->_db->setQuery($sql);
			$tmp = $this->_db->loadResult();
			if(!empty($tmp)) {
				$errors[] = JText::_('COM_AWOCOUPON_ERR_DUPLICATE_CODE');
			}
		}
		if(empty($row->passcode)) $row->passcode = substr( md5((string)time().rand(1,1000).$row->coupon_code ), 0, 6);
				
		//error checker
		if($row->function_type=='parent' && empty($data['assetlist'])) {
			$errors[] = JText::_('COM_AWOCOUPON_CP_COUPON').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
		}
		if($row->function_type=='coupon' && empty($data['assetlist']) && $row->discount_type=='specific') {
			$errors[] = JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC');
		}

		if( $row->function_type!='parent' && !empty($data['assetlist']) && empty($data['asset1_mode'])) {
			$errors[] = JText::_('COM_AWOCOUPON_CP_ERR_SELECT_MODE');
		}
		if($row->function_type=='shipping' && empty($data['assetlist']) && empty($data['assetlist2']) && $row->discount_type=='specific') {
			$errors[] = JText::_('COM_AWOCOUPON_CP_ERR_ONE_SPECIFIC');
		}
		if( $row->function_type=='buy_x_get_y') {
			if(empty($data['buy_xy_process_type']) || ($data['buy_xy_process_type']!='first' && $data['buy_xy_process_type']!='lowest' && $data['buy_xy_process_type']!='highest'))
				$errors[] = JText::_('COM_AWOCOUPON_CP_PARENT_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
			if(!empty($data['max_discount_qty']) && !ctype_digit($data['max_discount_qty']))  $errors[] = JText::_('COM_AWOCOUPON_CP_MAX_DISCOUNT_QTY').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');

			if(empty($data['assetlist'])) $errors[] = JText::_('COM_AWOCOUPON_CP_BUY_X').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
			if(empty($data['assetlist2'])) $errors[] = JText::_('COM_AWOCOUPON_CP_GET_Y').': '.JText::_('COM_AWOCOUPON_ERR_MAKE_SELECTION');
					
			if(empty($data['asset1_qty']) || !ctype_digit($data['asset1_qty']))
				$errors[] = JText::_('COM_AWOCOUPON_CP_BUY_X').' -> '.JText::_('COM_AWOCOUPON_GBL_NUMBER').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			if(empty($data['asset2_qty']) || !ctype_digit($data['asset2_qty']))
				$errors[] = JText::_('COM_AWOCOUPON_CP_GET_Y').' -> '.JText::_('COM_AWOCOUPON_GBL_NUMBER').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
				
			if($data['asset1_function_type']!='product' && $data['asset1_function_type']!='category'
			&& $data['asset1_function_type']!='manufacturer' && $data['asset1_function_type']!='vendor')
				$err[] = JText::_('COM_AWOCOUPON_CP_BUY_X').' => '.JText::_('COM_AWOCOUPON_GBL_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			if($data['asset2_function_type']!='product' && $data['asset2_function_type']!='category'
			&& $data['asset2_function_type']!='manufacturer' && $data['asset2_function_type']!='vendor')
				$err[] = JText::_('COM_AWOCOUPON_CP_GET_Y').' => '.JText::_('COM_AWOCOUPON_GBL_TYPE').': '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
			
			if(!empty($data['user_mode']) && $data['user_mode']!='include' && $data['user_mode']!='exclude') $err[] = JText::_('COM_AWOCOUPON_GBL_USER').' '.JText::_('COM_AWOCOUPON_CP_ERR_SELECT_MODE');
			if(!empty($data['asset2_mode']) && $data['asset2_mode']!='include' && $data['asset2_mode']!='exclude') $err[] = JText::_('COM_AWOCOUPON_CP_FUNCTION_TYPE_MODE').' 2: '.JText::_('COM_AWOCOUPON_ERR_ENTER_VALID_VALUE');
		}
		
		
		
		
		
		
		// take a break and return if there are any errors
		if(!empty($errors) || $error_check_only) return $errors;
				
		

		//correct invalid data
		$params = array();
		if($row->function_type=='coupon') {
			if(empty($row->user_type)) $row->user_type = 'user';
			if(!empty($data['userlist'])) $params['user_mode'] = $data['user_mode'];
			if(empty($row->num_of_uses_type) || empty($row->num_of_uses)) {
				$row->num_of_uses_type = null;
				$row->num_of_uses = null;
			}


			if(!is_null($row->coupon_value))	$row->coupon_value_def = null;
			else $row->coupon_value = null;
			if(empty($data['assetlist'])) {
				$row->discount_type = 'overall';
			}
			else {
				$params['asset1_type'] = $data['asset1_function_type'];
				$params['asset1_mode'] = $data['asset1_mode'];
			}
			$row->parent_type = null;

			
			if(!empty($data['min_value'])) {
				if(empty($data['min_value_type'])) $data['min_value_type'] = 'overall';
				$params['min_value_type'] = $data['min_value_type'];
			}
				
		}
		elseif($row->function_type=='shipping') {
			if(empty($row->user_type)) $row->user_type = 'user';
			if(!empty($data['userlist'])) $params['user_mode'] = $data['user_mode'];
			if(empty($row->num_of_uses_type) || empty($row->num_of_uses)) {
				$row->num_of_uses_type = null;
				$row->num_of_uses = null;
			}

			
			$row->coupon_value_def = null;
			if(!empty($data['assetlist'])) {
				$params['asset1_type'] = 'shipping';
				$params['asset1_mode'] = $data['asset1_mode'];
			}
			if(empty($data['assetlist2'])) $row->discount_type = null;
			else {
				$params['asset2_type'] = 'product';
				$params['asset2_mode'] = $data['asset2_mode'];
			}
			$row->exclude_special = null;
			$row->exclude_giftcert = null;
			$row->parent_type = null;
			
			if(!empty($data['min_value'])) {
				if(empty($data['min_value_type'])) $data['min_value_type'] = 'overall';
				$params['min_value_type'] = $data['min_value_type'];
			}
				
		}
		elseif($row->function_type=='buy_x_get_y') {
			if(empty($row->user_type)) $row->user_type = 'user';
			if(!empty($data['userlist'])) $params['user_mode'] = $data['user_mode'];
			if(empty($row->num_of_uses_type) || empty($row->num_of_uses)) {
				$row->num_of_uses_type = null;
				$row->num_of_uses = null;
			}
			
			$params['buy_xy_process_type'] = $data['buy_xy_process_type'];
			$params['max_discount_qty'] = $data['max_discount_qty'];
			$params['asset1_type'] = $data['asset1_function_type'];
			$params['asset2_type'] = $data['asset2_function_type'];
			$params['asset1_qty'] = $data['asset1_qty'];
			$params['asset2_qty'] = $data['asset2_qty'];
			$params['asset1_mode'] = $data['asset1_mode'];
			$params['asset2_mode'] = $data['asset2_mode'];
			$params['product_match'] = $data['product_match'];
			$params['addtocart'] = $data['addtocart'];
				

			$row->coupon_value_def = null;
			$row->discount_type = null;
			$row->exclude_special = null;
			$row->exclude_giftcert = null;
			$row->parent_type = null;
				
			if(!empty($data['min_value'])) {
				if(empty($data['min_value_type'])) $data['min_value_type'] = 'overall';
				$params['min_value_type'] = $data['min_value_type'];
			}
								
		}		
		elseif($row->function_type=='parent') {
			if(empty($row->user_type)) $row->user_type = 'user';
			if(!empty($data['userlist'])) $params['user_mode'] = $data['user_mode'];
			if(empty($row->num_of_uses_type) || empty($row->num_of_uses)) {
				$row->num_of_uses_type = null;
				$row->num_of_uses = null;
			}

			$row->coupon_value_type = null;
			$row->coupon_value = null;
			$row->coupon_value_def = null;
			$row->min_value = null;
			$row->discount_type = null;
			$row->user_type = null;
			$row->exclude_special = null;
			$row->exclude_giftcert = null;
			$params['asset1_type'] = 'coupon';
			
			if(!empty($data['min_value'])) {
				if(empty($data['min_value_type'])) $data['min_value_type'] = 'overall';
				$params['min_value_type'] = $data['min_value_type'];
			}
				
		}		
		elseif($row->function_type=='giftcert') {
			$row->coupon_value_type = 'amount';
			$row->coupon_value_def = null;
			$row->num_of_uses_type = null;
			$row->num_of_uses = null;
			$row->min_value = null;
			$row->discount_type = null;
			$row->user_type = null;
			$row->exclude_special = null;
			$row->parent_type = null;
			
			if(!empty($data['assetlist'])) {
				$params['asset1_type'] = $data['asset1_function_type'];
				$params['asset1_mode'] = $data['asset1_mode'];
			}
			if(!empty($data['assetlist2'])) {
				$params['asset2_type'] = 'shipping';
				$params['asset2_mode'] = $data['asset2_mode'];
			}
		}
		
		if(!empty($params)) $row->params = json_encode($params);

		// Store the entry to the database
		if (!$row->store(true)) {
			$errors[] = $this->_db->stderr();
			return $errors;
		}
					
			
		// clean out the products/users tables
		if(!empty($row->id)) {
			$this->_db->setQuery('DELETE FROM #__awocoupon_user WHERE coupon_id = '.$row->id); $this->_db->query();
			$this->_db->setQuery('DELETE FROM #__awocoupon_usergroup WHERE coupon_id = '.$row->id); $this->_db->query();

			$this->_db->setQuery('DELETE FROM #__awocoupon_asset1 WHERE coupon_id = '.$row->id); $this->_db->query();
			$this->_db->setQuery('DELETE FROM #__awocoupon_asset2 WHERE coupon_id = '.$row->id); $this->_db->query();
		}
		
		
		//store products and users if chosen
		if(!empty($data['userlist'])) {
			$insert_str = '';
			foreach($data['userlist'] as $tmp) $insert_str .= '('.$row->id.',\''.$tmp.'\'),';
			if($row->user_type == 'user') {
				$this->_db->setQuery('INSERT INTO #__awocoupon_user (coupon_id, user_id) VALUES '.substr($insert_str,0,-1));
				$this->_db->query();
			}
			elseif($row->user_type == 'usergroup') {
				$this->_db->setQuery('INSERT INTO #__awocoupon_usergroup (coupon_id, shopper_group_id) VALUES '.substr($insert_str,0,-1));
				$this->_db->query();
			}
		}

		if(!empty($data['assetlist'])) {
			$insert_str = '';
			if($row->function_type == 'parent') {
				$i = 0;
				foreach($data['assetlist'] as $tmp) $insert_str .= '('.$row->id.',"coupon",'.$tmp.','.++$i.'),';
				$this->_db->setQuery('INSERT INTO #__awocoupon_asset1 (coupon_id, asset_type, asset_id, order_by) VALUES '.substr($insert_str,0,-1));
				$this->_db->query();
			} 
			else {
				$asset_type = $row->function_type=='shipping' ? 'shipping' : $data['asset1_function_type'];
				foreach($data['assetlist'] as $tmp) $insert_str .= '('.$row->id.',"'.$asset_type.'",'.$tmp.'),';
				$this->_db->setQuery('INSERT INTO #__awocoupon_asset1 (coupon_id, asset_type,asset_id) VALUES '.substr($insert_str,0,-1));
				$this->_db->query();
				
			}
		}
		if(!empty($data['assetlist2'])) {
			$insert_str = '';
			
			$asset_type_str = '';
			if($row->function_type == 'shipping') $asset_type_str = 'product';
			elseif($row->function_type == 'buy_x_get_y')  $asset_type_str = $data['asset2_function_type'];
			elseif($row->function_type == 'giftcert')  $asset_type_str = 'shipping';
			
			if(!empty($asset_type_str)) {
				foreach($data['assetlist2'] as $tmp) $insert_str .= '('.$row->id.',"'.$asset_type_str.'",'.$tmp.'),';
				$this->_db->setQuery('INSERT INTO #__awocoupon_asset2 (coupon_id, asset_type, asset_id) VALUES '.substr($insert_str,0,-1));
				$this->_db->query();
			}
		}
				
		$this->_entry	=& $row;
		
		return;
	}

	function generatecoupons($data) {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/plgautogenerate.php';
		$number = (int)$data['number'];
		$template_id = (int)$data['template'];
		if(empty($number) || empty($template_id)) return;
		
		for($i=0; $i<$number; $i++) awoAutoGenerate::generateCoupon($template_id,null,null,null,AWOCOUPON_ESTORE);
		return true;
	
	}

	function duplicatecoupon($cid) {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/plgautogenerate.php';
		$template_id = (int)$cid;
		if(empty($template_id)) return false;
		
		$rtn = awoAutoGenerate::generateCoupon($template_id,null,null,null,AWOCOUPON_ESTORE);
		if(empty($rtn->coupon_id)) {
			$this->setError('COM_AWOCOUPON_GBL_ERROR', 'error');
			return false;
		}
		return $rtn;
	
	}

}

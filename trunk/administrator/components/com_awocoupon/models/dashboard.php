<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

class AwoCouponModelDashboard extends AwoCouponModel {
	var $_data = null;

	/**
	 * Constructor
	 **/
	function __construct() {
		$this->_type = 'coupons';
		parent::__construct(); 
	}

	/**
	 * Method to get general stats
	 **/
	function getGeneralstats() {
		$_products = array();

		/*
		* Get total number of entries
		*/
		$sql = 'SELECT count(id)  FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'"'; 
		$this->_db->SetQuery($sql);
  		$_products['total'] = $this->_db->loadResult();

 		/*
		* Get total number of approved entries
		*/
		$current_date = date('Y-m-d H:i:s');
		$sql = 'SELECT count(id) 
				  FROM #__awocoupon 
				 WHERE published=1 
				   AND estore="'.AWOCOUPON_ESTORE.'"
				   AND ( ((startdate IS NULL OR startdate="") 	AND (expiration IS NULL OR expiration="")) OR
						 ((expiration IS NULL OR expiration="") AND startdate<="'.$current_date.'") OR
						 ((startdate IS NULL OR startdate="") 	AND expiration>="'.$current_date.'") OR
						 (startdate<="'.$current_date.'"		AND expiration>="'.$current_date.'")
					   )
				'; 
		$this->_db->SetQuery($sql);
  		$_products['active'] = $this->_db->loadResult();
		
		$sql = 'SELECT count(id) 
				  FROM #__awocoupon 
				 WHERE estore="'.AWOCOUPON_ESTORE.'" AND (published=-1  OR startdate>"'.$current_date.'" OR expiration<"'.$current_date.'")';
		$this->_db->SetQuery($sql);
  		$_products['inactive'] = $this->_db->loadResult();
		
		$sql = 'SELECT count(id) 
				  FROM #__awocoupon
				 WHERE estore="'.AWOCOUPON_ESTORE.'" AND published=-2'; 
		$this->_db->SetQuery($sql);
  		$_products['templates'] = $this->_db->loadResult();
		
		return $_products;
  		
	}

	/**
	 * Method to get popular data
	 **/
	function getLastEntered() {
		$query = 'SELECT id,coupon_code,coupon_value_type,coupon_value,function_type FROM #__awocoupon WHERE estore="'.AWOCOUPON_ESTORE.'"  ORDER BY id DESC LIMIT 5';
		$this->_db->SetQuery($query);
  		$hits = $this->_db->loadObjectList();
  		
  		return $hits;
	}

	
	static function getVersionUpdate() {
		$path = 'sites/default/files/extstatus/awocoupon2.xml';
		$domain = 'awodev.com';
	 	$url = 'http://'.$domain.'/'.$path;
		$data = '';
		$check = array();
		$check['connect'] = 0;
		$instance = new AwoCouponModelDashboard;
		$check['current_version'] = $instance->getFullLocalVersion();

		//try to connect via cURL
		if(function_exists('curl_init') && function_exists('curl_exec')) {
			$ch = @curl_init();
			
			@curl_setopt($ch, CURLOPT_URL, $url);
			@curl_setopt($ch, CURLOPT_HEADER, 0);
			//http code is greater than or equal to 300 ->fail
			@curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			//timeout of 5s just in case
			@curl_setopt($ch, CURLOPT_TIMEOUT, 5);
						
			$data = @curl_exec($ch);
						
			@curl_close($ch);
		}

		//try to connect via fsockopen
		if(function_exists('fsockopen') && $data == '') {

			$errno = 0;
			$errstr = '';

			//timeout handling: 5s for the socket and 5s for the stream = 10s
			$fsock = @fsockopen($domain, 80, $errno, $errstr, 5);
		
			if ($fsock) {
				@fputs($fsock, "GET /".$path." HTTP/1.1\r\n");
				@fputs($fsock, "HOST: ".$domain."\r\n");
				@fputs($fsock, "Connection: close\r\n\r\n");
        
				//force stream timeout...
				@stream_set_blocking($fsock, 1);
				@stream_set_timeout($fsock, 5);
				 
				$get_info = false;
				while (!@feof($fsock))
				{
					if ($get_info)
					{
						$data .= @fread($fsock, 1024);
					}
					else
					{
						if (@fgets($fsock, 1024) == "\r\n")
						{
							$get_info = true;
						}
					}
				}        	
				@fclose($fsock);
				
				//need to check data cause http error codes aren't supported here
				if(!strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>')) {
					$data = '';
				}
			}
		}

	 	//try to connect via fopen
		if (function_exists('fopen') && ini_get('allow_url_fopen') && $data == '') {
		
			//set socket timeout
			ini_set('default_socket_timeout', 5);
			
			$handle = @fopen ($url, 'r');
			
			//set stream timeout
			@stream_set_blocking($handle, 1);
			@stream_set_timeout($handle, 5);
			
			$data	= @fread($handle, 1000);
			
			@fclose($handle);
		}
						
		if( $data && strstr($data, '<?xml version="1.0" encoding="utf-8"?><update>') ) {
			$element = simplexml_load_string($data);
			
			$check['version'] 		= (string)$element->version;
			$check['released'] 		= (string)$element->released;
			$check['connect'] 		= 1;
			$check['enabled'] 		= 1;
			
			$check['current'] 		= version_compare( $check['current_version'], $check['version'] );
		}
		
		return $check;
	 }
	function getLocalBuild() {
		$versionString	= $this->getFullLocalVersion();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[2]) )
		{
			return $tmpArray[2];
		}
		
		// Unknown build number.
		return 0;
	}
	function getLocalVersion() {
		$versionString	= $this->getFullLocalVersion();
		$tmpArray		= explode( '.' , $versionString );
		
		if( isset($tmpArray[0] ) && isset( $tmpArray[1] ) )
		{
			return doubleval( $tmpArray[0] . '.' . $tmpArray[1] ); 
		}
		return 0;
	}
	function getFullLocalVersion() {
		static $version		= '';

		if( empty( $version ) ) {
		
		
			$element = simplexml_load_file(JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon'.(version_compare(JVERSION, '3.0.0', 'ge') ? '_j3':'').'.xml');
			$version = (string)$element->version;
			$version 		= trim(str_replace(array(' ','pro'),'',$version));
		}
		return $version;
	}

	function getLicense() {
		$license = $website = $expiration = null;
		$this->_db->setQuery('SELECT id,value FROM #__awocoupon_license WHERE id IN ("license", "expiration","website")');
		$rows = $this->_db->loadObjectList();
		foreach($rows as $row) {
			if($row->id=='license') $license = $row->value;
			elseif($row->id=='expiration') $expiration = $row->value;
			elseif($row->id=='website') $website = explode('|',$row->value);
		}
		return (object) array('l'=>$license,'url'=>!empty($website) ? current($website) : '','exp'=>$expiration);
	}

	function getpluginrow() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		$folders = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAwoCouponPluginFolder'));
		$sqlfolders = implode('","',explode(',',$folders));

		// Lets load the files if it doesn't already exist
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$sql = 'SELECT extension_id as id,name,element,folder,client_id,enabled,
							access,params,checked_out,checked_out_time,ordering,CONCAT(folder,"-",element) as keyid
					  FROM #__extensions
					 WHERE type="plugin" AND element="awocoupon" AND folder IN ("'.$sqlfolders.'")';
		}
		else {
			$sql = 'SELECT id,name,element,folder,client_id,published as enabled,
							access,params,checked_out,checked_out_time,ordering,CONCAT(folder,"-",element) as keyid
					  FROM #__plugins 
					 WHERE element="awocoupon" AND folder IN ("'.$sqlfolders.'")';
		}
		$this->_db->setQuery($sql);
		return array('folders'=>explode(',',$folders),'data'=>$this->_db->loadObjectList('keyid'));
	}




	function publishplugin($publish,$id) {

		$sql = version_compare( JVERSION, '1.6.0', 'ge' )
					? 'UPDATE #__extensions SET enabled='.(int)$publish.' WHERE extension_id='.(int)$id
					: 'UPDATE #__plugins SET published='.(int)$publish.' WHERE id='.(int)$id;
		$this->_db->setQuery( $sql );
	
		if (!$this->_db->query()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;		
		
	}
	

	
	
	static function deleteExpiredCoupons() {
		
		$db = JFactory::getDBO();
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
		$params = new awoParams();
		$days_expired = $params->get('delete_expired','');
		
		if(!empty($days_expired) && ctype_digit($days_expired)) {
			$current_date = date('Y-m-d H:i:s',strtotime('-'.$days_expired.' days') );
			$sql = 'SELECT id FROM #__awocoupon WHERE expiration<"'.$current_date.'"';
			$db->setQuery($sql);
			$list = $db->loadResultArray();

			if(!empty($list)) {
				$controller = new AwoCouponController( );
				$model_coupon = $controller->getModel('coupons');
				$model_coupon->delete($list);
			}
			
		}
	}

}

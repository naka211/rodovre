<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelConfig extends AwoCouponModel {

	function __construct() {
		$this->_type = 'config';
		parent::__construct();
	}
	
	function getDefaultError() {
		return '';
	}
	function getOrderStatus() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		return call_user_func(array(AWOCOUPON_ESTOREHELPER,'getOrderStatuses'));
	}
	function getInstalledEstores() {
		global $AWOCOUPON_lang;
		$estores = array();
		$dir = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore';
		$dh  = opendir($dir);
		while (false !== ($name = readdir($dh))) {
			if($name=='.') continue;
			if($name=='..') continue;
			if(!is_dir($dir.'/'.$name)) continue;
			if(isset($AWOCOUPON_lang['estore'][$name])) $estores[] = $name;
		}


		$installedestores = array();
		foreach($estores as $estore) {
			if (!class_exists( 'Awocoupon'.$estore.'Helper' )) require JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.$estore.'/helper.php';
			if(call_user_func(array('Awocoupon'.$estore.'Helper','isInstalled'))) {
				$installedestores[] = $estore;
			}
		}
		return $installedestores;
	}
		
	function store($data) {
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
				
		if(!empty($data['params'])) {
			require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
			$params = new awoParams();
			foreach($data['params'] as $name=>$value) $params->set($name,$value);
		}
				
		if(!empty($data['install_joomfish']) && $data['install_joomfish']==1) {
			if (file_exists(JPATH_ADMINISTRATOR.'/components/com_joomfish/joomfish.php')
			&& !file_exists(JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/awocoupon_config.xml')) {
				@copy(JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/joomfish_awocoupon_config.xml',
					  JPATH_ADMINISTRATOR.'/components/com_joomfish/contentelements/awocoupon_config.xml');
			}
		}

		if(isset($data['casesensitive'],$data['casesensitiveold']) 
		&& $data['casesensitive']!=$data['casesensitiveold']
		&& ($data['casesensitive']==1 || $data['casesensitive']==0)) {
			$sql = $data['casesensitive'] == 0 
					? 'ALTER TABLE `#__awocoupon` MODIFY `coupon_code` VARCHAR(32) NOT NULL DEFAULT ""'
					: 'ALTER TABLE `#__awocoupon` MODIFY `coupon_code` VARCHAR(32) BINARY NOT NULL DEFAULT ""'
					;
			$this->_db->setQuery($sql);
			$this->_db->query();
		}
		
		// reset estore global variable
		JFactory::getApplication()->setUserState('com_awocoupon.global.estore','');


		
		return true;
	}
		

}
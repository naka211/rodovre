<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

class awoParams {

	var $params = null;

	function awoParams() {
		$db  = JFactory::getDBO();
		$db->setQuery('SELECT id,name,is_json,value FROM #__awocoupon_config');
		$this->params = $db->loadObjectList('name');
	}
	
	function get($param,$default='') { 
		$value = isset($this->params[$param]->value) ? $this->params[$param]->value : '';
		if(!empty($value) && !empty($this->params[$param]->is_json)) $value = json_decode($value);
		return (empty($value) && ($value !== 0) && ($value !== '0')) ? $default : $value; 
	}
	function set($key, $value = '') {
		if(!empty($key)) {
			$db  = JFactory::getDBO();
			//$value = empty($value) ? 'NULL' : '"'.mysql_real_escape_string($value).'"';
			
			$is_json = 'NULL';
			if(is_array($value)) {
				$value = json_encode($value);
				$is_json = 1;
			}
			$value = empty($value) ? 'NULL' : '"'.awolibrary::dbEscape($value).'"';
			$db->setQuery('SELECT name FROM #__awocoupon_config WHERE name="'.$key.'"');
			$tmp = $db->loadResult();
			$sql = empty($tmp)
						? 'INSERT INTO #__awocoupon_config (name,value,is_json) VALUES ("'.$key.'",'.$value.','.$is_json.')'
						: 'UPDATE #__awocoupon_config SET value='.$value.',is_json='.$is_json.' WHERE name="'.$key.'"';
			$db->setQuery($sql);
			$db->query();
		}
	}


}

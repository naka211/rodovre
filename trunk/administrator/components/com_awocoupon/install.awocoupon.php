<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/



function com_install(){ 
	$installer = new awocouponInstall('install');
	$installer->install_particulars(); 
}

function com_uninstall(){

	echo '<div><b>Database Tables Uninstallation: <font color="green">Successful</font></b></div>';

	$installer = new awocouponInstall('uninstall');
	$installer->uninstall_injections();
	$installer->uninstall_plugins();
}


class com_awocouponInstallerScript {

	function install($parent) {
		$installer = new awocouponInstall('install');
		$installer->install_particulars();
	}
 
	function update($parent) {
		$installer = new awocouponInstall('update');
		$installer->install_particulars();
	}
	
	function uninstall($parent) {
		$installer = new awocouponInstall('uninstall');
		$installer->uninstall_injections();
		$installer->uninstall_plugins();
	}
	
	function preflight($type, $parent) {}
	function postflight($type, $parent) {}
}



class awocouponInstall {

	var $version_new = '2.2.3 pro';
	var $version_old = 0;
	var $is_update = false;
	var $is_free_update = false;
	var $upgrade_sql_folder = 'helpers/install/upgrade/sql';
	var $upgrade_php_folder = 'helpers/install/upgrade/php';
	var $logger = array();
	var $is_debug = false;
	var $debug_file = 'awocoupon_install.xml';

	
	function awocouponInstall($type) {
		if($type == 'install' || $type == 'update') {
		
		
			$found_xml_file = '';
			if(file_exists(JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.xml')) $found_xml_file = 'awocoupon.xml';
			elseif(file_exists(JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon_j3.xml')) $found_xml_file = 'awocoupon_j3.xml';
			if(empty($found_xml_file)) echo '<div><b>Database Tables Installation: <font color="green">Successful</font></b></div>';
			else {
		
				$this->is_update = true;

				$contents = file_get_contents(JPATH_ADMINISTRATOR.'/components/com_awocoupon/'.$found_xml_file);
				preg_match('/\<version\>(.*?)\<\/version\>/i',$contents,$matches);
				
				$this->version_old = $matches[1];

				if($this->version_old == $this->version_new) {
					JError::raiseWarning(100, 'You already have this version of AwoCoupon Pro: '.$this->version_new);
					JFactory::getApplication()->redirect('index.php?option=com_installer');
				}
				
				
				if(!empty($this->version_old) && strpos($this->version_old,'pro') === false) {
				
					$db			= JFactory::getDBO();
						
					$config = JFactory::getConfig ();
					$p__ = $config->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'} ( 'dbprefix' );

					$db->setQuery('SHOW TABLES LIKE "'.$p__.'awocoupon_vm"');
					$tmp = $db->loadResult();
					if(!empty($tmp)) {
						$this->is_free_update = true;
						$this->version_old = '1.0.0';
					}
					else $this->is_update = false;

				}
				
				$this->version_new = trim(str_replace('pro','',$this->version_new));
				$this->version_old = trim(str_replace('pro','',$this->version_old));
				
			}

			require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
			//require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/awocoupon.config.php';
		}
		elseif($type=='uninstall') {
			require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
			
		}
		else {
			JError::raiseWarning(100, 'Invalid');
			JFactory::getApplication()->redirect('index.php?option=com_installer');
		}
	}
	
	
	
	function install_particulars() {
		if($this->is_debug) {
		// open file
			file_put_contents(JPATH_ROOT.'/tmp/'.$this->debug_file,'<?xml version="1.0" encoding="utf-8"?>'."\r\n\t".'<installation version="'.$this->version_new.'">'."\r\n");
		}
	
		$this->install_tableinserts();

		$this->install_tableupdates();
				
		$this->install_injections();

		$this->install_plugins();
			
		// Clear Caches
		$cache = JFactory::getCache();
		$cache->clean('com_awocoupon');
		
		
	}
	
	function install_tableinserts() {
		if($this->is_update) return;
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			//$path		= $installer->getPath('manifest');
			//$version	= $installer->getManifest()->version;
			$installer	= JInstaller::getInstance();
			$manifest	= $installer->getManifest();
			$mysql_file = $manifest->install->sql;
		}
		else {
			//$sourcePath	= $installer->getPath('source');
			//$version	= $manifest->document->getElementByPath('version');
			$installer	= JInstaller::getInstance();
			$manifest	= $installer->getManifest();
			$manifest	= $manifest->document;
			$mysql_file = $manifest->getElementByPath('install/sql');
		}
		if(!empty($mysql_file)) return; // if file is already included in the manifest do not re-install sql		
		
		// run install.mysql.sql file
		$db = JFactory::getDBO();
		$sqlfile = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/install/mysql.install.sql';
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);
			if (count($queries) != 0) {
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if (!$db->query()) {
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}
	}


	function install_tableupdates() {
		if(!$this->is_update) return;

		//custom sql file creation
		$upgradeFiles = array();
		if ($handle = opendir(JPATH_ROOT.'/administrator/components/com_awocoupon/'.$this->upgrade_sql_folder)) {
			while (false !== ($file = readdir($handle))) { if ($file != '.' AND $file != '..') $upgradeFiles[] = str_replace(".sql", "", $file); }
			closedir($handle);
		}
		if (empty($upgradeFiles)) {
		}
		natcasesort($upgradeFiles);


		$neededUpgradeFiles = array();
		foreach ($upgradeFiles AS $version) {
			if (version_compare($version, $this->version_old) == 1 AND version_compare($this->version_new, $version) != -1)
			$neededUpgradeFiles[] = $version;
		}

		if (empty($neededUpgradeFiles)) {
		}


		$sqlContentVersion = array();
		if (!empty($neededUpgradeFiles)) {
			foreach($neededUpgradeFiles AS $version) {
				$file = JPATH_ROOT.'/administrator/components/com_awocoupon/'.$this->upgrade_sql_folder.'/'.$version.'.sql';
				if (file_exists($file)) {
					if ($sqlContent = file_get_contents($file)) {
						$sqlContent = trim(preg_replace('/\/\*.*?\@component.*?\*\*\//is','',$sqlContent));
						$sqlContent = preg_split("/;\s*[\r\n]+/", $sqlContent);
						$sqlContentVersion[$version] = $sqlContent;
					}
				}
			}

		}

		$db = JFactory::getDBO();
		foreach ($sqlContentVersion as $version => $sqlContent) {
			foreach ($sqlContent as $query) {
				$query = trim($query);
				if(empty($query)) continue;
					
				if (strpos($query, '/* PHP:') === false) {
					$db->setQuery( $query );
					if(!$db->query()) {
						//Upgrade failed
						// mysql error 1060 is Duplicate column name
						if($db->getErrorNum() != 1060) echo "<font color=red>".$dbupdate['message']." failed! SQL error:" . $db->stderr()."</font><br />";
						
						$this->log('<request result="fail" sqlfile="'.$version.'" >
							<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
							<sqlMsgError><![CDATA['.htmlentities($db->getErrorMsg()).']]></sqlMsgError>
								<sqlNumberError><![CDATA['.htmlentities($db->getErrorNum()).']]></sqlNumberError>
							</request>'."\n");
					}
					else {
						$this->log('<request result="ok" sqlfile="'.$version.'">
								<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
							</request>'."\n");
					}
				}
				else {
				/* If php code have to be executed */
				
					/* Parsing php code */
					$pos = strpos($query, '/* PHP:') + strlen('/* PHP:');
					$phpString = substr($query, $pos, strlen($query) - $pos - strlen(' */;'));
					$php = explode('::', $phpString);
					preg_match('/\((.*)\)/', $phpString, $pattern);
					$paramsString = trim($pattern[0], '()');
					preg_match_all('/([^,]+),? ?/', $paramsString, $parameters);
					if (isset($parameters[1])) $parameters = $parameters[1];
					else $parameters = array();
					if (is_array($parameters)) {
						foreach ($parameters AS &$parameter) $parameter = str_replace('\'', '', $parameter);
					}

					if (strpos($phpString, '::') === false) {
					/* Call a simple function */
						$func_name = trim(str_replace($pattern[0], '', $php[0]),' ;');
						require_once(JPATH_ROOT.'/administrator/components/com_awocoupon/'.$this->upgrade_php_folder.'/'.$func_name.'.php');
						$phpRes = call_user_func_array($func_name, $parameters);
					}
					else {
					/* Or an object method */
						$func_name = array($php[0], str_replace($pattern[0], '', $php[1]));
						$phpRes = call_user_func_array($func_name, $parameters);
					}
					
					
					if ((is_array($phpRes) AND !empty($phpRes['error'])) OR $phpRes === false ) {
						$this->log('<request result="fail" sqlfile="'.$version.'">
							<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
							<phpMsgError><![CDATA['.(empty($phpRes['msg'])?'':$phpRes['msg']).']]></phpMsgError>
							<phpNumberError><![CDATA['.(empty($phpRes['error'])?'':$phpRes['error']).']]></phpNumberError>
						</request>'."\n");
					}
					else {
						$this->log('<request result="ok" sqlfile="'.$version.'">
							<sqlQuery><![CDATA['.htmlentities($query).']]></sqlQuery>
						</request>'."\n");
					}
					
				}
							
							
			}
			echo "<div>Updates for AwoCoupon Pro ".$version.": <font color=green>Applied.</font></div>";			

		}
	}


	function install_injections() {
		if($this->is_update) return;
		
		$estores = $this->getEstores();
		require_once JPATH_SITE.'/administrator/components/com_awocoupon/models/installation.php';
		
		foreach($estores as $estore) {
			if (!class_exists( 'Awocoupon'.$estore.'Helper' )) require JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.$estore.'/helper.php';
			if(call_user_func(array('Awocoupon'.$estore.'Helper','isInstalled'))) {
				$injectclass = new AwoCouponModelInstallation($estore);
				if($injectclass->installALL()) echo '<div><b>'.ucfirst($estore).' Installation: <font color="green">Successful</font></b></div>';
				else echo '<div><b><font color="red">'.ucfirst($estore).' Installation Applied Unsuccessful</font></b></div>
							<div><font color="red">&nbsp; &nbsp; Please review the <a href="'.JRoute::_('index.php?option=com_awocoupon&view=installation').'">Installation Check</a> page for problems</font></div>';
			}
		}

	}

	function uninstall_injections() {	
		$estores = $this->getEstores();
		require_once JPATH_SITE.'/administrator/components/com_awocoupon/models/installation.php';
		
		foreach($estores as $estore) {
			if (!class_exists( 'Awocoupon'.$estore.'Helper' )) require JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.$estore.'/helper.php';
			if(call_user_func(array('Awocoupon'.$estore.'Helper','isInstalled'))) {
					$injectclass = new AwoCouponModelInstallation($estore);
					if($injectclass->uninstallALL()) echo '<div><b>'.ucfirst($estore).' Uninstallation: <font color="green">Successful</font></b></div>';
					else echo '<div><b><font color="red">'.ucfirst($estore).' Uninstallation Applied Unsuccessfully</font></b></div>
								<div><font color="red">&nbsp; &nbsp; Please refer to user_guide.html Troubleshooting section for details on how to remove injections.  As a note, even if the injections are not removed, your redSHOP coupons will function normally.</font></div>';
			}
		}
	}



	function getEstores() {
		$folders = array();
		$dir = JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore';
		$dh  = opendir($dir);
		while (false !== ($name = readdir($dh))) {
			if($name=='.') continue;
			if($name=='..') continue;
			if(!is_dir($dir.'/'.$name)) continue;
			$folders[] = $name;
		}
		return $folders;
	}



	function install_plugins() {
		
		$estores = awoLibrary::getInstalledEstores();
		if(empty($estores)) return;
		
		foreach($estores as $estore) {
			if(awoLibrary::installplugin($estore)) echo '<div><b>'.ucfirst($estore).' Plugin Installation: <font color="green">Successful</font></b></div>';
		}
		
	}
	function uninstall_plugins() {

		$estores = $this->getEstores();
		if(empty($estores)) return;
		
		foreach($estores as $estore) {
			if(awoLibrary::uninstallplugin($estore)) echo '<div><b>'.ucfirst($estore).' Plugin Uninstallation: <font color="green">Successful</font></b></div>';
		}
	}

	function log($data) {
		$this->logger[] = $data;
		if($this->is_debug) file_put_contents(JPATH_ROOT.'/tmp/'.$this->debug_file,$data,FILE_APPEND);
	}
	
}


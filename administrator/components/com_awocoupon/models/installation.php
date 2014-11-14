<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponModelInstallation extends AwoCouponModel {
	
	function __construct($estore='') {
		$this->_type = 'installation';
		parent::__construct();
		if(empty($estore)) $estore = AWOCOUPON_ESTORE;
		if(!file_exists(JPATH_ROOT.'/administrator/components/com_awocoupon/helpers/estore/'.$estore.'/installation.php')) {
			$this->files = array();
			$this->dbupdates = array();
		}
		else {
			$this->installHandler = 'Awocoupon'.$estore.'Installation';
			if (!class_exists( $this->installHandler )) require JPATH_ROOT.'/administrator/components/com_awocoupon/helpers/estore/'.$estore.'/installation.php';
			
			if(call_user_func(array($this->installHandler,'include_installation'))) $this->files = call_user_func(array($this->installHandler,'getFiles'));
			else $this->files = array();
			
			if(call_user_func(array($this->installHandler,'include_installation'))) $this->dbupdates = call_user_func(array($this->installHandler,'getDBUpdates'));
			if(empty($this->dbupdates)) $this->dbupdates = array();

		}

	}
	
	function getCheck() {
		$vars = array();
		$files = array();
		foreach($this->files as $k=>$row) {
			$files[$row['func']]['indexes'][] = $row['index'];
		}
		foreach($files as $func=>$row) {
			//$files[$func]['rtn'] = $this->{$func}('check',$row['indexes']);
			$obj = call_user_func(array($this->installHandler,$func),'check',$row['indexes']);
			$files[$func]['rtn'] = $this->inject_process('check',$obj->file,$obj->vars,$row['indexes']);

		}

		foreach($this->files as $k=>$row) {
			$vars[$k] = array('name'=>$row['name'], 'file'=>$row['file'], 'desc'=>$row['desc'], 'err'=>$files[$row['func']]['rtn'][$row['index']]);
			$vars[$k]['status'] = empty($vars[$k]['err']) ? 'COM_AWOCOUPON_FI_INSTALLED' : 'COM_AWOCOUPON_FI_NOT_INSTALLED';
		}
//printrx($vars);

		foreach($this->dbupdates as $k=>$row) {
			//$files[$func]['rtn'] = $this->{$func}('check',$row['indexes']);
			$rtn = call_user_func(array($this->installHandler,$row['func']),'check');
			$vars[$k] = array(
				'name'=>$row['name'],
				'file'=>'not needed',
				'desc'=>$row['desc'],
				'err'=>$rtn ? '' : '---',
				'status'=>$rtn ? 'COM_AWOCOUPON_FI_INSTALLED' : 'COM_AWOCOUPON_FI_NOT_INSTALLED',
			);
		}

		return $vars;
	}
	

	function uninstall($cids) {
		global  $files;
		
		$return = true;

		$files = array();
		foreach($cids as $file) {
			if(isset($this->files[$file])) {
				$files[$this->files[$file]['func']]['indexes'][] = $this->files[$file]['index'];
			}
		}
		foreach($files as $func=>$row) {
			//$files[$func]['rtn'] = $this->{$func}('reject',$row['indexes']);
			$obj = call_user_func(array($this->installHandler,$func),'reject',$row['indexes']);
			$files[$func]['rtn'] = $this->inject_process('reject',$obj->file,$obj->vars,$row['indexes']);
		}

		foreach($cids as $file) {
			if(isset($this->files[$file])) {
				if(!empty($files[$this->files[$file]['func']]['rtn'])) {
					$return = false;
					JFactory::getApplication()->enqueueMessage(JText::_($this->files[$file]['name']).': '.JText::_($files[$this->files[$file]['func']]['rtn']), 'error');
				}
			}
		}
		
		foreach($cids as $file) {
			if(isset($this->dbupdates[$file])) {
				$return = call_user_func(array($this->installHandler,$this->dbupdates[$file]['func']),'uninstall');
			}
		}
		
		
		return $return;
	}
	function reinstall($cids) {
		global  $files;

		$return = true;
		$files = array();
		foreach($cids as $file) {
			if(isset($this->files[$file])) {
				$files[$this->files[$file]['func']]['indexes'][] = $this->files[$file]['index'];
			}
		}
		foreach($files as $func=>$row) {
			//$files[$func]['rtn'] = $this->{$func}('inject',$row['indexes']);
			$obj = call_user_func(array($this->installHandler,$func),'inject',$row['indexes']);
			$files[$func]['rtn'] = $this->inject_process('inject',$obj->file,$obj->vars,$row['indexes']);
		}
		foreach($cids as $file) {
			if(isset($this->files[$file])) {
				if(!empty($files[$this->files[$file]['func']]['rtn'])) {
					$return = false;
					JFactory::getApplication()->enqueueMessage(JText::_($this->files[$file]['name']).': '.JText::_($files[$this->files[$file]['func']]['rtn']), 'error');
				}
			}
		}
		
		foreach($cids as $file) {
			if(isset($this->dbupdates[$file])) {
				$return = call_user_func(array($this->installHandler,$this->dbupdates[$file]['func']),'install');
			}
		}
		

		return $return;
	}

	
	function installALL() {
		$files = $installfiles = array();
		
		foreach($this->files as $k=>$row) {
			$files[$row['func']]['indexes'][] = $row['index'];
		}
		
		// check all
		foreach($files as $func=>$row) {
			//$files[$func]['rtn'] = $this->{$func}('check',$row['indexes']);
			$obj = call_user_func(array($this->installHandler,$func),'check',$row['indexes']);
			$files[$func]['rtn'] = $this->inject_process('check',$obj->file,$obj->vars,$row['indexes']);
		}
		
		// find uninstalled
		foreach($this->files as $k=>$row) {
			if(!empty($files[$row['func']]['rtn'][$row['index']])) $installfiles[$row['func']]['indexes'][] = $row['index'];
		}
		
		// install uninstalled
		foreach($installfiles as $func=>$row) {
			//$installfiles[$func]['rtn'] = $this->{$func}('inject',$row['indexes'],true);
			$obj = call_user_func(array($this->installHandler,$func),'inject',$row['indexes']);
			$installfiles[$func]['rtn'] = $this->inject_process('inject',$obj->file,$obj->vars,$row['indexes']);
		}
		// check for errors
		foreach($this->files as $k=>$row) {
			if(!empty($installfiles[$row['func']]['rtn'][$row['index']])) return false;
		}

		foreach($this->dbupdates as $row) {
			call_user_func(array($this->installHandler,$row['func']),'install');
		}
		
		
		return true;
	}
	function uninstallALL() {
		$files = array();

		foreach($this->files as $k=>$row) {
			$files[$row['func']]['indexes'][] = $row['index'];
		}
		// uninstall all
		foreach($files as $func=>$row) {
			//$files[$func]['rtn'] = $this->{$func}('reject',$row['indexes']);
			$obj = call_user_func(array($this->installHandler,$func),'reject',$row['indexes']);
			$files[$func]['rtn'] = $this->inject_process('reject',$obj->file,$obj->vars,$row['indexes']);
		}
		
		// check for errors
		foreach($this->files as $k=>$row) {
			if(!empty($files[$row['func']]['rtn'][$row['index']])) return false;
		}

		foreach($this->dbupdates as $row) {
			call_user_func(array($this->installHandler,$row['func']),'uninstall');
		}
		
		return true;
	}
	
	

	function inject_process($type, $file, $vars, $indexes, $is_backup=false) {
		if(empty($type) || ($type!='inject' && $type!='reject' && $type!='check')) return 'COM_AWOCOUPON_FI_INVALID_FILE_TYPE';
		if (!file_exists($file) || !is_writable($file)) return 'COM_AWOCOUPON_FI_NO_EXIST_WRITEABLE';

		$content = file_get_contents($file);
		$patterns = array();
		$replacements = array();
		foreach($indexes as $index) {
			if(!empty($vars['patterns'][$index]) && isset($vars['replacements'][$index])) {
				$patterns[] = $vars['patterns'][$index];
				$replacements[] = $vars['replacements'][$index];
			}
		}
//printr($patterns);printrx($replacements);
		if($type == 'check') {
			$rtn = array_fill_keys($indexes,-1);
			if(empty($vars['patterntype']) || $vars['patterntype']=='regex') {
				if(is_array($vars['patterns'])) {
					foreach($vars['patterns'] as $k=>$pattern) {
						if(in_array($k,$indexes)) {
							$rtn[$k] = !preg_match($pattern, $content) ? 'COM_AWOCOUPON_FI_NO_MATCH' : '';
						}
					}
				}
			} elseif($vars['patterntype']=='str') {
				if(is_array($vars['patterns'])) {
					foreach($vars['patterns'] as $pattern) {
						if(in_array($k,$indexes)) {
							$rtn[$k] = strpos($content,$pattern)===false ? 'COM_AWOCOUPON_FI_NO_MATCH' : '';
						}
					}
				}
			} else return 'COM_AWOCOUPON_FI_INVALID_PATTERN_TYPE';
			
			return $rtn;

			
		} 
		else {
			if(!empty($patterns)) {
				if($is_backup) {
					$file_bak = substr($file,0,-4).'.awobak.php';
					copy ( $file , $file_bak );
				}
				$count = 0;
				$countcheck = count($patterns);
				if(empty($vars['patterntype']) || $vars['patterntype']=='regex')
					$new_content = preg_replace($patterns, $replacements, $content, -1, $count);
				elseif($vars['patterntype']=='str') 
					$new_content = str_replace($patterns, $replacements, $content,$count);
				else return 'COM_AWOCOUPON_FI_INVALID_PATTERN_TYPE';
//exit('<textarea cols="120" rows="35">'.$new_content.'</textarea> '.$count.' '.$countcheck);
				if($count < $countcheck) return 'COM_AWOCOUPON_FI_NO_POSITION';
			
				if (!@$handle = fopen($file, 'w')) return 'COM_AWOCOUPON_FI_NO_OPENFILE';

				// Write $somecontent to our opened file.
				if (fwrite($handle, $new_content) === FALSE) return 'COM_AWOCOUPON_FI_NO_WRITEABLE';
			
				fclose($handle);
			}
			return;
		}
	}


}

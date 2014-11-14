<?php
/**
 * @component AwoCoupon
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


if(version_compare( JVERSION, '3.0.0', 'ge' )) {
	class AwoCouponView extends JViewLegacy {
	
		function display($tpl=null) {
			parent::display($tpl);
			$this->display_afterload();
		}
		
		function display_beforeload() {
			if(JFactory::getApplication()->isAdmin()) {
				JHtml::_('bootstrap.framework');
				$document	= JFactory::getDocument();
				//$document->addStyleSheet(JURI::root(true).'/administrator/components/com_awocoupon/assets/css/style.css?219');
				$document->addScript(JURI::root(true).'/administrator/components/com_awocoupon/assets/js/awocoupon.js?219');
				
				$undesirables = array('jquery.ui.core.js','jquery.ui.core.min.js','jquery.ui.sortable.js','jquery.ui.sortable.min.js');
				foreach($document->_scripts as $key=>$script) {
					$basename = strtolower(basename($key));
					if(in_array($basename,$undesirables)) unset($document->_scripts[$key]);
				}
				
				
				$t1	= JRequest::getVar('tmpl','');
				$t2	= JRequest::getVar('no_html','');
				$t3	= JRequest::getVar('format','');
				
				//echo "t1: $t1 t2: $t2 t3: $t3";
				
				$test = false;
				if( (empty($t1) || $t1=='index')
				&& empty($t2)
				&& (empty($t3) || $t3!='raw')) {
					//require_once JPATH_COMPONENT_ADMINISTRATOR.'/toolbar.awocoupon.php';
					
					if(!class_exists('AwoCouponMenu')) require JPATH_ROOT.'/administrator/components/com_awocoupon/helpers/menu.php';
					$menu = new AwoCouponMenu();
					$html = $menu->process();
					echo $html.'<br />';
				}

				echo '<div class="m">';
			}
			
		}
	
		function display_aftertitle() {}
		
		function display_afterload() {
			if(JFactory::getApplication()->isAdmin()) {
				echo '</div>';
			}
		}
	}
}
else {

	jimport( 'joomla.application.component.view');

	class AwoCouponView extends JView {
	
		function display($tpl=null) {
			parent::display($tpl);
			$this->display_afterload();
		}
		
		function display_beforeload() {
			if(JFactory::getApplication()->isAdmin()) {
				if(!class_exists('AwoCouponMenu')) require JPATH_ROOT.'/administrator/components/com_awocoupon/helpers/menu.php';
				AwoCouponMenu::preload();

				$asset_path = JURI::root(true).'/administrator/components/com_awocoupon/assets';
				$document	= JFactory::getDocument();
				//$document->addStyleSheet($asset_path.'/css/style.css?219');
				//$document->addScript($asset_path.'/js/jquery.min.js?219');
				$document->addScript($asset_path.'/js/awocoupon.js?219');
			}
		}
		function display_aftertitle() {
			if(JFactory::getApplication()->isAdmin()) {
				$title = '';
				if (isset(JFactory::getApplication()->JComponentTitle)) $title = JFactory::getApplication()->JComponentTitle;
				
				if(!class_exists('AwoCouponMenu')) require JPATH_ROOT.'/administrator/components/com_awocoupon/helpers/menu.php';
				$menu = new AwoCouponMenu();
				$html = $menu->process();
				
				JFactory::getApplication()->JComponentTitle = '<div>'.$title.'</div><div>'.$html.'</div>';
			}
		}
		function display_afterload() {}
	
	}
}
<?php
/**
 * @component AwoCoupon
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


class AwoCouponMenu {

	static function preload() {
		$jlang = JFactory::getLanguage();
		$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_awocoupon', JPATH_ADMINISTRATOR, null, true);
		
		$document	= JFactory::getDocument();
		$asset_path = JURI::root(true).'/administrator/components/com_awocoupon/assets';
		$document->addStyleSheet($asset_path.'/css/style.css');
		$document->addStyleSheet($asset_path.'/css/menu.css?219');
		$document->addCustomTag('<!--[if IE 7]><link rel="stylesheet" href="'.$asset_path.'/css/ie7.css" type="text/css" media="all" /><![endif]-->');
		if(version_compare( JVERSION, '3.0.0', 'ge' )); 
		else {
			$media_path = JURI::root(true).'/media/com_awocoupon';
			$document->addScript($media_path.'/js/jquery.min.js?219');
			$document->addScript($media_path.'/js/bootstrap.min.js?219');
		}
	}
	
	function process() {
		$this->define_menu();
		$this->define_plugin_menu();
		return $this->print_menu();
	}
	
	
	function define_menu() {

		self::preload();
		
		$document	= JFactory::getDocument();
		$document->addScriptDeclaration('
			jQuery(document).ready(function () {
				jQuery("#awomenu .dropdown-toggle").dropdown();
				jQuery("#awomenu ul.nav li.dropdown").hover(
					function() {
						jQuery(this).find(".dropdown-menu").stop(true, true).show();
						jQuery(this).addClass("active");
					}, 
					function() {
						jQuery(this).find(".dropdown-menu").stop(true, true).hide();
						jQuery(this).removeClass("active");
					}
				);
			})
		');
		
		$include_installation = false;
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/installation.php')) {
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/installation.php';
			$include_installation = call_user_func(array('Awocoupon'.AWOCOUPON_ESTORE.'Installation','include_installation'));
		}
		
		$img_path = JURI::root(true).'/administrator/components/com_awocoupon/assets/images';
		$this->menu_items = array(
			array('COM_AWOCOUPON','index.php?option=com_awocoupon&view=dashboard',$img_path.'/awocoupon-small.png',array(
						array('COM_AWOCOUPON_DH_DASHBOARD','index.php?option=com_awocoupon&view=dashboard',$img_path.'/icon-16-home.png'),
						array('COM_AWOCOUPON_CFG_CONFIGURATION','index.php?option=com_awocoupon&view=config',$img_path.'/icon-16-config.png'),
						$include_installation ? array('COM_AWOCOUPON_FI_INSTALLATION_CHECK','index.php?option=com_awocoupon&view=installation',$img_path.'/icon-16-installation.png') : array(),
						array('COM_AWOCOUPON_LI_LICENSE','index.php?option=com_awocoupon&view=license',$img_path.'/icon-16-license.png'),
						array('COM_AWOCOUPON_AT_ABOUT','index.php?option=com_awocoupon&view=about',$img_path.'/icon-16-info.png'),
					),
				),
			array('COM_AWOCOUPON_CP_COUPONS','index.php?option=com_awocoupon&view=coupons',$img_path.'/icon-16-coupons.png',array(
						array('COM_AWOCOUPON_DH_COUPON_NEW','index.php?option=com_awocoupon&view=coupon',$img_path.'/icon-16-new.png'),
						array('COM_AWOCOUPON_CP_COUPONS','index.php?option=com_awocoupon&view=coupons',$img_path.'/icon-16-list.png'),
						array('COM_AWOCOUPON_CP_AUTO_DISCOUNT','index.php?option=com_awocoupon&view=couponsauto',$img_path.'/icon-16-auto.png'),
						array('COM_AWOCOUPON_CP_GENERATE_COUPONS','index.php?option=com_awocoupon&view=coupon&layout=generate',$img_path.'/icon-16-copy.png'),
						array('COM_AWOCOUPON_IMP_IMPORT','index.php?option=com_awocoupon&view=import',$img_path.'/icon-16-import.png'),
					),
				),
			array('COM_AWOCOUPON_CP_HISTORY_USES','index.php?option=com_awocoupon&view=history',$img_path.'/icon-16-history.png',array(
						array('COM_AWOCOUPON_CP_COUPONS','index.php?option=com_awocoupon&view=history',$img_path.'/icon-16-coupons.png'),
						array('COM_AWOCOUPON_GC_GIFTCERTS','index.php?option=com_awocoupon&view=history&layout=gift',$img_path.'/icon-16-giftcert.png'),
						array('COM_AWOCOUPON_GBL_ORDERS','index.php?option=com_awocoupon&view=history&layout=order',$img_path.'/icon-16-cart.png'),
					),
				),
			array('COM_AWOCOUPON_GBL_TOOLS','',$img_path.'/icon-16-tools.png',array(
						array('COM_AWOCOUPON_GC_PRODUCT_NEW','index.php?option=com_awocoupon&view=giftcert&layout=edit',$img_path.'/icon-16-new.png'),
						array('COM_AWOCOUPON_GC_PRODUCTS','index.php?option=com_awocoupon&view=giftcert',$img_path.'/icon-16-giftcert.png'),
						array('COM_AWOCOUPON_GC_CODES','index.php?option=com_awocoupon&view=giftcertcode',$img_path.'/icon-16-import.png'),
						array('--separator--'),
						array('COM_AWOCOUPON_PF_PROFILE_NEW','index.php?option=com_awocoupon&view=profile&layout=edit',$img_path.'/icon-16-new.png'),
						array('COM_AWOCOUPON_PF_PROFILES','index.php?option=com_awocoupon&view=profile',$img_path.'/icon-16-profile.png'),
						array('--separator--'),
						array('COM_AWOCOUPON_RPT_REPORTS','index.php?option=com_awocoupon&view=reports',$img_path.'/icon-16-report.png'),
					),
				),
		);
	}
	
	function define_plugin_menu() {
		$files = array(
			'aworewardsmenu'=>JPATH_ROOT.'/administrator/components/com_aworewards/helpers/menu.php',
			'awoaffiliatemenu'=>JPATH_ROOT.'/administrator/components/com_awoaffiliate/helpers/menu.php',
			'aworeferralmenu'=>JPATH_ROOT.'/administrator/components/com_aworeferral/helpers/menu.php',
			'awotrackermenu'=>JPATH_ROOT.'/administrator/components/com_awotracker/helpers/menu.php',
		);
		foreach($files as $class=>$file) {
			if(file_exists($file)) {
				require_once $file;
				$this->menu_items[] = call_user_func(array($class, 'define_menu'));
			}
		}
	}

	function print_menu() {
	
		// get all the urls into an array
		$menu_urls = array();
		foreach($this->menu_items as $item) {
			if(!empty($item[1])) $menu_urls[] = $item[1];
			if(!empty($item[3]) && is_array($item[3])) {
				foreach($item[3] as $item2) {
					if(!empty($item2[1])) $menu_urls[] = $item2[1];
					if(!empty($item2[3]) && is_array($item2[3])) {
						foreach($item2[3] as $item3) {
							if(!empty($item3[1])) $menu_urls[] = $item3[1];
						}
					}
				}
			}
		}
		
	
		// set current url
		$uri = &JURI::getInstance();
		$current_url = 'index.php'.$uri->toString(array('query'));
		if(!in_array($current_url,$menu_urls)){
			$view = JRequest::getVar('view');
			$layout = JRequest::getVar('layout');
			$current_url = 'index.php?option='.JRequest::getVar('option');
			if(empty($view)) $current_url .= '&view=dashboard';
			else {
				$current_url .= '&view='.$view;
				if(!empty($layout)) {
					if(in_array($current_url.'&layout='.$layout,$menu_urls)) $current_url .= '&layout='.$layout;
				}
			}
		}
		
		
		if (!class_exists( AWOCOUPON_ESTOREHELPER)) require JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		$store = '<a href="'.call_user_func(array(AWOCOUPON_ESTOREHELPER,'getAppLink')).'" class="store topmenu_'.AWOCOUPON_ESTORE.'">&nbsp;</a>';
		
		// process
		$html_menu = '
			<div id="awomenu">
				<div id="awomenu_container">
					<div class="navbar">
						<div class="navbar-inner">
							<ul id="" class="nav" >
								<li>'.$store.'</li>
		';	
						
						
		foreach($this->menu_items as $item) {
			if(empty($item)) continue; 
			$is_active_1 = false;
			$html_menu_2 = '';
			if(!empty($item[3]) && is_array($item[3])) {
				$html_menu_2 = '<ul class="dropdown-menu">';
				foreach($item[3] as $item2) {
					if(empty($item2)) continue;
					if(!empty($item2[1]) && $current_url==$item2[1]) $is_active_1 = true;
					$is_active_2 = false;
					$html_menu_3 = '';
					if(!empty($item2[3]) && is_array($item2[3])) {
						$html_menu_3 = '<ul>';
						foreach($item2[3] as $item3) {
							if(empty($item3)) continue; 
							if(!empty($item3[1]) && $current_url==$item3[1]) $is_active_2 = true;
							$html_menu_3 .= $this->print_menu_helper($item3,3,$current_url).'</li>';
						}
						$html_menu_3 .= '</ul>';
					}
					$html_menu_2 .= $this->print_menu_helper($item2,2,$current_url,$is_active_2).$html_menu_3.'</li>';
				}
				$html_menu_2 .= '</ul>';
			}
			$html_menu .= $this->print_menu_helper($item,1,$current_url,$is_active_1).$html_menu_2.'</li>';
		}
		$html_menu .= '</ul></div></div></div></div><div class="clr"></div>';
		
		return $html_menu;
	
	}

	function print_menu_helper($item,$level,$current_url,$force_active=false) {
		$html = '';
		$image = '';
		$a_class = '';
		if(!empty($item[2])) {
			if(substr($item[2],0,6)=='class:') $a_class = substr($item[2],6);
			else $image = '<img src="'.$item[2].'" class="tmb"/>';
		}
		else $image = '<div style="display:inline-block;width:16px;">&nbsp;</div>';
		
		$active_css = $force_active || (!empty($item[1]) && $current_url==$item[1]) ? 'current' : '';
		
		$html .= '<li class="';
		if($level==1) $html .= ' dropdown ';
		else {
			if($item[0]=='--separator--') $html .= ' divider ';
			else ;
		}
		$html .= $active_css;
		$html .= '">';
		
		if($item[0]!='--separator--') {
			$html .= '<a class="';
			//if($level==1) $html .= ' dropdown-toggle ';
			$html .= '" ';
			//if($level==1) $html .= 'data-toggle="dropdown"';
			$html .= ' href="'.(!empty($item[1]) ? $item[1] : '#').'"';
			$html .= '>'.$image.' '.JText::_($item[0]).'</a>';
		}
		else $html .= '<span></span>';
		return $html;
	
	}
}

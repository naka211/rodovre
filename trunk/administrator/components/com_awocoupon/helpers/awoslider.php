<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

if(version_compare(JVERSION,'1.6.0','ge')) {

	class awoJHtmlSliders {
		public static function start($group = 'sliders', $params = array()) {
			self::_loadBehavior($group,$params);
			return '<div id="'.$group.'" class="pane-sliders"><div style="display:none;"><div>';
		}
		public static function end() { return '</div></div></div>'; }
		public static function panel($text, $id) { return '</div></div><div class="panel"><h3 class="pane-toggler-down title" id="'.$id.'"><a href="javascript:void(0);"><span>'.$text.'</span></a></h3><div class="pane-slider content" style="height:auto;">'; }
		protected static function _loadBehavior($group, $params = array()) {
			$js  = 'var $j = jQuery.noConflict();
					jQuery(document).ready(function() {'.(
						isset($params['closeAll']) && $params['closeAll']==1 
								? 'jQuery("#'.$group.' .panel h3").removeClass("pane-toggler-down").addClass("pane-toggler");
									jQuery("#'.$group.' .panel div.pane-slider").hide();'
								: '').'
						jQuery("#'.$group.' .panel h3").click(function() {
							if(jQuery(this).parent().find(".pane-slider").is(":visible")) {
								jQuery(this).removeClass("pane-toggler-down").addClass("pane-toggler");
								jQuery(this).parent().find(".pane-slider").hide();
							}
							else {
								jQuery(this).removeClass("pane-toggler").addClass("pane-toggler-down");
								jQuery(this).parent().find(".pane-slider").show().css({"height":"auto"});
							}		
							
						});
					});	
					';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
		}
	}

} else {

	class awoJHtmlSliders {
		function start( $id, $params ) {
			$js  = 'var $j = jQuery.noConflict();
					jQuery(document).ready(function() {'.(
						isset($params['closeAll']) && $params['closeAll']==1 
								? 'jQuery("#'.$id.' .panel h3").removeClass("jpane-toggler-down").addClass("jpane-toggler");
									jQuery("#'.$id.' .panel div.mypane-slider").hide();'
								: '').'
						jQuery("#'.$id.' .panel h3").click(function() {
							if(jQuery(this).parent().find(".mypane-slider").is(":visible")) {
								jQuery(this).removeClass("jpane-toggler-down").addClass("jpane-toggler");
								jQuery(this).parent().find(".mypane-slider").hide();
							}
							else {
								jQuery(this).removeClass("jpane-toggler").addClass("jpane-toggler-down");
								jQuery(this).parent().find(".mypane-slider").show().css({"height":"auto"});
							}		
							
						});
					});	
					';
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
			return '<div id="'.$id.'" class="pane-sliders"><div style="display:none;"><div>';
		}
		function end() { return '</div></div></div>'; }
		function panel( $text, $id ) {
			return '</div></div><div class="panel">'
				.'<h3 class="jpane-toggler-down title" id="'.$id.'"><span>'.$text.'</span></h3>'
				.'<div class="mypane-slider content" style="height:auto;">';
		}
		function _loadBehavior($params = array()) { return;}
	}

}

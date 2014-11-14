<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

function AwoCouponBuildRoute(&$query) {
	$segments = array();

	if(isset($query['view'])) 	{
		if(empty($query['Itemid'])) {
			$segments[] = $query['view'];
		} else {
			$menu = &JSite::getMenu();
			$menuItem = &$menu->getItem( $query['Itemid'] );
			if(!isset($menuItem->query['view']) || $menuItem->query['view'] != $query['view']) {
				$segments[] = $query['view'];
			}
		}
		unset($query['view']);
	}
	return $segments;
}


function AwoCouponParseRoute($segments){
	$vars = array();

	$count = count($segments);
	if(!empty($count)) {
		$vars['view'] = $segments[0];
	}

	if($count > 1) {
		$vars['id']    = $segments[$count - 1];
	}

	return $vars;
}

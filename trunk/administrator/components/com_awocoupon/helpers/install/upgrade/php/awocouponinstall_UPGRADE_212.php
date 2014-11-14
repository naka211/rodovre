<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

function awocouponinstall_UPGRADE_212() {
	$db			= JFactory::getDBO();
	$elem_id = 1;
	
	$db->setQuery('SELECT id,email_subject,email_body FROM #__awocoupon_profile');
	$tmp = $db->loadObjectList();
	if(!empty($tmp)) {
		foreach($tmp as $row) {
			if(!empty($row->email_subject)) {
				$db->setQuery('INSERT INTO #__awocoupon_lang_text (elem_id,lang,text) VALUES ('.$elem_id.',"en-GB","'.awoLibrary::dbEscape($row->email_subject).'")');
				$db->query();
				$db->setQuery('UPDATE #__awocoupon_profile SET email_subject_lang_id='.$elem_id.' WHERE id='.$row->id);
				$db->query();
				$elem_id++;
			}
			if(!empty($row->email_body)) {
				$db->setQuery('INSERT INTO #__awocoupon_lang_text (elem_id,lang,text) VALUES ('.$elem_id.',"en-GB","'.awoLibrary::dbEscape($row->email_body).'")');
				$db->query();
				$db->setQuery('UPDATE #__awocoupon_profile SET email_body_lang_id='.$elem_id.' WHERE id='.$row->id);
				$db->query();
				$elem_id++;
			}
		}
	}
}
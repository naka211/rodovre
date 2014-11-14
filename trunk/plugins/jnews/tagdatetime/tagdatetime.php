<?php
defined('_JEXEC') OR die('Access Denied!');
### Copyright (c) 2006-2013 Joobi Limited. All rights reserved.
### license GNU GPLv3 , link http://www.joobi.co
$jNewsMainFrame = JFactory::getApplication();
$jNewsMainFrame->registerEvent( 'jnewsbot_tagdatetime', 'tagdatetime' );
$jNewsMainFrame->registerEvent( 'jnewsbot_transformall','tagdatetime_transform');

/**
 * <p>Function to insert a date tag<p>
 */
function tagdatetime() {
	
	$js = 'function insertjnewstag(tag){ ';
	if( version_compare(JVERSION,'1.6.0','<') ){//1.5
		$js .= ' if(window.top.insertTag(tag)){window.top.document.getElementById(\'sbox-window\').close();}';
	}else if(version_compare(JVERSION,'3.0.0','<')){
		$js .= ' if(window.top.insertTag(tag)) {window.parent.SqueezeBox.close();}';
	}else {$js .= ' if(window.top.insertTag(tag)) {           
                     var need_click = jQuery(window.top.document).find("div.modal-backdrop");
                    if(need_click.length == 0) window.parent.SqueezeBox.close();
                    else    jQuery(window.top.document).find("div.modal-backdrop").click();}';
        }
	
	$js .= '}';
	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration($js);
		
	echo '<style type="text/css">table.'. jnews::myTheme() . 'tr:hover {cursor: pointer;}</style>';
	
	?>

<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
	<table class="<?php echo jnews::myTheme(); ?>">
			<tbody>
				<thead>
					<tr>
						<th class="title"><center><?php echo _JNEWS_MAILING_TAG; ?></center></th>
						<th class="title"><center><?php echo _JNEWS_TEMPLATE_DESC; ?></center></th>
					</tr>
				</thead>
				<tr class="row0" onclick="insertjnewstag('{tag:date}')">
					<td>
						<strong><?php echo '{tag:date}'; ?></strong>
					</td>
					<td>
						<?php
						if( version_compare( JVERSION,'3.0.0','<' ) ) {
							$date = JHTML::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC'), JNEWS_TIME_OFFSET);
						} else {
							$date = JHtml::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC'), JNEWS_TIME_OFFSET);
						}							
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('{tag:date format=1}')">
					<td>
						<strong><?php echo '{tag:date format=1}'; ?></strong>
					</td>
					<td>
						<?php
						if( version_compare( JVERSION,'3.0.0','<' ) ) {
							$date = JHTML::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC1'), JNEWS_TIME_OFFSET);
						} else {
							$date = JHtml::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC1'), JNEWS_TIME_OFFSET);
						}								
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('{tag:date format=2}')">
					<td>
						<strong><?php echo '{tag:date format=2}'; ?></strong>
					</td>
					<td>
						<?php
						if( version_compare( JVERSION,'3.0.0','<' ) ) {
							$date = JHTML::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC2'), JNEWS_TIME_OFFSET);
						} else {
							$date = JHtml::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC2'), JNEWS_TIME_OFFSET);
						}							
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('{tag:date format=3}')">
					<td>
						<strong><?php echo '{tag:date format=3}'; ?></strong>
					</td>
					<td>
						<?php
						if( version_compare( JVERSION,'3.0.0','<' ) ) {
							$date = JHTML::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC3'), JNEWS_TIME_OFFSET);
						} else {
							$date = JHtml::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC3'), JNEWS_TIME_OFFSET);
						}							
						echo $date;
						?>
					</td>
				</tr>
				<tr class="row0" onclick="insertjnewstag('{tag:date format=4}')">
					<td>
						<strong><?php echo '{tag:date format=4}'; ?></strong>
					</td>
					<td>
						<?php
						if( version_compare( JVERSION,'3.0.0','<' ) ) {
							$date = JHTML::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC4'), JNEWS_TIME_OFFSET);
						} else {
							$date = JHtml::_( 'date',jnews::getNow(), JText::_('DATE_FORMAT_LC4'), JNEWS_TIME_OFFSET);
						}							
						echo $date;
						?>
					</td>
				</tr>
			</tbody>
		</table>

	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
<?php

}

function tagdatetime_transform( &$html, &$text, &$subject, $queueInfo=null ) {

	//we store the replacement of the date/time tag
	$replace = array();

//	$pattern = '#{date:?([^:]*)}#Ui';
	//sample tag = {tag:date format=1}
	$pattern = '#{tag:date ?(.*)}#Ui';

	preg_match_all($pattern, $html.$text.$subject, $tags);
	$time = time();
	if(!empty($tags[0])){
		if( ! empty($queueInfo) && ! empty($queueInfo->mailingid) && is_numeric($queueInfo->mailingid))
		{
			$db = JFactory::getDBO();		
			$query = 'SELECT send_date FROM `#__jnews_mailings` WHERE `id` = ' . $queueInfo->mailingid;		
			$db->setQuery($query);
			$send_date = $db->loadObject();
			if(is_object($send_date) && !empty($send_date->send_date))
			{
				$time = $send_date->send_date;
			}		
		}

		foreach ($tags[0] as $key => $tag){
			$format = (!empty($tags[1][$key])) ? substr($tags[1][$key], 8 ) : '';
			
			if( version_compare( JVERSION,'3.0.0','<' ) ) {
				$replace[$tag] = JHTML::_( 'date', $time, JText::_('DATE_FORMAT_LC'.$format ) );
			} else {
				$replace[$tag] = JHtml::_( 'date', $time, JText::_('DATE_FORMAT_LC'.$format ) );
			}			
			
		}

	}

	if ( !empty($replace) ) {
		$html = str_replace(array_keys($replace),$replace,$html);
		$text = str_replace(array_keys($replace),$replace,$text);
		$subject = str_replace(array_keys($replace),$replace,$subject);
	}

}//endfunc

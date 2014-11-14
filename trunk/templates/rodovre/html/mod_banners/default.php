<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//Detect mobile
$config =& JFactory::getConfig();
$showPhone = $config->getValue( 'config.show_phone' );
$enablePhone = $config->getValue( 'config.enable_phone' );
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
if ( ($showPhone || $detect->isMobile()) && ($enablePhone) ) {
    include('default_mobile.php');
    return;
}
//Detect mobile end

//require_once JPATH_ROOT . '/components/com_banners/helpers/banner.php';
$baseurl = JURI::base();
?>
<div class="banner">
    <div class="html_carousel">
        <div class="shawdow-banner"></div>
        <div id="foo1">
            <?php foreach($list as $item){
                $imageurl = $item->params->get('imageurl');
            ?>
            <div class="slide"> <a href="<?php echo $item->clickurl;?>"><img src="<?php echo $baseurl . $imageurl;?>" alt="" /></a>
                <div class="title">
                    <h3><?php echo $item->name;?></h3>
                    <p><?php echo $item->description;?></p>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="clearfix"></div>
        <div class="pagination" id="block1_pag"></div>
    </div>
</div>

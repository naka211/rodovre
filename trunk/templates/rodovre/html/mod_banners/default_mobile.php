<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_banners
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//require_once JPATH_ROOT . '/components/com_banners/helpers/banner.php';
$baseurl = JURI::base();
?>

<div class="eachBox banner-box clearfix">
    <div id="banner" class="clearfix">
        <div class="camera_wrap camera_azure_skin" id="camera_wrap_1">
            <?php foreach($list as $item){
                $imageurl = $item->params->get('imageurl');
            ?>
            <div data-src="<?php echo $baseurl . $imageurl;?>"> </div>
            <?php } ?>
        </div>
    </div>
</div>
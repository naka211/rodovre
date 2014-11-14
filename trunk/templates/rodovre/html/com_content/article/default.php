<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
//$params		= $this->item->params;
$images = json_decode($this->item->images);
//$urls = json_decode($this->item->urls);
//$canEdit	= $this->item->params->get('access-edit');
//$user		= JFactory::getUser();

?>
<div class="template">
    <div class="about_page">
        {module Breadcrumbs}
        <h2><?php echo $this->escape($this->item->title); ?></h2>
        <?php echo $this->item->text; ?>
    </div>
</div>

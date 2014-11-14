<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
//$params		= $this->item->params;
$images = json_decode($this->item->images);
//$urls = json_decode($this->item->urls);
//$canEdit	= $this->item->params->get('access-edit');
//$user		= JFactory::getUser();

?>
<div class="w-content undepages" id="content">
  <!--<ul class="eachBox breadcrumb">
    <li><a href="index.php">Forside</a></li>
    <li><a href="om-os.php">Om Scheel-Larsen</a></li>
  </ul>-->
  {module Breadcrumbs}
  <div class="eachBox about_page">
    <h2><?php echo $this->escape($this->item->title); ?></h2>
    <?php echo $this->item->text; ?>
  </div>
  <!--eachBox-->  
</div>

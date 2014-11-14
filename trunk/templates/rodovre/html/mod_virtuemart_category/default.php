<?php // no direct access
defined('_JEXEC') or die('Restricted access');
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
?>
<span id="add_menu" style="display:none;">
	<div class="sub clearfix">
<?php foreach($categories as $category){
    $caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
?>
    <div class="sub_col">
		<h4><a href="<?php echo $caturl;?>">• <?php echo $category->category_name;?></a></h4>
        <ul>
<?php	foreach($category->childs as $child){
		  $caturl1 = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$child->virtuemart_category_id);
		?>
	    <li><a href="<?php echo $caturl1;?>"><?php echo $child->category_name;?></a></li>
    <?php }?>
	    </ul>
    </div>
<?php
	}?>
	</div>
</span>
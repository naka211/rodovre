<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();
$tmpl = JURI::base().'templates/'.$app->getTemplate()."/";
if (!empty($this->product->images)) {
	$image = $this->product->images[0];
	?>

<?php 
$db = JFactory::getDBO();
$db->setQuery("SELECT virtuemart_media_id FROM #__virtuemart_manufacturer_medias WHERE virtuemart_manufacturer_id = ".$this->product->virtuemart_manufacturer_id);
$vmi = $db->loadResult();

if($vmi){
	$db->setQuery("SELECT file_url FROM #__virtuemart_medias WHERE virtuemart_media_id = ".$vmi);
	$file_url = $db->loadResult();
}

if(!$file_url){
	$file_url = JURI::base()."components/com_virtuemart/assets/images/vmgeneral/noimage.gif";
}
?>
	<?php if($this->product->virtuemart_category_id != 71){ ?>
	<h6 class="w_brand">
		<img src="<?php echo JURI::base().'thumbnail/timthumb.php?src='.$file_url.'&q=100&h=31'; ?>" />
	</h6>
	<?php }?>
    <div class="img_larg">
	<a class="imgZoom" id="btnLargeImage" href="<?php echo $image->file_url?>">
	<?php
		echo $image->displayMediaFull('width="430"',false,'');
	?>
	</a>
    </div>
    <a id="btnZoomIcon" class="imgZoom btnZoom" href="<?php echo $image->file_url?>"><img src="<?php echo $tmpl; ?>img/icon_zoom.png" alt=""></a>
<?php
	// Showing The Additional Images
	$count_images = count ($this->product->images);
		?>
<!--	<div class="list-item">
	<ul id="thumblist" class="gallery">
		<?php
		/*for ($i = 0; $i < $count_images; $i++) {
			$image = $this->product->images[$i];
			if($i==$count_images-1):
			?>
			<li>
			<?php else:?>
			<li>
			<?php endif;?>
			<a href="#">
            	<img width="102" alt="converse_all-stars" src="<?php echo $image->file_url;?>">
			<?php
				//echo $image->displayMediaThumb('width="102"',false,'');
			?>
			</a></li>
			<?php
		}*/
		?>
		<div class="clear"></div>
	</ul>
	</div>-->
<?php
}
?>
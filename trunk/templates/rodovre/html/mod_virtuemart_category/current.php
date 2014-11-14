<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$ID = str_replace('.', '_', substr(microtime(true), -8, 8));
function subrender($categories, $level=1, $chaindata=array()){
	?>
	<ul>
	<?php
		foreach ($categories as $category){
			$parent_mark='';
			$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
			$cattext = $category->category_name;

			if($chaindata[3]==$category->virtuemart_category_id):
				echo '<li class="active">';
			else:
				if(in_array( $category->virtuemart_category_id, $chaindata[4]))
					$parent_mark = ' class="active"';
				?>
				<li<?php echo $parent_mark?>>
				<?php
			endif;

			echo JHTML::link($caturl, $cattext);
			/*$category->childs = $chaindata[1]->call( array( 'VirtueMartModelCategory', 'getChildCategoryList' ),$chaindata[2], $category->virtuemart_category_id );
			if($category->childs)
				subrender($category->childs, $level+1, $chaindata);*/
				?>
			</li>
			<?php
		}
		?>
	</ul>
<?php
}
?>
<div class="category clearfix">
<h2>Produkter</h2>
<ul class="VMmenu<?php echo $class_sfx ?> cate" id="<?php echo "VMmenu".$ID ?>" >
<?php foreach ($categories as $category) {
		$active_menu = ' class="VmClose"';
		$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
		$cattext = $category->category_name;
		if (in_array( $category->virtuemart_category_id, $parentCategories))
			$active_menu = ' class="active"';
		?>
<li<?php echo $active_menu ?>>
		<?php
		echo JHTML::link($caturl, $cattext);
		if (strpos($active_menu, 'active'))
			subrender($category->childs,1,array($class_sfx, $cache, $vendorId, $categoryModel->_id, $parentCategories));
?>
</li>
<?php
} ?>
</ul>
</div>
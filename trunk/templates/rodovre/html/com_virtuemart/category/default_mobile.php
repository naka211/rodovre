<?php
//vmdebug('$this->category',$this->category);
vmdebug ('$this->category ' . $this->category->category_name);
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
/*$edit_link = '';
if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
if (Permissions::getInstance()->check("admin,storeadmin")) {
	$edit_link = '<a href="'.JURI::root().'index.php?option=com_virtuemart&tmpl=component&view=category&task=edit&virtuemart_category_id='.$this->category->virtuemart_category_id.'">
		'.JHTML::_('image', 'images/M_images/edit.png', JText::_('COM_VIRTUEMART_PRODUCT_FORM_EDIT_PRODUCT'), array('width' => 16, 'height' => 16, 'border' => 0)).'</a>';
}

echo $edit_link; */
$app = JFactory::getApplication();
$tmpl = JURI::base().'templates/rodovre/mobile/';

$clcat = 29;
$q = 'SELECT `category_parent_id` FROM `#__virtuemart_category_categories` WHERE `category_child_id`=' . $this->category->virtuemart_category_id;
$db = JFactory::getDbo();
$db->setQuery($q);
$parentCategoryID = $db->loadResult();
$parent = 0;
if ($parentCategoryID != '0') {
   $categoryModel = VmModel::getModel('category');
   $parentCategory = $categoryModel->getCategory($parentCategoryID);

   $parent = $parentCategory->virtuemart_category_id;
}
	?>

<div id="content" class="w-content undepages product_page">
	{module Breadcrumbs}
	<div class="eachBox boxProduct">
		<h2><?php echo $this->category->category_name?></h2>
		<?php if($this->category->virtuemart_category_id == $clcat || $parent == $clcat) { ?>
		<script type="text/javascript">
			$('head').append('<link href="<?php echo $tmpl;?>css/styles-moblie-black.css" rel="stylesheet" />');
		</script>
		<?php if($this->category->images){?>
			<div class="banner-box clearfix">
				<div id="banner" class="clearfix">  
					<div class="camera_wrap camera_azure_skin" id="camera_wrap_1">
						<?php foreach ($this->category->images as $pimage) { ?>
						<div data-src="<?php echo $pimage->file_url; ?>"> </div>
						<?php } ?>
					</div>    
				</div>
			 </div>
		<?php }
			}
		?>
		<h4><?php echo $this->category->category_description; ?></h4>
		<?php 
		
		if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
			if ($this->category->haschildren) {?>
		<ul class="list_product clearfix">
			<?php foreach ($this->category->children as $category){
				$caturl = JRoute::_ ('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id);
			?>
			<li>
				<div class="img_main"> <a href="<?php echo $caturl;?>"><?php echo $category->images[0]->displayMediaThumb ('width="217" heigh="161"', FALSE);?></a> </div>
				<h3><?php echo $category->category_name;?></h3>
			</li>
			<?php }?>
		</ul>
		<?php } else {
			if (!empty($this->products)){
		?>
			<ul class="listProd clearfix"> 
				<?php foreach($this->products as $product){
				$link = JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );
				?>
				<li>
					<div class="img_main">
					  <a href="<?php echo $link;?>"><?php echo $product->images[0]->displayMediaThumb( null, false );?></a>
					</div> 
					<h3><?php echo (mb_strlen($product->product_name,"UTF-8") < 62) ? $product->product_name : mb_substr($product->product_name, 0, 61, "UTF-8")."…"?></h3>
					<div class="wrap_price"> 
						<?php if(!empty($product->prices['discountAmount'])){?>
						<p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
						<p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
						<?php }?>
					</div>
					<h4><?php echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );?></h4>
					<a class="btnMore btn2" href="<?php echo $link;?>">Vis detaljer</a>
				</li> 
				<?php }?>
			</ul>
		<?php
				}
			}
		}

		if (!empty($this->keyword)){
			if (!empty($this->products)){
		?>
			<ul class="listProd clearfix"> 
				<?php foreach($this->products as $product){
				$link = JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id );
				?>
				<li>
					<div class="img_main">
					  <a href="<?php echo $link;?>"><?php echo $product->images[0]->displayMediaThumb( null, false );?></a>
					</div> 
					<h3><?php echo (mb_strlen($product->product_name,"UTF-8") < 62) ? $product->product_name : mb_substr($product->product_name, 0, 61, "UTF-8")."…"?></h3>
					<div class="wrap_price"> 
						<?php if(!empty($product->prices['discountAmount'])){?>
						<p class="price_before">Førpris: <?php echo $this->currency->priceDisplay($product->prices['basePrice'],0,1.0,false,$this->currency->_priceConfig['basePrice'][1] );?></p>
						<p class="price_sale">(De sparer: <?php echo $this->currency->priceDisplay(abs($product->prices['discountAmount']),0,1.0,false,$this->currency->_priceConfig['discountAmount'][1] );?>) </p>
						<?php }?>
					</div>
					<h4><?php echo $this->currency->priceDisplay($product->prices['salesPrice'],0,1.0,false,$this->currency->_priceConfig['salesPrice'][1] );?></h4>
					<a class="btnMore btn2" href="<?php echo $link;?>">Vis detaljer</a>
				</li> 
				<?php }?>
			</ul>
		<?php
			} else {
				echo 'Intet resultat' . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
			}
		}
		?>
	</div>
</div>
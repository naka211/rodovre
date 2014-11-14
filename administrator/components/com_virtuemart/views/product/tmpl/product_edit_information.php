<?php
/**
 *
 * Main product information
 *
 * @package	VirtueMart
 * @subpackage Product
 * @author RolandD
 * @todo Price update calculations
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product_edit_information.php 6547 2012-10-16 10:55:06Z alatak $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// set row counter
$i=0;
?>
<div class="vm-lang-list-container">
	<?php echo $this->langList; ?>
</div>
<script>
jQuery(document).ready(function(){
    setImage = function(value){
        if(value){
            jQuery("#type_image_src").attr("src", "<?php echo JURI::base()?>type_images/"+value+".jpg");
            jQuery("#type_image_src").show();
        } else {
            jQuery("#type_image_src").hide();
        }
    }
    setImage(jQuery("#type_image").val());
});
</script>
<style>
img#type_image_src {
    width:70px;
}
</style>
<fieldset>
	<legend><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_INFORMATION'); echo ' id: '.$this->product->virtuemart_product_id ?></legend>
    <table class="adminform" width="100%">
		<tr class="row<?php echo $i?>">
			<td style="min-width:75px;max-width:200px;width:130px;">
				<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_NAME') ?>
			</td>
			<td>
				<input type="text" class="inputbox"  name="product_name" id="product_name" value="<?php echo htmlspecialchars($this->product->product_name); ?>" size="32" maxlength="255" />
			</td>
			<td colspan="2">
				<label><?php echo VmHTML::checkbox('published', $this->product->published); ?><?php echo vmText::_('COM_VIRTUEMART_PUBLISHED') ?></label>
				<label><?php echo VmHTML::checkbox('product_special', $this->product->product_special); ?> <?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_SPECIAL') ?></label>
			</td>
			<td>
				<span class="hastip" title="<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_ALIAS_TIP');?>"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_ALIAS') ?></span>
			</td>
			<td height="18">
				<input type="text" class="inputbox"  name="slug" id="slug" value="<?php echo $this->product->slug; ?>" size="32" maxlength="255" />
			</td>
		</tr>

		<?php $i = 1 - $i; ?>
		<tr class="row<?php echo $i?>">
			<td>
				<span  class="hastip" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_SKU') ?></span>
			</td>
			<td>
				<input type="text" class="inputbox" name="product_sku" id="product_sku" value="<?php echo $this->product->product_sku; ?>" size="32" maxlength="64" />
			</td>
			<td width="130px">
				<span class="hastip" title="<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_GTIN_TIP') ?>"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_GTIN') ?></span>
			</td>
			<td>
				<input type="text" class="inputbox" name="product_gtin" id="product_gtin" value="<?php echo $this->product->product_gtin; ?>" size="32" maxlength="64" />
			</td>
			<td>
				<span class="hastip" title="<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_MPN_TIP') ?>"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_MPN') ?></span>
			</td>
			<td>
				<input type="text" class="inputbox" name="product_mpn" id="product_mpn" value="<?php echo $this->product->product_mpn; ?>" size="32" maxlength="64" />
			</td>
		</tr>

		<?php $i = 1 - $i; ?>
		<tr class="row<?php echo $i?>">
			<?php if(isset($this->lists['manufacturers'])) { ?>
				<td>
					<?php echo vmText::_('COM_VIRTUEMART_MANUFACTURER') ?>
				</td>
				<td>
					<?php echo $this->lists['manufacturers'];?>
				</td>
			<?php } else {
				echo '<td></td><td></td>';
			}?>
			<td>
				<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_PAGE') ?>
			</td>
			<td>
				<?php echo JHTML::_('Select.genericlist', $this->productLayouts, 'layout', 'size=1', 'value', 'text', $this->product->layout); ?>
			</td>
			<td>
				<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_URL') ?>
			</td>
			<td>
				<input type="text" class="inputbox" name="product_url" value="<?php echo $this->product->product_url; ?>" size="32" maxlength="255" />
			</td>
		</tr>
		<?php $i = 1 - $i; ?>
		<tr class="row<?php echo $i?>">
			<td>
				<?php echo vmText::_('COM_VIRTUEMART_CATEGORY_S') ?>
			</td>
			<td>
				<select class="inputbox" id="categories" name="categories[]" multiple="multiple"  data-placeholder="<?php echo vmText::_('COM_VIRTUEMART_DRDOWN_SELECT_SOME_OPTIONS')  ?>" size="10">
					<option value=""><?php echo vmText::_('COM_VIRTUEMART_UNCATEGORIZED') ?></option>
					<?php echo $this->category_tree; ?>
				</select>
			</td>
			<?php
			// It is important to have all product information in the form, since we do not preload the parent
			// I place the ordering here, maybe we make it editable later.
			if(!isset($this->product->ordering)) {
				$this->product->ordering = 0;
				?><input type="hidden" value="<?php echo $this->product->ordering ?>" name="ordering"> <?php
			} ?>
			<td>
				<span class="hasTip" title="<?php echo vmText::_ ('COM_VIRTUEMART_SHOPPER_FORM_GROUP_PRODUCT_TIP'); ?>">
				<?php echo vmText::_('COM_VIRTUEMART_SHOPPER_FORM_GROUP') ?></span>
			</td>
			<td>
				<?php echo $this->shoppergroupList; ?>
			</td>
			<?php if(Vmconfig::get('multix','none')!=='none') { ?>
			<td>
				<?php echo vmText::_('COM_VIRTUEMART_VENDOR') ?>
			</td>
			<td>
				<?php echo $this->lists['vendors'];?>
			</td>
		<?php } else {
				echo '<td><td>';
			}?>
		</tr>
        <!--T.Trung-->
        <?php 
        if($this->document->language == 'da-dk'){
            $select_text = 'Vælg et billede';
            $width_text = 'Bredde';
            $length_text = 'Længde';
            $depth_text = 'Dybde';
            $height_text = 'Højde';
            $seatheight_text = 'Sædehøjde';
        } else {
            $select_text = 'Please select a image';
            $width_text = 'Width';
            $length_text = 'Length';
            $depth_text = 'Depth';
            $height_text = 'Height';
            $seatheight_text = 'Seat height';
        }
        $diameter_text = 'Diameter';
        ?>
        <?php $i = 1 - $i; ?>
        <tr class="row<?php echo $i?>">
            <td>
                <select id="type_image" name="type_image" onChange="setImage(this.value)">
                    <option value=""><?php echo $select_text;?></option>
                    <option value="Borde1" <?php if($this->product->type_image == 'Borde1') echo 'selected'?>>Borde1</option>
                    <option value="Borde2" <?php if($this->product->type_image == 'Borde2') echo 'selected'?>>Borde2</option>
                    <option value="Borde3" <?php if($this->product->type_image == 'Borde3') echo 'selected'?>>Borde3</option>
                    <option value="Borde4" <?php if($this->product->type_image == 'Borde4') echo 'selected'?>>Borde4</option>
                    <option value="Krukker1" <?php if($this->product->type_image == 'Krukker1') echo 'selected'?>>Krukker1</option>
                    <option value="Krukker2" <?php if($this->product->type_image == 'Krukker2') echo 'selected'?>>Krukker2</option>
                    <option value="Krukker3" <?php if($this->product->type_image == 'Krukker3') echo 'selected'?>>Krukker3</option>
                    <option value="Krukker4" <?php if($this->product->type_image == 'Krukker4') echo 'selected'?>>Krukker4</option>
                    <option value="Parasoller1" <?php if($this->product->type_image == 'Parasoller1') echo 'selected'?>>Parasoller1</option>
                    <option value="Parasoller2" <?php if($this->product->type_image == 'Parasoller2') echo 'selected'?>>Parasoller2</option>
                    <option value="Parasoller3" <?php if($this->product->type_image == 'Parasoller3') echo 'selected'?>>Parasoller3</option>
                    <option value="Sofaer" <?php if($this->product->type_image == 'Sofaer') echo 'selected'?>>Sofaer</option>
                    <option value="Stole" <?php if($this->product->type_image == 'Stole') echo 'selected'?>>Stole</option>
                </select>
            </td>
            <td>
                <img id="type_image_src" src="" />
            </td>
            <td colspan="4">
            </td>
        </tr>
        <tr>
            <td colspan="6">
                <div style="width:100px; float:left;"><?php echo $diameter_text?>: </div><input type="text" name="diameter" value="<?php echo $this->product->diameter;?>" style="margin:0px 0px 5px; width:70px" /> cm<br />
                <div style="width:100px; float:left;"><?php echo $width_text?>: </div><input type="text" name="width" value="<?php echo $this->product->width;?>" style="margin:0px 0px 5px; width:70px"/> cm<br />
                <div style="width:100px; float:left;"><?php echo $length_text?>: </div><input type="text" name="length" value="<?php echo $this->product->length;?>" style="margin:0px 0px 5px; width:70px"/> cm<br />
                <div style="width:100px; float:left;"><?php echo $depth_text?>: </div><input type="text" name="depth" value="<?php echo $this->product->depth;?>" style="margin:0px 0px 5px; width:70px"/> cm<br />
                <div style="width:100px; float:left;"><?php echo $height_text?>: </div><input type="text" name="height" value="<?php echo $this->product->height;?>" style="margin:0px 0px 5px; width:70px"/> cm<br />
                <div style="width:100px; float:left;"><?php echo $seatheight_text?>: </div><input type="text" name="seatheight" value="<?php echo $this->product->seatheight;?>" style="margin:0px 0px 5px; width:70px"/> cm<br />
            </td>
        </tr>
        <?php $i = 1 - $i; ?>
        <tr class="row<?php echo $i?>">
            <td colspan="6">
                <div style="width:100px; float:left;">Video: </div><input type="text" name="video" value="<?php echo $this->product->video;?>" style="margin:0px 0px 5px; width:200px" />
            </td>
        </tr>
        <!--T.Trung end-->
        
	</table>
</fieldset>

			<!-- Product pricing -->
			<fieldset>
			    <legend><?php
					echo vmText::sprintf('COM_VIRTUEMART_PRODUCT_FORM_PRICES',$this->activeShoppergroups); ?></legend>
			
				<?php
				//$product = $this->product;
			
				if (empty($this->product->prices)) {
					$this->product->prices[] = array();
				}
				$this->i = 0;
				$rowColor = 0;
				if (!class_exists ('calculationHelper')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
				}
				$calculator = calculationHelper::getInstance ();
				$currency_model = VmModel::getModel ('currency');
				$currencies = $currency_model->getCurrencies ();
				$nbPrice = count ($this->product->prices);
				$this->priceCounter = 0;
				$this->product->prices[$nbPrice] = $this->product_empty_price;
			
			
			
				if (!class_exists ('calculationHelper')) {
					require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'calculationh.php');
				}
				$calculator = calculationHelper::getInstance ();
				?>
			    <table border="0" width="100%" cellpadding="2" cellspacing="3" id="mainPriceTable" class="adminform">
			        <tbody id="productPriceBody">
					<?php
					//vmdebug('grummel ',$this->product->prices);
					foreach ($this->product->prices as $sPrices) {
			
						if(count($sPrices) == 0) continue;
						if (empty($sPrices['virtuemart_product_price_id'])) {
							$sPrices['virtuemart_product_price_id'] = '';
						}
						//vmdebug('my $sPrices ',$sPrices);
						$sPrices = (array)$sPrices;
						$this->tempProduct = (object)array_merge ((array)$this->product, $sPrices);
						$this->calculatedPrices = $calculator->getProductPrices ($this->tempProduct);
			
						if((string)$sPrices['product_price']==='0' or (string)$sPrices['product_price']===''){
							$this->calculatedPrices['costPrice'] = '';
						}
			
						$currency_model = VmModel::getModel ('currency');
						$this->lists['currencies'] = JHTML::_ ('select.genericlist', $currencies, 'mprices[product_currency][' . $this->priceCounter . ']', '', 'virtuemart_currency_id', 'currency_name', $this->tempProduct->product_currency);
			
						$DBTax = ''; //vmText::_('COM_VIRTUEMART_RULES_EFFECTING') ;
						foreach ($calculator->rules['DBTax'] as $rule) {
							$DBTax .= $rule['calc_name'] . '<br />';
						}
						$this->DBTaxRules = $DBTax;
			
						$tax = ''; //vmText::_('COM_VIRTUEMART_TAX_EFFECTING').'<br />';
						foreach ($calculator->rules['Tax'] as $rule) {
							$tax .= $rule['calc_name'] . '<br />';
						}
						foreach ($calculator->rules['VatTax'] as $rule) {
							$tax .= $rule['calc_name'] . '<br />';
						}
						$this->taxRules = $tax;
			
						$DATax = ''; //vmText::_('COM_VIRTUEMART_RULES_EFFECTING');
						foreach ($calculator->rules['DATax'] as $rule) {
							$DATax .= $rule['calc_name'] . '<br />';
						}
						$this->DATaxRules = $DATax;
			
						if (!isset($this->tempProduct->product_tax_id)) {
							$this->tempProduct->product_tax_id = 0;
						}
						$this->lists['taxrates'] = ShopFunctions::renderTaxList ($this->tempProduct->product_tax_id, 'mprices[product_tax_id][' . $this->priceCounter . ']');
						if (!isset($this->tempProduct->product_discount_id)) {
							$this->tempProduct->product_discount_id = 0;
						}
						$this->lists['discounts'] = $this->renderDiscountList ($this->tempProduct->product_discount_id, 'mprices[product_discount_id][' . $this->priceCounter . ']');
			
						$this->lists['shoppergroups'] = ShopFunctions::renderShopperGroupList ($this->tempProduct->virtuemart_shoppergroup_id, false, 'mprices[virtuemart_shoppergroup_id][' . $this->priceCounter . ']');
			
						if ($this->priceCounter == $nbPrice) {
							$tmpl = "productPriceRowTmpl";
						} else {
							$tmpl = "productPriceRowTmpl_" . $this->priceCounter;
						}
			
						?>
			        <tr id="<?php echo $tmpl ?>" class="removable row<?php echo $rowColor?>">
				            <td width="100%">
					        <span class="vmicon vmicon-16-move price_ordering"></span>
					        <?php /* <span class="vmicon vmicon-16-new price-clone" ></span> */ ?>
			                <span class="vmicon vmicon-16-remove price-remove"></span>
							<?php //echo vmText::_ ('COM_VIRTUEMART_PRODUCT_PRICE_ORDER');
							echo $this->loadTemplate ('price'); ?>
						 </td>
			        </tr>
						<?php
						$this->priceCounter++;
					}
					?>
			        </tbody>
			    </table>
			    <div class="button2-left">
			        <div class="blank">
			            <a href="#" id="add_new_price"><?php echo vmText::_ ('COM_VIRTUEMART_PRODUCT_ADD_PRICE') ?> </a>
			        </div>
			    </div>
			</fieldset>
		</tr>
	</td>
</tr>
<tr>
	<td colspan="2" >
	<fieldset>
		<legend>
		<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_CHILD_PARENT'); ?></legend>
		<table class="adminform">
			<tr class="row<?php echo $i?>">
				<td width="50%">
					<?php
						if ($this->product->virtuemart_product_id) {
							$link=JROUTE::_('index.php?option=com_virtuemart&view=product&task=createVariant&virtuemart_product_id='.$this->product->virtuemart_product_id.'&token='.JUtility::getToken() );
							$add_child_button="";
						} else {
							$link="";
							$add_child_button=" not-active";
						}
					?>
					<div class="button2-left <?php echo $add_child_button ?>">
						<div class="blank">
							<?php if ($link) { ?>
								<a href="<?php echo $link ?>">
							<?php } else { ?>
								<span class="hasTip" title="<?php echo vmText::_ ('COM_VIRTUEMART_PRODUCT_ADD_CHILD_TIP'); ?>">
							<?php } ?>
								<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ADD_CHILD');?>
							<?php if ($link) { ?>
								</a>
							<?php } else{ ?>
								</span>
							<?php } ?>
						</div>
					</div>
				</td>
				<th>
					<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_PARENT') ?>
				</th>
				<td width="71%"> 
					<?php if ($this->product->product_parent_id) {
							$result = vmText::_('COM_VIRTUEMART_EDIT').' ' . $this->product_parent->product_name;
							echo ' | '.JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$this->product->product_parent_id),
								$this->product_parent->product_name, array('title' => $result)).' | '.$this->parentRelation;
						}
					?>
				</td>
			</tr>
			<?php $i = 1 - $i; ?>
			<tr class="row<?php echo $i?>" >
				<td width="79%" colspan = "3">
					<?php if (count($this->product_childs)>0 ) {
					
		                	$customs = array();
		                	if(!empty($this->product->customfields)){
		                		foreach($this->product->customfields as $custom){
		                			//vmdebug('my custom',$custom);
		                			if($custom->field_type=='A'){
		                				$customs[] = $custom;
		                			}
		                		}
		                	}
							// vmdebug('ma $customs',$customs);
							?>

						<table class="adminform">
							<tr>
								<th style="text-align: left !important;"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_CHILD') ?></th>
								<th style="text-align: left !important;"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_CHILD_NAME')?></th>
								<th style="text-align: left !important;"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_PRICE_COST')?></th>
								<th style="text-align: left !important;"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_IN_STOCK')?></th>
								<th style="text-align: left !important;" width="5%"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_FORM_ORDERED_STOCK')?></th>
								<?php foreach($customs as $custom){ ?>
									<th>
										<?php echo vmText::sprintf('COM_VIRTUEMART_PRODUCT_CUSTOM_FIELD_N',vmText::_('COM_VIRTUEMART_'.strtoupper($custom->custom_value)))?>
									</th>
								<?php } ?>
								<th><?php echo vmText::_('COM_VIRTUEMART_ORDERING')?></th>
								<th><?php echo vmText::_('COM_VIRTUEMART_PUBLISHED')?> foo</th>
							</tr>
							<?php foreach ($this->product_childs as $child  ) {
								$i = 1 - $i; ?>
								<tr class="row<?php echo $i ?>">
									<td>
										<?php echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$child->virtuemart_product_id), $child->slug, array('title' => vmText::_('COM_VIRTUEMART_EDIT').' '.$child->product_name)) ?>
										<input type="hidden" name="childs[<?php echo $child->virtuemart_product_id ?>][slug]" id="child<?php echo $child->virtuemart_product_id ?>slug" value="<?php echo $child->slug ?>" />
									</td>
									<td><input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][product_name]" id="child<?php echo $child->virtuemart_product_id ?>product_name" size="32" value="<?php echo htmlspecialchars($child->product_name) ?>" /></td>
									<td><input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][mprices][product_price][]" size="10" value="<?php echo $child->product_price ?>" /><input type="hidden" name="childs[<?php echo $child->virtuemart_product_id ?>][mprices][virtuemart_product_price_id][]" value="<?php echo $child->virtuemart_product_price_id?>"  ></td>
									<td><?php echo $child->product_in_stock ?></td>
									<td><?php echo $child->product_ordered ?></td>
									<?php foreach($customs as $custom){
										$attrib = $custom->custom_value;
										if(isset($child->$attrib)){
											$childAttrib = $child->$attrib;
										} else {
											//vmdebug('unset? use Fallback product_name instead $attrib '.$attrib,$child);
											$childAttrib = $child->product_name;
										}
										?>
										<td><input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][<?php echo $attrib ?>]" size="20" value="<?php echo $childAttrib ?>" /></td>
										<?php
									}
									?>
									<td>
	                                    <input type="text" class="inputbox" name="childs[<?php echo $child->virtuemart_product_id ?>][pordering]" size="2" value="<?php echo $child->pordering ?>" /></td>
									</td>
									<td>
										<?php echo VmHTML::checkbox('childs['.$child->virtuemart_product_id.'][published]', $child->published) ?>
									</td>
								</tr>
							<?php } ?>
						</table>
					<?php } ?>
				</td>
			</tr>
		</table>
	</fieldset>
</tr>
<tr>
	<td width="100%" valign="top" colspan="2">
		<fieldset>
			<legend>
				<?php echo vmText::_('COM_VIRTUEMART_PRODUCT_PRINT_INTNOTES'); ?>
			</legend>
			<textarea style="width: 100%;" class="inputbox" name="intnotes" id="intnotes" cols="35" rows="6">
					<?php echo $this->product->intnotes; ?></textarea>
		</fieldset>
	</td>
</tr>



<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#mainPriceTable").dynoTable({
            removeClass:'.price-remove', //remove class name in  table
            cloneClass:'.price-clone', //Custom cloner class name in  table
            addRowTemplateId:'#productPriceRowTmpl', //Custom id for  row template
            addRowButtonId:'#add_new_price', //Click this to add a price
            lastRowRemovable:true, //let the table be empty.
            orderable:true, //prices can be rearranged
            dragHandleClass:".price_ordering", //class for the click and draggable drag handle
            onRowRemove:function () {
            },
            onRowClone:function () {
                //var myLastRow = jQuery('input [name="mprices[virtuemart_product_price_id][]"]',jQuery('#mainPriceTable tr:last') );
                //var myLastRow = jQuery('#mainPriceTable tr:last' );
	            /*jQuery.each(myLastRow,function(key, element1) {
                           jQuery.each(element1,function(key, element) {
                                alert('key: ' + key + '\n' + 'value: ' + element);
                           })
                });*/
            },
            onRowAdd:function () {
            },
            onTableEmpty:function () {
            },
            onRowReorder:function () {
            }
        });
    });

</script>

<script type="text/javascript">
	var tax_rates = new Array();
	<?php
		if( property_exists($this, 'taxrates') && is_array( $this->taxrates )) {
			foreach( $this->taxrates as $key => $tax_rate ) {
				echo 'tax_rates["'.$tax_rate->tax_rate_id.'"] = '.$tax_rate->tax_rate."\n";
			}
		}
	?>
</script>
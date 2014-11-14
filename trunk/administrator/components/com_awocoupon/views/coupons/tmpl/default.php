<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); ?>
<script language="javascript" type="text/javascript">
<!--

function clickpublished(id,type) {
	var form = document.adminForm;
	form.task.value = type;
	
	cb = form.elements['cid[]'];
	if(cb.length == undefined && cb.value==id) {
		cb.checked = true;
		form.submit();
	}
	else {
		for (i = 0; i < cb.length; i++) {
			if(cb[i].value==id) {
				cb[i].checked = true ;
				form.submit();
				return;
			}
		}
	}
}


//-->
</script>

<div class="m">
<ul class="submenu">
	<li><span class="active nolink"><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></span></li>
	<li><a href="index.php?option=com_awocoupon&view=couponsauto"><?php echo JText::_( 'COM_AWOCOUPON_CP_AUTO_DISCOUNT' ); ?></a></li>
</ul>
<div class="clr"></div>
</div>

<form action="index.php" method="post" id="adminForm" name="adminForm">

	<table class="adminform">
		<tr>
			<td width="100%">
			  	<?php echo JText::_( 'COM_AWOCOUPON_GBL_SEARCH' ); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_AWOCOUPON_GBL_GO' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_AWOCOUPON_GBL_RESET' ); ?></button>
			</td>
			<td nowrap="nowrap"></td>
			<td nowrap="nowrap">
				<?php echo $this->lists['function_type']; ?>
				<?php echo $this->lists['coupon_value_type']; ?>
				<?php echo $this->lists['discount_type']; ?>
				<?php echo $this->lists['filter_template']; ?>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this)' : 'checkAll('.count( $this->rows ).')'; ?>;" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_COUPON_CODE', 'c.coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_FUNCTION_TYPE', 'c.function_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE_TYPE', 'c.coupon_value_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE', 'c.coupon_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_NUMBER_USES', 'c.num_of_uses', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE_MIN', 'c.min_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DISCOUNT_TYPE', 'c.discount_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DATE_START', 'c.startdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_EXPIRATION', 'c.expiration', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_ASSET' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'c.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="17"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		foreach ($this->rows as $i=>$row) :
			if ( $row->published == 1 ) {
				$img = com_awocoupon_ASSETS.'/images/published.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'unpublishcoupon\')" style="cursor:pointer;" ';
			} elseif($row->published == -2) {
				$img = com_awocoupon_ASSETS.'/images/template.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_TEMPLATE' );
				$imgjs = '';
			} else{
				$img = com_awocoupon_ASSETS.'/images/unpublished.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'publishcoupon\')" style="cursor:pointer;" ';
			}			
			
			$num_of_uses = $discount_type = $min_value = '--';
			$product_column = $user_column = '';
			$detail_button = '<a class="modal" href="index.php?option=com_awocoupon&amp;view=assets&amp;type=detail&id='.$row->id.'" rel="{handler: \'iframe\', size: {x: 580, y: 590}}"><span><img src="'.com_awocoupon_ASSETS.'/images/coupon-details.png" /></span></a>';
				
			$function_type = $this->AWOCOUPON_lang['function_type'][$row->function_type];
			$coupon_value_type = $row->function_type == 'parent' ? '': $this->AWOCOUPON_lang['coupon_value_type'][$row->coupon_value_type];
			$coupon_value = !empty($row->coupon_value) ? number_format($row->coupon_value,2): $row->coupon_value_def;
			
			if($row->function_type=='giftcert') {
				if(!empty($row->asset1count)) $product_column .= '<div><span id="prproduct1'.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][$row->params->asset1_mode].' '.$row->asset1count.'</span>&nbsp;<span>'.$this->AWOCOUPON_lang['asset_type'][$row->params->asset1_type].'</span></div>';
				if(!empty($row->asset2count)) $product_column .= '<div><span id="prproduct2'.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][$row->params->asset2_mode].' '.$row->asset2count.'</span>&nbsp;<span>'.$this->AWOCOUPON_lang['asset_type'][$row->params->asset2_type].'</span></div>';
			}
			elseif($row->function_type == 'parent') {
				$num_of_uses = empty($row->num_of_uses) ? JText::_( 'COM_AWOCOUPON_GBL_UNLIMITED' ) : $row->num_of_uses.' '.$this->AWOCOUPON_lang['num_of_uses_type'][$row->num_of_uses_type];
				
				$coupon_value_type = $coupon_value = '--';
				$product_column = '<span id="pr'.$row->id.'">'.$this->AWOCOUPON_lang['parent_type'][$row->parent_type].' '.$row->asset1count.'</span>&nbsp;<span>'.JText::_('COM_AWOCOUPON_CP_COUPONS').'</span>';
			}
			else {
				$num_of_uses = empty($row->num_of_uses) ? JText::_( 'COM_AWOCOUPON_GBL_UNLIMITED' ) : $row->num_of_uses.' '.$this->AWOCOUPON_lang['num_of_uses_type'][$row->num_of_uses_type];
				//$min_value = !empty($row->min_value) ? number_format($row->min_value,2): '';
				$min_value = '';
				if(!empty($row->min_value)) {
					$min_value = number_format($row->min_value,2).' '.$this->AWOCOUPON_lang['discount_type'][!empty($row->params->min_value_type) ? $row->params->min_value_type : 'overall'];
				}
				switch($row->user_type) {
					case 'user' : $title = 'COM_AWOCOUPON_CP_CUSTOMERS'; break;
					case 'usergroup' : $title = 'COM_AWOCOUPON_CP_SHOPPER_GROUPS'; break;
				}
				//$user_column = '<div><span id="ur'.$row->id.'">'.(empty($row->{$row->user_type.'count'}) ? JText::_( 'COM_AWOCOUPON_GBL_ALL' ) : $row->{$row->user_type.'count'}).'</span>&nbsp;<span>'.JText::_($title).'</span></div>';
				if(!empty($row->{$row->user_type.'count'})) $user_column = '<div><span id="ur'.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][empty($row->params->user_mode) ? 'include' : $row->params->user_mode].' '.$row->{$row->user_type.'count'}.'</span>&nbsp;<span>'.JText::_($title).'</span></div>';

				if(!empty($row->discount_type)) $discount_type = $this->AWOCOUPON_lang['discount_type'][$row->discount_type];
				$title = '';
				if(!empty($row->params->asset1_type)) {
					switch($row->params->asset1_type) {
						case 'product' : $title = 'COM_AWOCOUPON_CP_PRODUCTS'; break;
						case 'category' : $title = 'COM_AWOCOUPON_CP_CATEGORYS'; break;
						case 'manufacturer' : $title = 'COM_AWOCOUPON_CP_MANUFACTURERS'; break;
						case 'vendor' : $title = 'COM_AWOCOUPON_CP_VENDORS'; break;
						case 'shipping' : $title = 'COM_AWOCOUPON_CP_SHIPPING'; break;
					}
				}
				if($row->function_type=='shipping') {
					if(!empty($row->asset1count)) $product_column .= '<div><span id="pr'.$row->function_type.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][$row->params->asset1_mode].' '.$row->asset1count.'</span>&nbsp;<span>'.JText::_($title).'</span></div>';
					if(!empty($row->asset2count)) $product_column .= '<div><span id="prproduct'.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][$row->params->asset2_mode].' '.$row->asset2count.'</span>&nbsp;<span>'.JText::_('COM_AWOCOUPON_CP_PRODUCTS').'</span></div>';
				}
				elseif($row->function_type=='buy_x_get_y') {
					$product_column .= '<div><span id="prproduct1'.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][$row->params->asset1_mode].' '.$row->asset1count.'</span>&nbsp;<span>'.$this->AWOCOUPON_lang['asset_type'][$row->params->asset1_type].'</span></div>';
					$product_column .= '<div><span id="prproduct2'.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][$row->params->asset2_mode].' '.$row->asset2count.'</span>&nbsp;<span>'.$this->AWOCOUPON_lang['asset_type'][$row->params->asset2_type].'</span></div>';
				}
				else {
					if(!empty($row->asset1count)) $product_column = '<div><span id="pr'.$row->function_type.$row->id.'">'.$this->AWOCOUPON_lang['asset_mode'][$row->params->asset1_mode].' '.$row->asset1count.'</span>&nbsp;<span>'.JText::_($title).'</span></div>';
				}
			} 
			
			
			
			$internal_note = empty($row->note) ? '' : JHTML::tooltip(nl2br($row->note),JText::_('COM_AWOCOUPON_CP_ADMIN_NOTE'), 'tooltip.png', '', '', false);
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="left">
				<?php if($row->is_editable) { ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?>::<?php echo $row->coupon_code; ?>">
					<a href="index.php?option=com_awocoupon&amp;controller=coupons&amp;task=editcoupon&amp;cid[]=<?php echo $row->id; ?>"><?php echo $row->coupon_code; ?></a>
				</span>
				<?php } else echo $row->coupon_code; ?>&nbsp;<?php echo $internal_note; ?>
			</td>
			<td align="center"><?php echo $function_type; ?>&nbsp;</td>
			<td align="center"><?php echo $coupon_value_type; ?>&nbsp;</td>
			<td align="center"><?php echo $coupon_value; ?>&nbsp;</td>
			<td align="center"><?php echo $num_of_uses; ?>&nbsp;</td>
			<td align="center"><?php echo $min_value; ?>&nbsp;</td>
			<td align="center"><?php echo $discount_type; ?>&nbsp;</td>
			<td align="center"><?php echo str_replace(' ','<br />',$row->startdate); ?>&nbsp;</td>
			<td align="center"><?php echo str_replace(' ','<br />',$row->expiration); ?>&nbsp;</td>
			<td align="center" nowrap><?php echo '<table style="width:100%;"><tr><td style="border:0;">'.$user_column.$product_column.'</td><td width="1%" style="border:0;">'.$detail_button.'</td></tr></table>';?></td>
			<td align="center"><?php echo '<img src="'.$img.'" width="16" height="16" class="hand" border="0" alt="'.$alt.'" title="'.$alt.'" '.$imgjs.'/>'; ?></td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="controller" value="coupons" />
	<input type="hidden" name="view" value="coupons" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
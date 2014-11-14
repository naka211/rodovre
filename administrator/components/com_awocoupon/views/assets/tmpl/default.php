<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
?>

<fieldset>
	<div class="" >
		<?php
		if ( $this->row->published == 1 ) {
			$img = com_awocoupon_ASSETS.'/images/published.png';
			$alt = JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' );
		} elseif($this->row->published == -2) {
			$img = com_awocoupon_ASSETS.'/images/template.png';
			$alt = JText::_( 'COM_AWOCOUPON_CP_TEMPLATE' );
		} else{
			$img = com_awocoupon_ASSETS.'/images/unpublished.png';
			$alt = JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' );
		}			
		$num_of_uses = empty($this->row->num_of_uses) ? JText::_( 'COM_AWOCOUPON_GBL_UNLIMITED' ) : $this->row->num_of_uses.' '.$this->AWOCOUPON_lang['num_of_uses_type'][$this->row->num_of_uses_type];
		$coupon_value_type = $this->row->function_type == 'parent' ? '' : $this->AWOCOUPON_lang['coupon_value_type'][$this->row->coupon_value_type];
		if(!empty($this->AWOCOUPON_lang['discount_type'][$this->row->discount_type])) $discount_type = $this->AWOCOUPON_lang['discount_type'][$this->row->discount_type];
		$function_type = $this->AWOCOUPON_lang['function_type'][$this->row->function_type];
		$coupon_value = !empty($this->row->coupon_value) ? $this->row->coupon_value: $this->row->coupon_value_def;
		
		$exclude_str = array();
		if(!empty($this->row->exclude_special)) $exclude_str[] = JText::_( 'COM_AWOCOUPON_CP_SPECIALS' );
		if(!empty($this->row->exclude_giftcert)) $exclude_str[] = JText::_( 'COM_AWOCOUPON_CP_GIFT_PRODUCTS' );
		?>
		<table class="admintable">
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></label></td>
			<td><?php echo $this->row->coupon_code; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_SECRET_KEY' ); ?></label></td>
			<td><?php echo $this->row->passcode; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></label></td>
			<td ><?php echo '<img src="'.$img.'" width="16" height="16" border="0" alt="'.$alt.'" title="'.$alt.'" />'; ?></td>
		</tr>
		<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ); ?></label></td>
			<td><?php echo $function_type; ?></td>
		</tr>
		<?php if($this->row->function_type=='coupon' && !empty($num_of_uses))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_NUMBER_USES' ); ?></label></td>
				<td><?php echo $num_of_uses; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($coupon_value_type))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PERCENT_AMOUNT' ); ?></label></td>
				<td><?php echo $coupon_value_type; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($discount_type))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DISCOUNT_TYPE' ); ?></label></td>
				<td><?php echo $discount_type; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($coupon_value))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></label></td>
				<td><?php echo $coupon_value; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($this->row->params->buy_xy_process_type))  {?>
			<tr><td class="key"><label><?php echo JText::_('COM_AWOCOUPON_CP_PARENT_TYPE'); ?></label></td>
				<td><?php echo $this->AWOCOUPON_lang['buy_xy_process_type'][$this->row->params->buy_xy_process_type]; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($this->row->min_value))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_MIN' ); ?></label></td>
				<td><?php echo number_format($this->row->min_value,2).' '.$this->AWOCOUPON_lang['discount_type'][!empty($this->row->params->min_value_type) ? $this->row->params->min_value_type : 'overall']; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($this->row->params->max_discount_qty))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_MAX_DISCOUNT_QTY' ); ?></label></td>
				<td><?php echo $this->row->params->max_discount_qty; ?></td>
			</tr>
		<?php } ?>
		<?php if($this->row->function_type=='parent')  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_PARENT_TYPE' ); ?></label></td>
				<td><?php echo $this->AWOCOUPON_lang['parent_type'][$this->row->parent_type]; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($this->row->params->asset1_qty))  {?>
			<tr><td class="key"><label><?php echo JText::_('COM_AWOCOUPON_CP_BUY_X'); ?></label></td>
				<td><?php echo $this->row->params->asset1_qty; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($this->row->params->asset2_qty))  {?>
			<tr><td class="key"><label><?php echo JText::_('COM_AWOCOUPON_CP_GET_Y'); ?></label></td>
				<td><?php echo $this->row->params->asset2_qty; ?></td>
			</tr>
		<?php } ?>
		<?php if($this->row->function_type=='buy_x_get_y')  {?>
			<tr><td class="key"><label><?php echo JText::_('COM_AWOCOUPON_CP_DONT_MIX_PRODUCTS'); ?></label></td>
				<td><?php echo JText::_(empty($this->row->params->product_match) ? 'COM_AWOCOUPON_GBL_NO' : 'COM_AWOCOUPON_GBL_YES'); ?></td>
			</tr>
			<tr><td class="key"><label><?php echo JText::_('COM_AWOCOUPON_CP_ADDTOCART_GETY'); ?></label></td>
				<td><?php echo JText::_(empty($this->row->params->addtocart) ? 'COM_AWOCOUPON_GBL_NO' : 'COM_AWOCOUPON_GBL_YES'); ?></td>
			</tr>
		<?php } ?>
		

		<?php if(!empty($this->row->startdate))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_DATE_START' ); ?></label></td>
				<td><?php echo $this->row->startdate; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($this->row->expiration))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ); ?></label></td>
				<td><?php echo $this->row->expiration; ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($exclude_str))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_EXCLUDE' ); ?></label></td>
				<td><?php echo implode(', ',$exclude_str); ?></td>
			</tr>
		<?php } ?>
		<?php if(!empty($this->row->note))  {?>
			<tr><td class="key"><label><?php echo JText::_( 'COM_AWOCOUPON_CP_ADMIN_NOTE' ); ?></label></td>
				<td><?php echo nl2br($this->row->note); ?></td>
			</tr>
		<?php } ?>
		
		
		</table>
		
	</div>
</fieldset>


<?php
switch ($this->row->_type) {
	case 'parent': echo $this->loadTemplate('parent'); break;
	case 'product': echo $this->loadTemplate('default'); break;
	case 'category': echo $this->loadTemplate('default'); break;
	case 'manufacturer': echo $this->loadTemplate('default'); break;
	case 'vendor': echo $this->loadTemplate('default'); break;
	case 'shipping': echo $this->loadTemplate('default'); break;
	case 'detail': echo $this->loadTemplate('detail'); break;

}
?>

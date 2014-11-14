<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
?>


<?php

echo awoJHtmlSliders::start('extra_options', array('closeAll'=>0));



if(!empty($this->row->users)) {
	$user_title = '';
	if($this->row->user_type == 'user') $user_title = JText::_('COM_AWOCOUPON_CP_CUSTOMERS');		
	elseif($this->row->user_type == 'usergroup') $user_title = JText::_('COM_AWOCOUPON_CP_SHOPPER_GROUPS');	

	$user_title .= ' ('.$this->AWOCOUPON_lang['asset_mode'][empty($this->row->params->user_mode) ? 'include' : $this->row->params->user_mode].')';		
	
	
echo awoJHtmlSliders::panel($user_title, 'pn_user');
?>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5">#</th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_CP_ASSET'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->row->users as $i=>$row) : ?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo ($i+1); ?></td>
			<td align="center"><?php echo $row->asset_id; ?></td>
			<td align="center"><?php echo $row->_name; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	</table>

<?php
}
?>

<?php
if(!empty($this->row->asset1)) {

$asset1_title = '';
if($this->row->function_type == 'shipping') $asset1_title = JText::_('COM_AWOCOUPON_CP_SHIPPING').' ('.$this->AWOCOUPON_lang['asset_mode'][$this->row->params->asset1_mode].')';
elseif($this->row->function_type == 'parent') $asset1_title = JText::_('COM_AWOCOUPON_CP_COUPONS');
elseif($this->row->function_type == 'buy_x_get_y') $asset1_title = JText::_('COM_AWOCOUPON_CP_BUY_X').' ('.$this->AWOCOUPON_lang['asset_mode'][$this->row->params->asset1_mode].' '.$this->AWOCOUPON_lang['asset_type'][$this->row->params->asset1_type].')';	
else {
	$lang = 'COM_AWOCOUPON_CP_PRODUCTS';
	if(!empty($this->row->params->asset1_type)) {
		if($this->row->params->asset1_type == 'product') $lang = 'COM_AWOCOUPON_CP_PRODUCTS';
		elseif($this->row->params->asset1_type == 'category') $lang = 'COM_AWOCOUPON_CP_CATEGORYS';
		elseif($this->row->params->asset1_type == 'manufacturer') $lang = 'COM_AWOCOUPON_CP_MANUFACTURERS';
		elseif($this->row->params->asset1_type == 'vendor') $lang = 'COM_AWOCOUPON_CP_VENDORS';
	}
	$asset1_title = JText::_($lang).' '.(!empty($this->row->params->asset1_mode) ? '('.$this->AWOCOUPON_lang['asset_mode'][$this->row->params->asset1_mode].')' : '');
}

echo awoJHtmlSliders::panel($asset1_title, 'pn_asset1');
if(empty($this->row->asset1)) echo JText::_('COM_AWOCOUPON_GBL_ALL');
else {
?>

		<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="5">#</th>
				<th class="title"><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th>
				<th class="title"><?php echo JText::_('COM_AWOCOUPON_CP_ASSET'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->row->asset1 as $i=>$row) : ?>
			<tr class="row<?php echo ($i%2); ?>">
				<td><?php echo ($i+1); ?></td>
				<td align="center"><?php echo $row->asset_id; ?></td>
				<td align="center"><?php echo $row->asset_name; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		</table>

<?php } 
}
?>




<?php
if(!empty($this->row->asset2)) {
	$asset2_title = '';
	if($this->row->function_type == 'giftcert') $asset2_title = JText::_('COM_AWOCOUPON_CP_SHIPPING').' ('.$this->AWOCOUPON_lang['asset_mode'][$this->row->params->asset2_mode].')';
	elseif($this->row->function_type == 'shipping') $asset2_title = JText::_('COM_AWOCOUPON_CP_PRODUCTS').' ('.$this->AWOCOUPON_lang['asset_mode'][$this->row->params->asset2_mode].')';
	elseif($this->row->function_type == 'buy_x_get_y') $asset2_title = JText::_('COM_AWOCOUPON_CP_GET_Y').' ('.$this->AWOCOUPON_lang['asset_mode'][$this->row->params->asset2_mode].' '.$this->AWOCOUPON_lang['asset_type'][$this->row->params->asset2_type].')';
	
echo awoJHtmlSliders::panel($asset2_title, 'pn_asset2');
?>				
		<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="5">#</th>
				<th class="title"><?php echo JText::_('COM_AWOCOUPON_GBL_ID'); ?></th>
				<th class="title"><?php echo JText::_('COM_AWOCOUPON_CP_ASSET'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->row->asset2 as $i=>$row) : ?>
			<tr class="row<?php echo ($i%2); ?>">
				<td><?php echo ($i+1); ?></td>
				<td align="center"><?php echo $row->asset_id; ?></td>
				<td align="center"><?php echo $row->asset_name; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		</table>

<?php } 

echo awoJHtmlSliders::end();
?>

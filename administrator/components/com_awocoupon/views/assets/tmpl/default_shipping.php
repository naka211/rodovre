<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
?>

<fieldset><legend><?php echo JText::_($this->asset_title);?></legend>
<form action="index.php" method="post" id="adminForm" name="adminForm">

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'shipper_id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_SHIPPING_MODULE', 'shipping_module', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_DESCRIPTION', 'shipper_string', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach ($this->row->assets as $i=>$row) :
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo ($i+1); ?></td>
			<td align="center"><?php echo $row->shipper_id; ?></td>
			<td align="center"><?php echo $row->shipping_module; ?></td>
			<td align="center"><?php echo $row->shipper_string; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="controller" value="coupons" />
	<input type="hidden" name="view" value="assets" />
	<input type="hidden" name="type" value="<?php echo $this->row->_type; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</fieldset>
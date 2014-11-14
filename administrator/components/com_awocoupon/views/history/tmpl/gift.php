<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
?>
<div class="m">
<ul class="submenu">
	<li><a href="index.php?option=com_awocoupon&view=history&layout=coupon"><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></a></li>
	<li><span class="active nolink"><?php echo JText::_( 'COM_AWOCOUPON_GC_GIFTCERTS' ); ?></span></li>
	<li><a href="index.php?option=com_awocoupon&view=history&layout=order"><?php echo JText::_( 'COM_AWOCOUPON_GBL_ORDERS' ); ?></a></li>
</ul>
<div class="clr"></div>
</div>


<form action="index.php" method="post" id="adminForm" name="adminForm">

	<table class="adminform">
		<tr>
			<td width="100%">
			  	<?php echo JText::_( 'COM_AWOCOUPON_GBL_SEARCH' ); ?>
				<input type="text" name="search" id="searchuses" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
				<?php echo $this->lists['search_type']; ?>
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_AWOCOUPON_GBL_GO' ); ?></button>
				<button onclick="document.getElementById('searchuses').value='';document.getElementById('search_type').selectedIndex=0;this.form.submit();"><?php echo JText::_( 'COM_AWOCOUPON_GBL_RESET' ); ?></button>
			</td>
			<td nowrap="nowrap"></td>
			<td nowrap="nowrap">
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this)' : 'checkAll('.count( $this->row ).')'; ?>;" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GC_GIFTCERT', 'c.coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE', 'c.coupon_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GC_VALUE_USED', 'coupon_value_used', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GC_BALANCE', 'balance', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_EXPIRATION', 'c.expiration', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="13"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		
		foreach ($this->row as $i=>$row) :
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="center"><?php echo $row->coupon_code; ?></td>
			<td align="center"><?php echo number_format($row->coupon_value,2); ?></td>
			<td align="center"><?php echo number_format($row->coupon_value_used,2); ?></td>
			<td align="center"><?php echo number_format($row->balance,2); ?></td>
			<td align="center"><?php echo $row->expiration; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="history" />
	<input type="hidden" name="layout" value="gift" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

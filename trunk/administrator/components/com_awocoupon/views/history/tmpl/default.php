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
	<li><span class="active nolink"><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></span></li>
	<li><a href="index.php?option=com_awocoupon&view=history&layout=gift"><?php echo JText::_( 'COM_AWOCOUPON_GC_GIFTCERTS' ); ?></a></li>
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
				<?php echo $this->lists['function_type']; ?>
				<?php echo $this->lists['coupon_value_type']; ?>
				<?php echo $this->lists['discount_type']; ?>
				<?php echo $this->lists['published']; ?>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this)' : 'checkAll('.count( $this->row ).')'; ?>;" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_COUPON_CODE', 'c.coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'uu.user_id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_EMAIL', 'uu.user_email', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_USERNAME', '_username', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_LAST_NAME', '_lname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_FIRST_NAME', '_fname', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DISCOUNT', 'discount', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GC_ORDER_NUM', 'c.order_id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_RPT_ORDER_DATE', '_created_on', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="12"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		
		foreach ($this->row as $i=>$row) :
			$coupon_code = $row->coupon_entered_code.($row->coupon_id!=$row->coupon_entered_id ? ' ('.$row->coupon_code.')' : '');
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->use_id ); ?></td>
			<td align="center"><?php echo $coupon_code; ?></td>
			<td align="center"><?php echo $row->user_id; ?></td>
			<td align="center"><?php echo $row->user_email; ?></td>
			<td align="center"><?php echo $row->_username; ?></td>
			<td align="center"><?php echo $row->_lname; ?></td>
			<td align="center"><?php echo $row->_fname; ?></td>
			<td align="center"><?php echo number_format($row->discount,2); ?></td>
			<td align="center"><?php echo !empty($row->order_id) ? '<a href="'.call_user_func(array(AWOCOUPON_ESTOREHELPER,'getOrderDetailLink'),$row->order_id).'">'.$row->order_number.'</a>' : '&nbsp;'; ?></td>
			<td align="center"><?php echo !empty($row->_created_on) ? date('Y-m-d',strtotime($row->_created_on)) : '&nbsp;'; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="history" />
	<input type="hidden" name="layout" value="default" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

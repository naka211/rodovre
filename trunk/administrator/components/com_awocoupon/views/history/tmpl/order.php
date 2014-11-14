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
	<li><a href="index.php?option=com_awocoupon&view=history&layout=gift"><?php echo JText::_( 'COM_AWOCOUPON_GC_GIFTCERTS' ); ?></a></li>
	<li><span class="active nolink"><?php echo JText::_( 'COM_AWOCOUPON_GBL_ORDERS' ); ?></span></li>
</ul>
<div class="clr"></div>
</div>


<form action="index.php" method="post" id="adminForm" name="adminForm">

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this)' : 'checkAll('.count( $this->row ).')'; ?>;" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GC_ORDER_NUM', 'go.order_id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_GC_CODES'); ?></th>
			<th class="title"></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="13"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		
		foreach ($this->row as $i=>$row) :
			@parse_str($row->codes,$codes);
			$code_list = array();
			if(!empty($codes[0]['p'])) {
				foreach($codes AS $code) $code_list[] = $code['c'];
			} else {
				$code_list = explode(',',$row->codes);
			}
			$codestr = implode(', ',$code_list);
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->order_id ); ?></td>
			<td align=""><a href="<?php echo call_user_func(array(AWOCOUPON_ESTOREHELPER,'getOrderDetailLink'),$row->order_id); ?>"><?php echo $row->order_number; ?></a></td>
			<td align=""><?php echo $codestr; ?>&nbsp;</td>
			<td align=""><input type="button" value="<?php echo JText::_('COM_AWOCOUPON_GBL_RESEND')?>" onclick="this.form.toggle.checked=false;<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this.form.toggle)' : 'checkAll('.count( $this->row ).')'; ?>;document.getElementById('cb<?php echo $i; ?>').checked=true;submitbutton('resend_giftcert')" /></td>
		</tr>
		<?php endforeach; ?>
	</tbody>


	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="history" />
	<input type="hidden" name="layout" value="order" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

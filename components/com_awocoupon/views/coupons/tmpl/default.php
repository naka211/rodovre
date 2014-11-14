<?php 
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

 
// no direct access
defined('_JEXEC') or die('Restricted access');

global $AWOCOUPON_lang;
?>

<script language="javascript" type="text/javascript">
function tableOrdering( order, dir, task ) {
        var form = document.adminForm;

        form.filter_order.value = order;
        form.filter_order_Dir.value = dir;
        document.adminForm.submit( task );
}
</script>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
	<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
<?php endif; ?>


<form id="adminForm" name="adminForm" action="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=coupons' );?>" method="post">
<div class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ) ?>">
	<table cellspacing="0" cellpadding="0" border="0" width="90%">
	<thead>
	<tr>
		<th class="sectiontableheader"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_COUPON_CODE', 'coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		<th class="sectiontableheader"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE_TYPE', 'coupon_value_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		<th class="sectiontableheader"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE', 'coupon_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		<th class="sectiontableheader"><?php echo JText::_('COM_AWOCOUPON_GC_BALANCE'); ?></th>
		<th class="sectiontableheader"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DATE_START', 'startdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		<th class="sectiontableheader"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_EXPIRATION', 'expiration', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
	</tr></thead>
	<tbody>
	<?php 
	$i=-1;
	//printrx($this->rows);
	foreach($this->rows as $row) {
		$i++;
		?>
		<tr class="sectiontableentry<?php echo ($i%2)+1;?>">
			<td><?php 
					echo $row->coupon_code; 
					if(!empty($row->filename)) 
						echo ' <a class="modal" rel="{handler: \'iframe\', size: {x:600, y: 550}}" href="'.JRoute::_('index.php?option=com_awocoupon&tmpl=component&view=giftcerts&coupon_code='.$row->coupon_code).'">
									<img src="'.JURI::root(true).'/components/com_awocoupon/assets/images/icon_view.png" style="height:20px;" >
								</a>';
				?></td>
			<td><?php echo JText::_($AWOCOUPON_lang['function_type'][$row->function_type == 'parent' ? 'coupon': $row->function_type]); ?></td>
			<td><?php echo $row->str_coupon_value; ?></td>
			<td><?php echo isset($row->balance) ? $row->str_balance : '---'; ?></td>
			<td><?php echo $row->startdate; ?></td>
			<td><?php echo $row->expiration; ?></td>
		</tr>
	<?php } ?>
	</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_awocoupon" />
<input type="hidden" name="view" value="coupons" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

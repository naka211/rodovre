<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

$listOrder = $this->lists['order'];
$listDirn  = $this->lists['order_Dir'];
if(empty($listDirn)) $listDirn = 'asc';
$saveOrder	= $listOrder == 'a.ordering';
?>

<div class="m">
<ul class="submenu">
	<li><a href="index.php?option=com_awocoupon&view=coupons"><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></a></li>
	<li><span class="active nolink"><?php echo JText::_( 'COM_AWOCOUPON_CP_AUTO_DISCOUNT' ); ?></span></li>
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
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DISCOUNT_TYPE', 'c.discount_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="10%"><?php echo JHtml::_('grid.sort', 'COM_AWOCOUPON_GBL_ORDERING', 'a.ordering', $listDirn, $listOrder);
						if ($saveOrder) echo JHtml::_('grid.order',  $this->rows, 'filesave.png', 'couponsautosaveorder'); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="14"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		foreach ($this->rows as $i=>$row) :
			if ( $row->published == 1 ) {
				$img = com_awocoupon_ASSETS.'/images/published.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'UNPUBLISHcouponsauto\')"';
			} else {
				$img = com_awocoupon_ASSETS.'/images/unpublished.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'PUBLISHcouponsauto\')"';
			}			
					
			$num_of_uses = $discount_type = '--';
				
			$function_type = $this->AWOCOUPON_lang['function_type'][$row->function_type];
			$coupon_value_type = $row->function_type == 'parent' ? '': $this->AWOCOUPON_lang['coupon_value_type'][$row->coupon_value_type];
			$coupon_value = !empty($row->coupon_value) ? number_format($row->coupon_value,2): $row->coupon_value_def;
			
			if($row->function_type != 'giftcert') {
				$num_of_uses = empty($row->num_of_uses) ? JText::_( 'COM_AWOCOUPON_GBL_UNLIMITED' ) : $row->num_of_uses.' '.$this->AWOCOUPON_lang['num_of_uses_type'][$row->num_of_uses_type];
			
				if($row->function_type == 'parent') { $coupon_value_type = $coupon_value = '--'; } 
				else { if(!empty($row->discount_type)) $discount_type = $this->AWOCOUPON_lang['discount_type'][$row->discount_type]; }
			}
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="center"><?php echo $row->coupon_code; ?>&nbsp;</td>
			<td align="center"><?php echo $function_type; ?>&nbsp;</td>
			<td align="center"><?php echo $coupon_value_type; ?>&nbsp;</td>
			<td align="center"><?php echo $coupon_value; ?>&nbsp;</td>
			<td align="center"><?php echo $num_of_uses; ?>&nbsp;</td>
			<td align="center"><?php echo $discount_type; ?>&nbsp;</td>
			<td class="order"><?php 
				if ($saveOrder) { 
					$ordering	= ($listOrder == 'a.ordering');
					if ($listDirn == 'asc') { ?>
						<span><?php echo $this->pageNav->orderUpIcon($i, true, 'couponsautoorderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pageNav->orderDownIcon($i, $this->pageNav->total, true, 'couponsautoorderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php } elseif ($listDirn == 'desc') { ?>
						<span><?php echo $this->pageNav->orderUpIcon($i, true, 'couponsautoorderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pageNav->orderDownIcon($i, $this->pageNav->total, true, 'couponsautoorderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php } 
				}
				$disabled = $saveOrder ?  '' : 'disabled="disabled"'; 
				?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
			</td>
			<td align="center"><?php echo '<img src="'.$img.'" width="16" height="16" border="0" alt="'.$alt.'" title="'.$alt.'" '.$imgjs.' style="cursor:pointer;"/>'; ?></td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="couponsauto" />
	<input type="hidden" name="layout" value="default" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
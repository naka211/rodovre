<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); ?>

<div class="m">
<ul class="submenu">
	<li><span class="active nolink"><?php echo JText::_( 'COM_AWOCOUPON_GC_PRODUCTS' ); ?></span></li>
	<li><a href="index.php?option=com_awocoupon&view=giftcertcode"><?php echo JText::_( 'COM_AWOCOUPON_GC_CODES' ); ?></a></li>
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
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_PRODUCT', '_product_name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_TEMPLATE', 'c.coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_PF_IMAGE', 'pr.title', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JText::_( 'COM_AWOCOUPON_GC_CODES' ); ?></th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_CP_EXPIRATION'); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VENDOR', 'g.vendor_name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'g.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="14"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		foreach ($this->rows as $i=>$row) :
			if ( $row->published == 1 ) {
				$img = com_awocoupon_ASSETS.'/images/published.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'UNPUBLISHgiftcert\')"';
			} else {
				$img = com_awocoupon_ASSETS.'/images/unpublished.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'PUBLISHgiftcert\')"';
			}			
			$expiration = !empty($row->expiration_number) && !empty($row->expiration_type) 
					? $row->expiration_number.' '.$this->AWOCOUPON_lang['expiration_type'][$row->expiration_type]
					: '';
					
			
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_AWOCOUPON_GC_PRODUCT' ); ?>::<?php echo $row->_product_name; ?>">
					<a href="index.php?option=com_awocoupon&amp;task=EDITgiftcert&amp;cid[]=<?php echo $row->id; ?>"><?php echo $row->_product_name; ?></a>
				</span>
			</td>
			<td align="center"><?php echo $row->coupon_code; ?>&nbsp;</td>
			<td align="center"><?php echo $row->profile; ?></td>
			<td align="center"><?php echo $row->codecount;  
				if(!empty($row->codecount) )
					echo '[ <a href="index.php?option=com_awocoupon&amp;view=giftcertcode&amp;filter_product='.$row->product_id.'&filter_state=&search=" ><span>'.JText::_('COM_AWOCOUPON_GBL_VIEW').'</span></a> ]';
				?>
			</td>
			<td align="center"><?php echo $expiration; ?>&nbsp;</td>
			<td align=""><?php echo $row->vendor_name.(!empty($row->vendor_email) ? ' &lt;'.$row->vendor_email.'&gt;' : ''); ?>&nbsp;</td>
			<td align="center"><?php echo '<img src="'.$img.'" width="16" height="16" border="0" alt="'.$alt.'" title="'.$alt.'" '.$imgjs.' style="cursor:pointer;"/>'; ?></td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="giftcert" />
	<input type="hidden" name="layout" value="default" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
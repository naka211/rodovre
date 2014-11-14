<?php 
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

defined('_JEXEC') or die('Restricted access'); 
$this->_toolbarDefault();
$rows = $this->products;
?>
<form action="index.php?option=com_jmsvmcustom" method="post" name="adminForm">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_status').value='0';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php echo $this->lists['language'];?>
				<?php echo $this->lists['filter_cat_id'];?>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellpadding="1">
		<thead>
			<tr>
				<th width="2%" class="title">
					<?php echo JText::_( 'NUM' ); ?>
				</th>
				<th width="5%" class="title">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" />
				</th>
				<th class="title" style="text-align:left!important;">
					<?php echo JText::_('PRODUCT_NAME');?>
				</th>				
				<th width="25%" class="title" nowrap="nowrap">
					<?php echo JText::_('CATEGORY');?>
				</th>				
				<th width="10%" class="title" nowrap="nowrap">
					<?php echo JText::_('ID');?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			$k = 0;
			
			for ($i=0, $n=count( $rows ); $i < $n; $i++)
			{
				$row 	=& $rows[$i];
				$link 	= 'index.php?option=com_jmsvmcustom&controller=products&task=edit&cid[]='. $row->virtuemart_product_id;			
				
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->virtuemart_product_id ); ?>
				</td>
				<td align="left">
					<a href="<?php echo $link; ?>">
						<?php echo $row->product_name; ?>
					</a>
				</td>				
				<td>
					<?php echo $row->category_name;?>
				</td>				
				<td align="center">
					<?php echo $row->virtuemart_product_id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_jmsvmcustom" />
	<input type="hidden" name="controller" value="products" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
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
$ordering = 1;
$rows = $this->rows;
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
				<?php echo $this->lists['state'];?>
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
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
				</th>
				<th class="title" style="text-align:left!important;">
					<?php echo JText::_('COLOR_TITLE');?>
				</th>
				<th width="15%" class="title" nowrap="nowrap">
					<?php echo JText::_('COLOR_ICON');?>
				</th>
				<th width="15%" class="title" nowrap="nowrap">
					<?php echo JText::_('Published');?>
				</th>
				<th width="15%" nowrap="nowrap">
					<?php echo JText::_('Order');?>
					<?php if ($ordering) echo JHTML::_('grid.order',  $rows ); ?>
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

				$img 	= !$row->published ? 'publish_x.png' : 'tick.png';
				$task 	= !$row->published ? 'unpublish' : 'publish';
				$alt 	= !$row->published ? JText::_( 'ACTIVE' ) : JText::_( 'UNACTIVE' );
				$link 	= 'index.php?option=com_jmsvmcustom&controller=colors&task=edit&cid[]='. $row->id;				
				$published		= JHTML::_('grid.published', $row, $i );

			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $i+1+$this->pagination->limitstart;?>
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td align="left">
					<a href="<?php echo $link; ?>">
						<?php echo $row->color_title; ?>
					</a>
				</td>
				<td align="left">					
					<img src="<?php echo JURI::Root();?>administrator/components/com_jmsvmcustom/assets/color_icons/<?php echo $row->color_icon; ?>" />					
				</td>
				<td align="center">
					<?php echo $published;?>
				</td>
				<td class="order">
						<span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', 1 ); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?> class="text_area" style="text-align: center" />
				</td>
				
				<td align="center">
					<?php echo $row->id; ?>
				</td>
			</tr>
			<?php
				$k = 1 - $k;
				}
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_jmsvmcustom" />
	<input type="hidden" name="controller" value="colors" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
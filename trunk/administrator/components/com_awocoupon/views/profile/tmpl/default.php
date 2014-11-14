<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); ?>

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
			<td nowrap="nowrap"></td>
		</tr>
	</table>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this)' : 'checkAll('.count( $this->rows ).')'; ?>;" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_PF_TITLE', 'title', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_PF_FROM_NAME', 'from_name', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_PF_FROM_EMAIL', 'from_email', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_PF_BCC_ADMIN', 'bcc_admin', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_PF_EMAIL_SUBJECT', 'email_subject', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_PF_MESSAGE_TYPE', 'message_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap">&nbsp;</th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_GBL_DEFAULT' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot><tr><td colspan="14"><?php echo $this->pageNav->getListFooter(); ?></td></tr></tfoot>

	<tbody>
		<?php
		foreach ($this->rows as $i=>$row) :
			$default = $row->is_default==1 ? '<img src="'.com_awocoupon_ASSETS.'/images/icon-16-default.png" border="0"  />' : '';
			$message_type = $this->AWOCOUPON_lang['giftcert_message_type'][$row->message_type];
			$bcc_admin = JText::_( !empty($row->bcc_admin) ? 'COM_AWOCOUPON_GBL_YES' : 'COM_AWOCOUPON_GBL_NO' );
			
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_AWOCOUPON_GBL_EDIT' ); ?>::<?php echo $row->title; ?>">
					<a href="index.php?option=com_awocoupon&amp;task=EDITprofile&amp;cid[]=<?php echo $row->id; ?>"><?php echo $row->title; ?></a>
				</span>
			</td>
			<td align="center"><?php echo $row->from_name; ?>&nbsp;</td>
			<td align="center"><?php echo $row->from_email; ?>&nbsp;</td>
			<td align="center"><?php echo $bcc_admin; ?>&nbsp;</td>
			<td align="center"><?php echo $row->email_subject; ?>&nbsp;</td>
			<td align="center"><?php echo $message_type; ?>&nbsp;</td>
			<td align="left">
					<?php if($row->message_type=='html') { ?>
					<a class="modal" href="index.php?option=com_awocoupon&task=previewprofile&cid=<?php echo $row->id; ?>" rel="{handler: 'iframe', size: {x: 600, y: 400}}">
						<span><?php echo JText::_('COM_AWOCOUPON_PF_PREVIEW'); ?></span>
					</a>
					<?php } else echo '&nbsp;'; ?>
			</td>
			<td align="center"><?php echo $default; ?>&nbsp;</td>
			<td align="center"><?php echo $row->id; ?>&nbsp;</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="profile" />
	<input type="hidden" name="layout" value="default" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
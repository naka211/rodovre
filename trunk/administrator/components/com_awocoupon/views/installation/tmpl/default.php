<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); ?>

<style>tr.error td {color:red }</style>

<form action="index.php" method="post" name="adminForm">


	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5">&nbsp;</th>
			<th class="title"><?php echo JText::_( 'COM_AWOCOUPON_RPT_STATUS' ); ?></th>
			<th class="title"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NAME' ); ?></th>
			<th class="title"><?php echo JText::_( 'COM_AWOCOUPON_GBL_DESCRIPTION' ); ?></th>
			<th class="title"><?php echo JText::_( 'COM_AWOCOUPON_GBL_ERROR' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach($this->checks as $id=>$row) :
			$error = $row['err'];
			$todo = !empty($error) 
						? JHTML::_('grid.id', $id,$id )
						: '<a href="'.JRoute::_( 'index.php?option=com_awocoupon&task=removeinstallation&cid[]='. $id ).'">
							<img src="'.com_awocoupon_ASSETS.'/images/uninstall.png" width="20" border="0" alt="'.JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ).'" title="'.JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ).'" /></a>';
		?>
		<tr class="<?php if(!empty($error)) echo 'error';?>">
			<td width="7"><?php echo $todo; ?>&nbsp;</td>
			<td><?php echo JText::_($row['status']); ?>&nbsp;</td>
			<td width="1%" nowrap><?php echo JText::_($row['name']); ?></td>
			<td><?php echo JText::_( 'COM_AWOCOUPON_GBL_FILE' ).': '.$row['file'].'<br />'.JText::_($row['desc']); ?>&nbsp;</td>
			<td><?php echo JText::_($error); ?>&nbsp;</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_awocoupon" />
	<input type="hidden" name="view" value="installation" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
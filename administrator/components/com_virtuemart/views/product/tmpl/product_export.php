<?php
/**
 *
 * Main product information
 *
 * @package	VirtueMart
 * @subpackage Product
 * @author RolandD
 * @todo Price update calculations
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product_edit_information.php 6547 2012-10-16 10:55:06Z alatak $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
<?php echo $this->langList;
$i=0;
?>

<form method="post" name="adminForm" action="index.php" enctype="multipart/form-data" id="adminForm">
<fieldset>
	<legend>
	<?php echo JText::_('Products Import');?></legend>
    <table width="100%">
	  

			
						<?php $i = 1 - $i; ?>

						<tr class="row<?php echo $i?>">
							<td>
								<div style="text-align:right;font-weight:bold;">
								<?php echo JText::_('COM_VIRTUEMART_CATEGORY_S') ?></div>
							</td>
							<td colspan="0">
								<select class="inputbox" id="categories" name="categories">
									<option value=""><?php echo JText::_('COM_VIRTUEMART_UNCATEGORIZED')  ?></option>
									<?php echo $this->category_tree; ?>
								</select>
							</td>	

									<th width="20px"><input type="submit" name="submit" value="Export"></th>


						</tr>
				
</fieldset>

<input type="hidden" name="task" value="saveExport" />
<input type="hidden" name="option" value="com_virtuemart" />
<input type="hidden" name="view" value="product" />
<input type="hidden" name="controller" value="product" />


	</table>
</fieldset>
</form>			
				




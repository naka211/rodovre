<?php
 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidation');
if(JRequest::getVar('success')){?>

<div class="w-content undepages newsletter_page" id="content">
	{module Breadcrumbs}
	<div class="eachBox wrap-newsletter clearfix">
		<h2>Kvittering</h2>
		Du er hermed tilmeldt vores nyhedsbrev.<br>
		<br>
		Med venlig hilsen<br>
		Krukker & Havemøbler ApS
		</p>
	</div>
</div>
<?php } else { ?>
<div class="w-content undepages newsletter_page" id="content">
	{module Breadcrumbs}
	<div class="eachBox wrap-newsletter clearfix">
		<p class="txt-newsletter">TILMELD DIG VORES NYHEDSBREV OG FÅ GODE TILBUD OG NYHEDER. VI UDSENDER NYHEDSBREVE 1-2 GANGE OM MÅNEDEN!</p>
		<div class="row-ip clearfix">
			<form action="index.php" method="post" class="form-validate">
				<input type="text" placeholder="Indtast din e-mail" class="required validate-email txtInput" name="email">
				<button type="submit" class="btnSubscribe btn2 validate" style="cursor:pointer; border:none;">Tilmeld</button>
				<input type="hidden" name="option" value="com_virtuemart" />
				<input type="hidden" name="controller" value="virtuemart" />
				<input type="hidden" name="task" value="subscribe" />
			</form>
		</div>
	</div>
</div>
<?php }?>



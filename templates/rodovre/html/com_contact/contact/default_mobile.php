<?php
 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.formvalidation');?>
<style>
.invalid {
border-color: red !important;
}
#recaptcha_area input {
    height: auto;
    display: inline;
}
.wrapMap  {
	position: relative;
	padding-bottom: 65.25%;
	padding-top: 30px;
	height: 0;
	overflow: hidden;
}
.wrapMap iframe {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
</style>
<div id="content" class="w-content undepages contact">
	{module Breadcrumbs}
	<div class="eachBox boxContact">
		<h2>Kontakt</h2>
		<div class="clearfix">
			<div class="cont-left">
				{article 9}{introtext}{/article}
			</div>
			<div class="cont-right ">
				<div class="wrap-form-contact ">
					<p> Felter markeret med * skal udfyldes </p>
					<script type="text/javascript">
					 var RecaptchaOptions = {
						theme : 'white',
						lang : 'da',
						custom_translations : { instructions_visual : "Indtast koden" }
					 };
					 </script>
					<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
						<input type="text" placeholder="Navn *" class="required txtInput" name="jform[contact_name]">
						<input type="text" placeholder="Email *" class="required validate-email txtInput" name="jform[contact_email]">
						<input type="text" placeholder="Telefon *" class="required txtInput" name="jform[contact_phone]">
						<textarea placeholder="Din besked" name="jform[contact_message]" class="txtArea"></textarea>
						<?php
						  require_once('recaptchalib.php');
						  $publickey = "6LeznvkSAAAAAFtnSP0wmbHyPrp643iRsMuY9_Zw"; // you got this from the signup page
						  echo recaptcha_get_html($publickey);
						?>
						<div style="height:10px"></div>
						<button type="submit" class="btn2 btnSend validate" style="border:none; cursor:pointer;">Send</button>
						<button type="reset" class="btn2 btnNustil" style="border:none; cursor:pointer;">Nulstil</button>
						<input type="hidden" name="option" value="com_contact" />
						<input type="hidden" name="task" value="contact.submit" />
						<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
						<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
						<?php echo JHtml::_( 'form.token' ); ?>
					</form>
				</div>
			</div>
		</div>
		<div class="wrapMap clearfix"> <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d4470.661793324947!2d12.43221850034413!3d55.92630137101223!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4652478c58c6b30d%3A0x882207eeab2b9c27!2zSGVzc2VscsO4ZHZlaiAyNiwgMjk4MCBLb2trZWRhbCwgxJBhbiBN4bqhY2g!5e0!3m2!1svi!2s!4v1407383222950" width="600" height="400" frameborder="0" style="border:0"></iframe> </div>
	</div>
</div>
<!--End #content-->
<?php return;?>
<?php if(JRequest::getVar('success')){?>
<div class="template">
	<div class="about_page"> {module Breadcrumbs}
		<h2>Kvittering</h2>
		<p>Kære kunde, <br>
			Tak for din henvendelse. Vi vil kontakte dig hurtigst muligt.<br>
			<br>
			Med venlig hilsen<br>
			Scheel-Larsen </p>
	</div>
</div>
<?php } else {?>
<style>
.invalid {
border-color: red !important;
}
#recaptcha_area input {
    height: auto;
    display: inline;
}
</style>
<div class="template">
	<div class="contact_page"> {module Breadcrumbs}
		<h2>Kontakt</h2>
		<div class="w375 fl">
			<div class="w71 fl mr30 mt10"> <img alt="" src="templates/scheellarsen/img/logo2.png"> </div>
			{article 9}{introtext}{/article} </div>
		<div class="w320 fr">
			<p>Felter markeret med * skal udfyldes</p>
			<div class="frm_contact clearfix"> 
				<script type="text/javascript">
                 var RecaptchaOptions = {
                    theme : 'white',
                    lang : 'da',
                    custom_translations : { instructions_visual : "Indtast koden" }
                 };
                 </script>
				<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate">
					<input type="text" placeholder="Navn *" class="required" name="jform[contact_name]">
					<input type="text" placeholder="Email *" class="required validate-email" name="jform[contact_email]">
					<input type="text" placeholder="Telefon *" class="required" name="jform[contact_phone]">
					<textarea placeholder="Din besked" name="jform[contact_message]"></textarea>
					<?php
                      require_once('recaptchalib.php');
                      $publickey = "6LeznvkSAAAAAFtnSP0wmbHyPrp643iRsMuY9_Zw"; // you got this from the signup page
                      echo recaptcha_get_html($publickey);
                    ?>
					<div style="height:10px"></div>
					<button type="submit" class="btn2 btnSend validate" style="border:none; cursor:pointer;">Send</button>
					<button type="reset" class="btn2 btnNustil" style="border:none; cursor:pointer;">Nulstil</button>
					<input type="hidden" name="option" value="com_contact" />
					<input type="hidden" name="task" value="contact.submit" />
					<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
					<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
					<?php echo JHtml::_( 'form.token' ); ?>
				</form>
			</div>
		</div>
		<div class="clear"></div>
		<div class="map clearfix">
			<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d4470.661793324947!2d12.43221850034413!3d55.92630137101223!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4652478c58c6b30d%3A0x882207eeab2b9c27!2zSGVzc2VscsO4ZHZlaiAyNiwgMjk4MCBLb2trZWRhbCwgxJBhbiBN4bqhY2g!5e0!3m2!1svi!2s!4v1407383222950" width="721" height="472" frameborder="0" style="border:0"></iframe>
		</div>
	</div>
</div>
<?php }?>

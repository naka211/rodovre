<?php
 /**
 * @package		Joomla.Site
 * @subpackage	com_contact
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

//Detect mobile
$config =& JFactory::getConfig();
$showPhone = $config->getValue( 'config.show_phone' );
$enablePhone = $config->getValue( 'config.enable_phone' );
require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
if ( ($showPhone || $detect->isMobile()) && ($enablePhone) ) {
    include('default_mobile.php');
    return;
}
//Detect mobile end

JHtml::_('behavior.formvalidation');
if(JRequest::getVar('success')){?>
<div class="template">
    <div class="about_page">
        {module Breadcrumbs}
        <h2>Kvittering</h2>
        <p>Kære kunde, <br>
        Tak for din henvendelse. Vi vil kontakte dig hurtigst muligt.<br>
        <br>
        Med venlig hilsen<br>
        Isenkramshoppen <br>
		<a href="">TIL FORSIDE</a>
		</p>
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
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2249.5332077175503!2d12.455072!3d55.679717000000004!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46525141055c8c89%3A0xd6af26aeaaf88dee!2sR%C3%B8dovre+Isenkram!5e0!3m2!1sen!2s!4v1424747792224" width="721" height="472" frameborder="0" style="border:0"></iframe>
        </div>
    </div>
</div>
<?php }?>

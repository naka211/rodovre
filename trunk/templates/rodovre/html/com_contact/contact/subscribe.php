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
    include('subscribe_mobile.php');
    return;
}
//Detect mobile end

JHtml::_('behavior.formvalidation');
if(JRequest::getVar('success')){?>

<div class="template">
    <div class="about_page">
        <h2>Kvittering</h2>
        Du er hermed tilmeldt vores nyhedsbrev.<br>
        <br>
        Med venlig hilsen<br>
        RØDOVRE ISENKRAM ApS
        </p>
    </div>
</div>
<?php } else { ?>
<div class="template">
    <div class="conditions_page">
        {module Breadcrumbs}
        <!--<h2>NYHEDSBREV TILMELDING</h2>-->
        <div class="newsletter clearfix pt0">
            <div class="w485 fl">
                <p>TILMELD DIG VORES NYHEDSBREV OG FÅ GODE TILBUD OG NYHEDER. <br>
                    VI UDSENDER NYHEDSBREVE 1-2 GANGE OM MÅNEDEN!</p>
            </div>
            <div class="w430 fl mt10">
                <form action="index.php" method="post" class="form-validate">
                    <input type="text" placeholder="Indtast din e-mail" class="required validate-email" name="email">
                    <button type="submit" class="btnSubscribe btn2 fl mt10 validate" style="cursor:pointer; border:none;">Tilmeld</button>
                    <input type="hidden" name="option" value="com_virtuemart" />
                    <input type="hidden" name="controller" value="virtuemart" />
                    <input type="hidden" name="task" value="subscribe" />
                </form>
            </div>
        </div>
    </div>
</div>
<?php }?>

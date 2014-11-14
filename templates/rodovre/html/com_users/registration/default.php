<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.formvalidation');
JHtml::_('behavior.noframes');
?>
<script language="javascript" src="templates/amager/js/jquery.validate.js"></script>
<script language="javascript" src="templates/amager/js/trung.js"></script>
<div id="signup-page">
    <div id="w-signup-page">
    <div class="signup-title">
        <h2>Opret konto</h2>
        <div class="come-back"><a href="#">Tilbage</a></div><!--.come-back-->
    </div><!--.signup-title-->
    <?php if(JRequest::getVar('complete')){?>
		<p style="margin-left:10px; margin-top:10px;">Kære kunde, <br />Din konto er oprettet.<br /><br />
        Med venlig hilsen<br />
        Amager Isenkram
        </p>
        <div class="bnt-login" style="float:none; margin-left:10px;">
        <a href="javascript:void(0);" data-reveal-id="myModal">TIL FORSIDEN</a>
        </div>
	<?php }else {?>
    <form method="post" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" name="registrationForm" class="info-per" id="registrationForm">
        <fieldset>
            <p>Felter markeret med * skal udfyldes (kodeord skal være minimum 4 tegn)</p>
            <h3>Personlig information</h3>
            <div>
                <label>Vælg kundetype *</label>
            </div>
            <div>
                <select name="mwctype" id="choicemaker">
                    <option value="1">Privat</option>
                    <option value="2">Erhverv</option>
                    <option value="3">Offentlig instans</option>
                </select>
            </div>
            
            <div id="w-privat">
            	<div id="companyadd"></div>
                <div>
                  <input value="E-mail *" name="email" type="text" id="email" />
                </div>
                <div id="publicadd"></div>
                <div>
                  <input type="text" value="Fornavn *" name="firstname" id="firstname"  />
                </div>
                <div>
                  <input type="text" value="Efternavn *" name="lastname" id="lastname" />
                </div>
                <div>
                  <input type="text" value="Adresse *" name="address" id="address" />
                </div>
                <div>
                  <input type="text" value="Postnr. *" name="zipcode" id="zipcode" maxlength="4" />
                </div>
                <div>
                  <input type="text" value="By *" name="city" id="city" />
                </div>
                <div>
                  <input type="text" value="Telefon *" name="phone" id="phone" />
                </div>
            </div><!--.w-privat-->
            
            <h3>LOG-IND INFORMATION</h3>
            <div>
                <label>Kodeord (skal være min 4 tegn) *</label>
            </div>
            <div>
                <input type="password" minlength="4" name="password1" id="password1">
            </div>
            <div>
                <label>Bekræft kodeord *</label>
            </div>
            <div>
                <input type="password" name="password2" id="password2">
            </div>
             <div>
                    <label>Bemærk! E-mail bruges til login</label>
                </div>
            <div class="chk2">
                <input type="checkbox" name="newsletter" value="1">
                <p>Ved registering husk tilmelding nyhedsbrev samtidig </p>
            </div>
            <!--<div class="bnt-subs-now n-m-b">-->
                <!--<a href="index2.php">Tilmeld nu</a>-->
                <input type="hidden" name="username" value="" id="username" />
                <input type="hidden" name="name" value="" id="name" />
                <button type="submit" class="validate bnt-subs-now n-m-b" style="border:none; cursor:pointer;"> </button>
                <input type="hidden" name="option" value="com_users" />
                <input type="hidden" name="task" value="registration.register" />
                <?php echo JHtml::_('form.token');?>
            <!--</div>--><!--.bnt-subs-now-->
        </fieldset>
    </form>
    <?php }?>
    </div><!--#w-signup-page-->
</div>

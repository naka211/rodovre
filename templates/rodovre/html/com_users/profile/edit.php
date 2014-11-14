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
//load user_profile plugin language
/*$lang = JFactory::getLanguage();
$lang->load( 'plg_user_profile', JPATH_ADMINISTRATOR );*/
$user = JFactory::getUser();
$user =& JUser::getInstance($user->id);
$mwctypeArr = array(1 => 'Privat', 2 => 'Erhverv', 3 => 'Offentlig instans');
?>
<script language="javascript" src="templates/amager/js/jquery.validate.js"></script>
<script language="javascript">
jQuery(document).ready(function()
{
	jQuery("#overview-page").validate({
		rules: {
			<?php if($user->mwctype == 3){?>
			ean: {
				required: true,
				number: true
			},
			authority: {
				required: true,
			},
			order: {
				required: true,
			},
			person: {
				required: true,
			},
			<?php }?>
			firstname: {
				required: true,
			},
			lastname: {
				required: true,
			},
			password2: {
				equalTo: "#password1",
			},
			address: {
				required: true,
			},
			zipcode: {
				required: true,
				number: true,
				minlength: 4
			},
			city: {
				required: true,
			},
			phone: {
				required: true,
				number: true
			}
		},
		messages: {
			<?php if($user->mwctype == 3){?>
			ean: "",
			authority: "",
			order: "",
			person: "",
			<?php }?>
			firstname: "",
			lastname: "",
			password: {
				required: "",
				minlength: ""
			},
			confirmpassword: {
				required: "",
				equalTo: ""
			},
			address: "",
			zipcode: "",
			city: "",
			phone: ""
		}
	});
	jQuery("#firstname").bind("blur",function(){
		jQuery("#name").val(jQuery("#firstname").val()+' '+jQuery("#lastname").val());
	});
	jQuery("#lastname").bind("blur",function(){
		jQuery("#name").val(jQuery("#firstname").val()+' '+jQuery("#lastname").val());
	});
});// JavaScript Document
</script>
<form id="overview-page" name="f1" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post">
	<fieldset>
		<div class="overview-left">
		 	<div class="bnt-overview active2"><a href="#"></a></div><!--.bnt-overview-->
			<div class="bnt-my-profile"><a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&layout=list'); ?>"></a></div><!--.bnt-my-profile-->
		</div><!--.overview-left-->
		<div class="overview-right">
			<div class="info-persional">
			<h3>Personlig information</h3>
			 <div class="over-info">
				<label>Kundetype:</label><span><?php echo $mwctypeArr[$user->mwctype];?></span><br />
				<label>E-mail: </label><span><a href="<?php echo $user->email;?>"><?php echo $user->email;?></a></span>
			</div><!--.over-info-->
			<?php if($user->mwctype == 2){?>
				<div>
					<label>Firmanavn</label>
					<input type="text" name="company" id="company" value="<?php echo $user->company;?>" />
				</div>
				<div>
					<label>CVR-nr.</label>
					<input type="text" name="cvr" id="cvr" value="<?php echo $user->cvr;?>" />
				</div>
			<?php } else if($user->mwctype == 3){?>
				<div>
					<label>EAN-nr. <span>*</span></label>
					<input type="text" name="ean" id="ean" value="<?php echo $user->ean;?>" />
				</div>
				<div>
					<label>Myndighed/Institution <span>*</span></label>
					<input type="text" name="authority" id="authority" value="<?php echo $user->authority;?>" />
				</div>
				<div>
					<label>Ordre- el. rekvisitionsnr. <span>*</span></label>
					<input type="text" name="order" id="order" value="<?php echo $user->order;?>" />
				</div>
				<div>
					<label>Personreference <span>*</span></label>
					<input type="text" name="person" id="person" value="<?php echo $user->person;?>" />
				</div>
			<?php }?>
			 <div>
				<label>Fornavn <span>*</span></label>
				<input type="text" name="firstname" id="firstname" value="<?php echo $user->firstname;?>" />
			</div>
			 <div>
				<label>Efternavn <span>*</span></label>
				<input type="text" name="lastname" id="lastname" value="<?php echo $user->lastname;?>" />
			</div>
			 <div>
				<label>Adresse<span>*</span></label>
				<input type="text" name="address" id="address" value="<?php echo $user->address;?>" />
			</div>
			 <div>
				<label>Postnr. <span>*</span></label>
				<input type="text" name="zipcode" id="zipcode" value="<?php echo $user->zipcode;?>" />
			</div>
			 <div>
				<label>By <span>*</span></label>
				<input type="text" name="city" id="city" value="<?php echo $user->city;?>" />
			</div>
			 <div>
				<label>Telefon <span>*</span></label>
				<input type="text" name="phone" id="phone" value="<?php echo $user->phone;?>" />
			</div>
			<h3>LOG-IND INFORMATION</h3>
			<div>
				<label>Kodeord <span>*</span></label>
				<input type="password" name="password1" id="password1" value="" />
				<span>(skal være min 4 tegn)</span>
			</div>
			<div>
				<label>Bekræft kodeord <span>*</span></label>
				<input type="password" name="password2" id="password2" value="" />
			</div>
			<div class="m-l-114"><label><span>* </span>Skal udfyldes</label></div>
			<!--<div class="bnt-save">
				<a href="#">Gem</a>-->
				<button type="submit" class="bnt-save" style="border:none; cursor:pointer; clear:both;"> </button>
			<!--</div>--><!--.bnt-save-->
			</div><!--.info-persional-->
		</div><!--.overview-right-->
		<input type="hidden" name="name" value="<?php echo $user->name;?>" id="name" />
		<input type="hidden" name="mwctype" value="<?php echo $user->mwctype;?>" />
		<input type="hidden" name="email" value="<?php echo $user->email;?>" />
		<input type="hidden" name="username" value="<?php echo $user->username;?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="profile.save" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form><!--#overview-page-->
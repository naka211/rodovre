<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if(!class_exists( 'PHPMailer' )) jimport('phpmailer.phpmailer');
if(!class_exists( 'JMailHelper' )) jimport('joomla.mail.helper');
//require_once JPATH_ROOT.'/libraries/phpmailer/phpmailer.php';
//require_once JPATH_ROOT.'/libraries/joomla/mail/helper.php';

class awoMail extends PHPMailer {

	/**
	 * Constructor
	 *
	 */
	function awoMail() {
		$this->CharSet='utf-8';
		
		// phpmailer has an issue using the relative path for it's language files
		$this->SetLanguage('joomla', JPATH_LIBRARIES.'/phpmailer/language/');
		 
		$conf	= JFactory::getConfig();
		
		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$sendmail 	= $conf->get('sendmail');
			$smtpauth 	= $conf->get('smtpauth');
			$smtpuser 	= $conf->get('smtpuser');
			$smtppass  	= $conf->get('smtppass');
			$smtphost 	= $conf->get('smtphost');
			$smtpsecure	= $conf->get('smtpsecure');
			$smtpport	= $conf->get('smtpport');
			$mailfrom 	= $conf->get('mailfrom');
			$fromname 	= $conf->get('fromname');
			$mailer 	= $conf->get('mailer');
		}
		else {
			$sendmail 	= $conf->getValue('config.sendmail');
			$smtpauth 	= $conf->getValue('config.smtpauth');
			$smtpuser 	= $conf->getValue('config.smtpuser');
			$smtppass  	= $conf->getValue('config.smtppass');
			$smtphost 	= $conf->getValue('config.smtphost');
			$smtpsecure	= $conf->getValue('config.smtpsecure');
			$smtpport	= $conf->getValue('config.smtpport');
			$mailfrom 	= $conf->getValue('config.mailfrom');
			$fromname 	= $conf->getValue('config.fromname');
			$mailer 	= $conf->getValue('config.mailer');
		}

		/*
		$sendmail 	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.sendmail');
		$smtpauth 	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.smtpauth');
		$smtpuser 	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.smtpuser');
		$smtppass  	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.smtppass');
		$smtphost 	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.smtphost');
		$smtpsecure	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.smtpsecure');
		$smtpport	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.smtpport');
		$mailfrom 	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.mailfrom');
		$fromname 	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.fromname');
		$mailer 	= $conf->{version_compare( JVERSION, '1.6.0', 'ge' ) ? 'get' : 'getValue'}('config.mailer');
		*/
		
		// Create a JMail object

		// Set default sender
		$this->setSender(array ($mailfrom, $fromname));

		// Default mailer is to use PHP's mail function
		switch ($mailer) {
			case 'smtp' :
				$this->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
				break;
			case 'sendmail' :
				$this->useSendmail($sendmail);
				break;
			default :
				$this->IsMail();
				break;
		}
		 
	}

	/**
	 * @return mixed True if successful, a JError object otherwise
	 */
	function Send()
	{
		if ( ( $this->Mailer == 'mail' ) && ! function_exists('mail') )
		{
			return JError::raiseNotice( 500, JText::_('COM_AWOCOUPON_ERR_MAIL_FUNCTION_DISABLED') );
		}

		@ $result = parent::Send();

		if ($result == false)
		{
			// TODO: Set an appropriate error number
			$result = JError::raiseNotice( 500, JText::_($this->ErrorInfo) );
		}
		return $result;
	}

	/**
	 * Set the E-Mail sender
	 *
	 * @access public
	 * @param array $from E-Mail address and Name of sender
	 * 		<pre>
	 * 			array( [0] => E-Mail Address [1] => Name )
	 * 		</pre>
	 * @return void
	 * @since 1.5
	 */
	function setSender($from)
	{
		// If $from is an array we assume it has an address and a name
		if (is_array($from))
		{
			$this->From 	= JMailHelper::cleanLine( $from[0] );
			$this->Sender 	= JMailHelper::cleanLine( $from[0] );
			$this->FromName = JMailHelper::cleanLine( $from[1] );
		// If it is a string we assume it is just the address
		} elseif (is_string($from)) {
			$this->From = JMailHelper::cleanLine( $from );
		// If it is neither, we throw a warning
		} else {
			JError::raiseWarning( 0, "JMail::  Invalid E-Mail Sender: $from", "JMail::setSender($from)");
		}
	}

	/**
	 * Set the E-Mail subject
	 *
	 * @access public
	 * @param string $subject Subject of the e-mail
	 * @return void
	 * @since 1.5
	 */
	function setSubject($subject) {
		$this->Subject = JMailHelper::cleanLine( $subject );
	}

	/**
	 * Set the E-Mail body
	 *
	 * @access public
	 * @param string $content Body of the e-mail
	 * @return void
	 * @since 1.5
	 */
	function setBody($content)
	{
		/*
		 * Filter the Body
		 * TODO: Check for XSS
		 */
		$this->Body = JMailHelper::cleanText( $content );
		$this->Body = $this->fixImagePath($this->Body);
		$this->Body = $this->fixRelativeLinks($this->Body);
	}

	function fixImagePath($str){
		$siteUrl = JURI::root();
		$str = preg_replace('/src=\"(?!cid)(?!http).*/Uis', "src=\"".$siteUrl, $str);
		$str = str_replace("url(components", "url(".$siteUrl."components", $str);
		return $str;
	}
	
	function fixRelativeLinks($str){
		$str = str_replace("href=\"..", "href=\"", $str);
		$str = preg_replace('/href=\"(?!cid)(?!http).*/Uis', "href=\"".JURI::root(), $str);
		return $str;
	}


	/**
	 * Add recipients to the email
	 *
	 * @access public
	 * @param mixed $recipient Either a string or array of strings [e-mail address(es)]
	 * @return void
	 * @since 1.5
	 */
	function addRecipient($recipient)
	{
		// If the recipient is an aray, add each recipient... otherwise just add the one
		if (is_array($recipient))
		{
			foreach ($recipient as $to) {
				$to = JMailHelper::cleanLine( $to );
				$this->AddAddress($to);
			}
		} else {
			$recipient = JMailHelper::cleanLine( $recipient );
			$this->AddAddress($recipient);
		}
	}

	/**
	 * Add carbon copy recipients to the email
	 *
	 * @access public
	 * @param mixed $cc Either a string or array of strings [e-mail address(es)]
	 * @return void
	 * @since 1.5
	 */
	function addCC($cc,$name='')
	{
		//If the carbon copy recipient is an aray, add each recipient... otherwise just add the one
		if (isset ($cc))
		{
			if (is_array($cc)) {
				foreach ($cc as $to) {
					$to = JMailHelper::cleanLine( $to );
					parent::AddCC($to);
				}
			} else {
				$cc = JMailHelper::cleanLine( $cc );
				parent::AddCC($cc);
			}
		}
	}

	/**
	 * Add blind carbon copy recipients to the email
	 *
	 * @access public
	 * @param mixed $cc Either a string or array of strings [e-mail address(es)]
	 * @return void
	 * @since 1.5
	 */
	function addBCC($bcc,$name='')
	{
		// If the blind carbon copy recipient is an aray, add each recipient... otherwise just add the one
		if (isset ($bcc))
		{
			if (is_array($bcc)) {
				foreach ($bcc as $to) {
					$to = JMailHelper::cleanLine( $to );
					parent::AddBCC($to);
				}
			} else {
				$bcc = JMailHelper::cleanLine( $bcc );
				parent::AddBCC($bcc);
			}
		}
	}

	/**
	 * Add Reply to e-mail address(es) to the e-mail
	 *
	 * @access public
	 * @param array $reply Either an array or multi-array of form
	 * 		<pre>
	 * 			array( [0] => E-Mail Address [1] => Name )
	 * 		</pre>
	 * @return void
	 * @since 1.5
	 */
	function addReplyTo($replyto,$name='')
	{
		// Take care of reply email addresses
		if (is_array($replyto[0]))
		{
			foreach ($replyto as $to) {
				$to0 = JMailHelper::cleanLine( $to[0] );
				$to1 = JMailHelper::cleanLine( $to[1] );
				parent::AddReplyTo($to0, $to1);
			}
		} else {
			$replyto0 = JMailHelper::cleanLine( $replyto[0] );
			$replyto1 = JMailHelper::cleanLine( $replyto[1] );
			parent::AddReplyTo($replyto0, $replyto1);
		}
	}

	/**
	 * Use sendmail for sending the e-mail
	 *
	 * @access public
	 * @param string $sendmail Path to sendmail [optional]
	 * @return boolean True on success
	 * @since 1.5
	 */
	function useSendmail($sendmail = null)
	{
		$this->Sendmail = $sendmail;

		if (!empty ($this->Sendmail)) {
			$this->IsSendmail();
			return true;
		} else {
			$this->IsMail();
			return false;
		}
	}

	/**
	 * Use SMTP for sending the e-mail
	 *
	 * @access public
	 * @param string $auth SMTP Authentication [optional]
	 * @param string $host SMTP Host [optional]
	 * @param string $user SMTP Username [optional]
	 * @param string $pass SMTP Password [optional]
	 * @param string $secure SMTP Secure ssl,tls [optinal]
	 * @param string $port SMTP Port [optional]
	 * @return boolean True on success
	 * @since 1.5
	 */
	function useSMTP($auth = null, $host = null, $user = null, $pass = null,$secure = null, $port = 25)
	{
		$this->SMTPAuth = $auth;
		$this->Host 	= $host;
		$this->Username = $user;
		$this->Password = $pass;
		$this->Port     = $port;
		if ($secure == 'ssl' || $secure == 'tls') {
			$this->SMTPSecure = $secure;
		}

		if ($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null) {
			$this->IsSMTP();
		}
	}
}

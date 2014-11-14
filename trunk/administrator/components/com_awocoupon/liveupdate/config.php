<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_awocoupon';
	var $_extensionTitle		= 'AwoCoupon Pro 2';
	var $_updateURL				= 'http://awodev.com/sites/default/files/extstatus/awocoupon2.xml';
	var $_requiresAuthorization	= false;
	var $_versionStrategy		= 'different';
	
	function __construct() {
		parent::__construct();
	}
}

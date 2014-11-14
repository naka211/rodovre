<?php
/**
*
* Base controller Frontend
*
* @package		VirtueMart
* @subpackage
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: virtuemart.php 5310 2012-01-23 21:34:19Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * VirtueMart Component Controller
 *
 * @package		VirtueMart
 */
class VirtueMartControllerVirtuemart extends JController
{

	function __construct() {
		parent::__construct();
		if (VmConfig::get('shop_is_offline') == '1') {
		    JRequest::setVar( 'layout', 'off_line' );
	    }
	    /*else {
		    JRequest::setVar( 'layout', 'default' );
	    }*/
	}

	/**
	 * Override of display to prevent caching
	 *
	 * @return  JController  A JController object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false){

		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewName = JRequest::getCmd('view', $this->default_view);
		$viewLayout = JRequest::getVar('layout', 'default');

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));
		$view->assignRef('document', $document);

		$view->display();

		return $this;
	}

    public function set_session(){
        $session = JFactory::getSession();
        $session->set('notify', 1);
        die(true);
    }
    
    function subscribe(){
        require_once "Mailchimp.php";
        
        $email = JRequest::getVar('email');
        
        $apikey  ='3a6a77a681eb1df592510fba6a453e95-us9';
        $mailchimp  = new Mailchimp($apikey);
        $list_id ='95d8af127e';
        $data  = array('email'=>$email);
        $result = $mailchimp->mailchimp_subcriber($data, $mailchimp, $list_id);
   
        $this->setRedirect(JRoute::_("index.php?option=com_contact&view=contact&id=1&success=1&layout=subscribe&Itemid=133"));
    }

}
 //pure php no closing tag

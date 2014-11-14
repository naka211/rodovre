<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');


if(version_compare( JVERSION, '3.0.0', 'ge' )) { class AwoCouponControllerConnect extends JControllerForm {} }
else {
	jimport('joomla.application.component.controller');
	class AwoCouponControllerConnect extends JController {}
}

class AwoCouponController extends AwoCouponControllerConnect {

	function __construct() {
		parent::__construct();
		
		$this->registerDefaultTask('processor');
		

		// Register Extra task
		$this->registerTask( 'addcoupon',		'editcoupon' );
		$this->registerTask( 'applyconfig',		'saveconfig' );

	}
	public function display($cachable = false, $urlparams = false) {
		JRequest::setVar('view', JRequest::getCmd('view', 'dashboard'));
		parent::display($cachable);
	}

	
	function processor() {
		
		if(!isset($this->task)) @$this->task=$this->_task; # joomla 1.5 compatibility

		if(substr($this->task,0,4)=='SAVE') {
			$model = substr($this->task,4);
			$this->processor_save($model);
		}
		elseif(substr($this->task,0,6)=='CANCEL') {
			$model = substr($this->task,6);
			$this->processor_cancel($model);
		}
		elseif(substr($this->task,0,3)=='ADD') {
			$model = substr($this->task,3);
			JRequest::setVar( 'cid', array(0) );
			$this->processor_edit($model);
		}
		elseif(substr($this->task,0,4)=='EDIT') {
			$model = substr($this->task,4);
			$this->processor_edit($model);
		}
		elseif(substr($this->task,0,6)=='REMOVE') {
			$model = substr($this->task,6);
			$this->processor_remove($model);
		}
		elseif(substr($this->task,0,7)=='PUBLISH') {
			$model = substr($this->task,7);
			$this->processor_publish($model);
		}
		elseif(substr($this->task,0,9)=='UNPUBLISH') {
			$model = substr($this->task,9);
			$this->processor_unpublish($model);
		}
		else $this->display();
	}
	function processor_save($modelname) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel($modelname);

		if ( $model->store($post) ) {
			$this->setRedirect('index.php?option=com_awocoupon&view='.$modelname, JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ));
		} else {
			return $this->execute('edit'.$modelname);
		}
	}
	function processor_cancel($modelname) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_awocoupon&view='.$modelname );
	}
	function processor_edit( $modelname ) {
	
		JRequest::setVar( 'view', $modelname );
		JRequest::setVar( 'layout', 'edit' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model 	= $this->getModel($modelname);
		parent::display();
	}
	function processor_remove($modelname) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel($modelname);

			if(!$model->delete($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view='.$modelname, count( $cid ).' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED') );
			}
		}
	}
	function processor_publish($modelname) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel($modelname);

			if(!$model->publish($cid, 1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view='.$modelname, JText::_( 'COM_AWOCOUPON_MSG_ITEMS_PUBLISHED') );
			}
		}
	}
	function processor_unpublish($modelname) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel($modelname);

			if(!$model->publish($cid, -1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view='.$modelname, JText::_( 'COM_AWOCOUPON_MSG_ITEMS_UNPUBLISHED') );
			}
		}
	}

	

	
	
/*
savegiftcert
cancelgiftcert
addgiftcert
editgiftcert
removegiftcert
publishgiftcert
unpublishgiftcert
savehistory
cancelhistory
addhistory
cancelgiftcertcode
addgiftcertcode
removegiftcertcode
publishgiftcertcode
unpublishgiftcertcode
saveprofile
cancelprofile
editprofile
removeprofile
addprofile

*/

	
	
	
	function savecoupon() {
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('coupon');

		if ( $model->store($post) ) {

			$this->setRedirect('index.php?option=com_awocoupon&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ));
		} else {
			return $this->execute('editcoupon');
		}


	}
	function applycoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post 			= JRequest::get( 'post' );
		$model = $this->getModel('coupon');

		if($model->store($post)) JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ), 'message');

		return $this->execute('editcoupon');
	}
	function cancelcoupon() {	
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons' );
	}
	function editcoupon( ) {
	
		JRequest::setVar( 'view', 'coupon' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model 	= $this->getModel('coupon');
		parent::display();
	}
	function removecoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->delete($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons', count( $cid ).' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED') );
			}
		}
	}
	function publishcoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->publish($cid, 1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_PUBLISHED') );
			}
		}
	}
	function unpublishcoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->publish($cid, -1)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_ITEMS_UNPUBLISHED') );
			}
		}
	}
	function duplicatecoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) != 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupon');
			$rtn = $model->duplicatecoupon(current($cid));

			if($rtn===false) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons', JText::_('COM_AWOCOUPON_CP_COUPON_CODE').': '.$rtn->coupon_code );
			}
		}
		$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons');
	}
	
	
	function generatecouponform( ) {
		JRequest::setVar( 'view', 'coupon' );
		JRequest::setVar( 'layout', 'generate' );
		JRequest::setVar( 'hidemainmenu', 1 );
		$model 	= $this->getModel('coupon');
		parent::display();
	}
	function generatecoupons() {		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('coupon');

		if ( $model->generatecoupons($post) ) {
			$this->setRedirect('index.php?option=com_awocoupon&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ));
		} else {
			$this->setRedirect('index.php?option=com_awocoupon&view=coupon&layout=generate');
		}

	}

	
	function assetorderupparent() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('assets');

		if ( $model->order_up($post) ) {
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_GBL_ERROR' ), 'error');
		}
		$this->setRedirect('index.php?option=com_awocoupon&view=assets&type=parent');
	}
	function assetorderdownparent() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$post = JRequest::get( 'post' );
		$model = $this->getModel('assets');

		if ( $model->order_down($post) ) {
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_GBL_ERROR' ), 'error');
		}
		$this->setRedirect('index.php?option=com_awocoupon&view=assets&type=parent');
	}



	
	function saveimport() {
		header('Content-type: text/html; charset=utf-8');

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$data = array();
		$file = $_FILES;
		
		$exclude_first_row 	= JRequest::getVar( 'exclude_first_row', '', 'post' );
		$store_none_errors 	= JRequest::getVar( 'store_none_errors', '', 'post' );

		if(strtolower(substr($file['file']['name'],-4))=='.csv') {
			ini_set('auto_detect_line_endings',TRUE); //needed for mac users
			if (($handle = fopen($file['file']['tmp_name'], "r")) !== FALSE) {
				require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
				$params = new awoParams();
				$delimiter = $params->get('csvDelimiter', ',') ;
				
				while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) $data[] = $row;
				fclose($handle);
			}
		}
		if(!empty($exclude_first_row)) array_shift($data);
		
		if(empty($data)) {
			JFactory::getApplication()->enqueueMessage('Empty Import File', 'error');
			$this->setRedirect('index.php?option=com_awocoupon&view=import'.(empty($exclude_first_row) ? '&exclude_first_row=' : '').(empty($store_none_errors) ? '&store_none_errors=' : ''));
		} else {
		
			$model = $this->getModel('import');
			$errors = $model->store($data,$store_none_errors);
			
			if(empty($errors)) {
				$this->setRedirect('index.php?option=com_awocoupon&view=coupons', JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ));
			} else {
				foreach($errors as $id=>$errarray) {
					$errText = '<br /><div>ID: '.$id.'<hr /></div>';
					foreach($errarray as $err) $errText .= '<div style="padding-left:20px;">-- '.$err.'</div>';
					JFactory::getApplication()->enqueueMessage($errText, 'error');
				}
				$msg = empty($store_none_errors) ? '' : JText::_( 'COM_AWOCOUPON_IMP_SAVED_NO_ERRS');
				$this->setRedirect('index.php?option=com_awocoupon&view=import'.(empty($exclude_first_row) ? '&exclude_first_row=' : '').(empty($store_none_errors) ? '&store_none_errors=' : ''), $msg );
			}
		}


	}
	function cancelimport() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$this->setRedirect( 'index.php?option=com_awocoupon&view=coupons' );
	}
	
	
	
	
	
	
	
	
	function viewinstallation() { $this->setRedirect('index.php?option=com_awocoupon&view=installation'); }
	function saveinstallation() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1 || (count($cid)==1 && empty($cid[0]))) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('installation');

			if(!$model->reinstall($cid)) return $this->execute('viewinstallation');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=installation' );
			}
		}
	}
	function removeinstallation() {
		// Check for request forgeries
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'get', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1 || (count($cid)==1 && empty($cid[0]))) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('installation');

			if(!$model->uninstall($cid)) return $this->execute('viewinstallation');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=installation' );
			}
		}
	}
	
	function saveconfig() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$task		= JRequest::getVar('task');

		//Sanitize
		$post 			= JRequest::get( 'post' );
		$post['option'] = 'com_awocoupon';

		$model = $this->getModel('config');

		if ( $model->store($post) ) {
			switch ($task) {
				case 'applyconfig' :
					JRequest::setVar( 'hidemainmenu', 1 );
					$link = 'index.php?option=com_awocoupon&view=config';
					break;

				default :
					$link = 'index.php?option=com_awocoupon';
					break;
			}
			$msg = JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' );
		} else {
			$msg = JText::_( 'COM_AWOCOUPON_GBL_ERROR' );
			JError::raiseError( 500, $model->getError() );
			$link 	= 'index.php?option=com_awocoupon&view=config';
		}

		$this->setRedirect($link, $msg);
	}

	function cancelconfig() { $this->setRedirect('index.php?option=com_awocoupon'); }
	
	function publishplugin() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getInt( 'cid', -1, 'post' );
		$cid2 	= (int) JRequest::getInt( 'cid2', -1, 'post' );
		if(($cid!=0 && $cid!=1) || $cid2<1) {
			JFactory::getApplication()->enqueueMessage('COM_AWOCOUPON_GBL_ERROR', 'error');
			$this->setRedirect('index.php?option=com_awocoupon');
			return;
		}

		$model = $this->getModel('dashboard');

		if(!$model->publishplugin($cid,$cid2)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
		else {
			$this->setRedirect( 'index.php?option=com_awocoupon');
		}
	}	
	
	function installplugin() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('dashboard');


		$language = JFactory::getLanguage();
		$language->load('com_installer');

		if(!awoLibrary::installplugin(AWOCOUPON_ESTORE)) 
			JFactory::getApplication()->enqueueMessage( version_compare( JVERSION, '1.6.0', 'ge' ) 
												? JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_PLUGIN'))
												: JText::sprintf('INSTALLEXT', JText::_('plugin'), JText::_('Error'))
										);
		else 
			JFactory::getApplication()->enqueueMessage( version_compare( JVERSION, '1.6.0', 'ge' ) 
										? JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_PLUGIN'))
										: JText::sprintf('INSTALLEXT', JText::_('plugin'), JText::_('Success'))
								);


		$this->setRedirect( 'index.php?option=com_awocoupon');
	}	
	function g_installplugin() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$model = $this->getModel('dashboard');
		$q = substr(JRequest::getVar( 'cid' ),0,-10);
		if(!empty($q)) {
			$plg = array(
					'dir'=>JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/estore/'.AWOCOUPON_ESTORE.'/extensions/plugins/'.$q,
					'name'=>'awocoupon',
					'group'=>$q,
				);

			if(!empty($plg)) {

				$language = JFactory::getLanguage();
				$language->load('com_installer');

				if(!awoLibrary::installpluginrun($plg)) 
					JFactory::getApplication()->enqueueMessage( version_compare( JVERSION, '1.6.0', 'ge' ) 
														? JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_PLUGIN'))
														: JText::sprintf('INSTALLEXT', JText::_('plugin'), JText::_('Error'))
												);
				else 
					JFactory::getApplication()->enqueueMessage( version_compare( JVERSION, '1.6.0', 'ge' ) 
												? JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_PLUGIN'))
												: JText::sprintf('INSTALLEXT', JText::_('plugin'), JText::_('Success'))
										);

			}
		}
		$this->setRedirect( 'index.php?option=com_awocoupon');
	}	
	
	
	
	
	
	
	
	function processreport( ) {
	
		//JRequest::setVar( 'view', 'coupon' );
		//JRequest::setVar( 'hidemainmenu', 1 );
		$model 	= $this->getModel('reports');
		parent::display();
	}
	
	
	
	function licenseactivate() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$model = $this->getModel('license');

//		if ( $model->activate() ) {
			$this->setRedirect('index.php?option=com_awocoupon', JText::_( 'COM_AWOCOUPON_LI_LICENSE_ACTIVATED' ));
//		} else {
//			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_LI_ERROR_ACTIVATING_LICENSE' ), 'error');
//			$this->setRedirect('index.php?option=com_awocoupon&view=license');
//		}
	}
	function licenseupdateexpired() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		//Sanitize
		$model = $this->getModel('license');

//		if ( $model->check() ) {
			$this->setRedirect('index.php?option=com_awocoupon', JText::_( 'COM_AWOCOUPON_LI_LICENSE_ACTIVATED' ));
//		} else {
//			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_LI_ERROR_ACTIVATING_LICENSE' ), 'error');
//			$this->setRedirect('index.php?option=com_awocoupon&view=license');
//		}
	}
	function licenseupdatelocalkey() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$post = JRequest::get( 'post' );
		//Sanitize
		$model = $this->getModel('license');

//		if ( $model->update_localkey($_POST['license'],$_POST['local_key']) ) {
			$this->setRedirect('index.php?option=com_awocoupon', JText::_( 'COM_AWOCOUPON_LI_LOCAL_KEY_UPDATED' ));
//		} else {
//			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_LI_ERROR_LOCAL_KEY' ), 'error');
//			$this->setRedirect('index.php?option=com_awocoupon&view=license');
//		}
	}
	function licensedelete() {
		// Check for request forgeries
		
		$model = $this->getModel('license');

		if(!$model->uninstall()) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
//		else {
//			$this->setRedirect( 'index.php?option=com_awocoupon&view=license', JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED') );
//		}
	}
	
	
	
	
	
		
	function savegiftcertcode() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$data = array();
		$file = $_FILES;
		
		$product_id 	= (int)JRequest::getVar( 'product_id', 0, 'post' );
		$exclude_first_row 	= JRequest::getVar( 'exclude_first_row', '', 'post' );
		$store_none_errors 	= JRequest::getVar( 'store_none_errors', '', 'post' );
		
		if(empty($product_id)) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_CP_PRODUCT').': '.JText::_('COM_AWOCOUPON_ERR_SELECT_ITEM'), 'error');
			$this->setRedirect('index.php?option=com_awocoupon&view=giftcertcode'.(empty($exclude_first_row) ? '&exclude_first_row=' : '').(empty($store_none_errors) ? '&store_none_errors=' : ''));
			return;
		}

		if(strtolower(substr($file['file']['name'],-4))=='.csv') {
			ini_set('auto_detect_line_endings',TRUE); //needed for mac users
			if (($handle = fopen($file['file']['tmp_name'], "r")) !== FALSE) {
				require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awoparams.php';
				$params = new awoParams();
				$delimiter = $params->get('csvDelimiter', ',') ;

				while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
					if(count($row)>3) $row = array_slice($row,0,3);
					$data[] = $row;
				}
				fclose($handle);
			}
		}
		if(!empty($exclude_first_row)) array_shift($data);
		
		if(empty($data)) {
			JFactory::getApplication()->enqueueMessage('Empty Import File', 'error');
			$this->setRedirect('index.php?option=com_awocoupon&view=giftcertcode'.(empty($exclude_first_row) ? '&exclude_first_row=' : '').(empty($store_none_errors) ? '&store_none_errors=' : ''));
		} else {
		
			$model = $this->getModel('giftcertcode');
			$errors = $model->store(array('product_id'=>$product_id,'data'=>$data,'store_none_errors'=>$store_none_errors));
			
			if(empty($errors)) {
				$this->setRedirect('index.php?option=com_awocoupon&view=giftcertcode', JText::_( 'COM_AWOCOUPON_MSG_DATA_SAVED' ));
			} else {
				foreach($errors as $id=>$errarray) {
					$errText = '<br /><div>ID: '.$id.'<hr /></div>';
					foreach($errarray as $err) $errText .= '<div style="padding-left:20px;">-- '.$err.'</div>';
					JFactory::getApplication()->enqueueMessage($errText, 'error');
				}
				$msg = empty($store_none_errors) ? '' : JText::_( 'COM_AWOCOUPON_IMP_SAVED_NO_ERRS');
				$this->setRedirect('index.php?option=com_awocoupon&view=giftcertcode'.(empty($exclude_first_row) ? '&exclude_first_row=' : '').(empty($store_none_errors) ? '&store_none_errors=' : ''), $msg );
			}
		}


	}

	
	
		


	
	
	
	function defaultprofile() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) != 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('profile');

			if(!$model->makedefault($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
		}
		$this->setRedirect( 'index.php?option=com_awocoupon&view=profile' );
	}
	function duplicateprofile() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) != 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('profile');

			if(!$model->duplicate($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=profile', count( $cid ).' '.JText::_('COM_AWOCOUPON_MSG_DATA_SAVED') );
			}
		}
		$this->setRedirect( 'index.php?option=com_awocoupon&view=profile');
	}
	function previewprofileEdit() {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
		$profile = array();
		
		$get = JRequest::get( 'get' );
			
		$profile['image'] = $get['image'];
		$profile['message_type'] = 'html';
		if(empty($profile['image'])) exit;
		
		list($x1,$x2,$x3,$x4,$x5,$x6) = explode('|',$get['code']);
		$profile['coupon_code_config'] = array('align'=>$x1,'pad'=>$x2,'y'=>$x3,'font'=>$x4,'size'=>$x5,'color'=>$x6,);
		list($x1,$x2,$x3,$x4,$x5,$x6) = explode('|',$get['value']);
		$profile['coupon_value_config'] = array('align'=>$x1,'pad'=>$x2,'y'=>$x3,'font'=>$x4,'size'=>$x5,'color'=>$x6,);
		
		if(!empty($get['expiration'])) {
			list($x1,$x2,$x3,$x4,$x5,$x6,$x7) = explode('|',$get['expiration']);
			$profile['expiration_config'] = array('text'=>$x1,'align'=>$x2,'pad'=>$x3,'y'=>$x4,'font'=>$x5,'size'=>$x6,'color'=>$x7,);
		}
		if(!empty($get['freetext1'])) {
			list($x1,$x2,$x3,$x4,$x5,$x6,$x7) = explode('|',$get['freetext1']);
			$profile['freetext1_config'] = array('text'=>$x1,'align'=>$x2,'pad'=>$x3,'y'=>$x4,'font'=>$x5,'size'=>$x6,'color'=>$x7,);
		}
		if(!empty($get['freetext2'])) {
			list($x1,$x2,$x3,$x4,$x5,$x6,$x7) = explode('|',$get['freetext2']);
			$profile['freetext2_config'] = array('text'=>$x1,'align'=>$x2,'pad'=>$x3,'y'=>$x4,'font'=>$x5,'size'=>$x6,'color'=>$x7,);
		}
		if(!empty($get['freetext3'])) {
			list($x1,$x2,$x3,$x4,$x5,$x6,$x7) = explode('|',$get['freetext3']);
			$profile['freetext3_config'] = array('text'=>$x1,'align'=>$x2,'pad'=>$x3,'y'=>$x4,'font'=>$x5,'size'=>$x6,'color'=>$x7,);
		}
		
		if(!empty($get['imgplugin']) && is_array($get['imgplugin'])) {
			foreach($get['imgplugin'] as $k=>$r) {foreach($r as $k2=>$r2) {
				list($x1,$x2,$x3,$x4,$x5,$x6,$x7) = explode('|',$r2);
				$profile['imgplugin'][$k][$k2] = array('text'=>$x1,'align'=>$x2,'pad'=>$x3,'padding'=>$x3,'y'=>$x4,'font'=>$x5,'size'=>$x6,'color'=>$x7,);
			}}
		}

		$image = awoLibrary::writeToImage('ABSIE@SD12bSeA','25.00 USD',1262304000,'screen',$profile);
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Content-type: image/png');
		if($image === false) echo 'error';
		else {
			imagepng($image);					// save image to file
			imagedestroy($image);				// destroy resource
		}
		exit;
	}
	function previewprofile() {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
			
		$profile_id		= (int)JRequest::getVar( 'cid',0 );
		$image = awoLibrary::writeToImage('ABSIE@SD12bSeA','25.00 USD',1262304000,'screen',null,$profile_id);
		if($image === false) exit;
		header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Content-type: image/png');
		if($image === false) echo 'error';
		else {
			imagepng($image);					// save image to file
			imagedestroy($image);				// destroy resource
		}
		exit;
	}
	

	
	
	function removehistorycoupon() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('history');

			if(!$model->delete($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=history&layout=default', count( $cid ).' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED') );
			}
		}
		$this->setRedirect( 'index.php?option=com_awocoupon&view=history&layout=default' );
	}
	function removehistorygift() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('coupons');

			if(!$model->delete($cid)) JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=history&layout=gift', count( $cid ).' '.JText::_('COM_AWOCOUPON_MSG_ITEMS_DELETED') );
			}
		}
		$this->setRedirect( 'index.php?option=com_awocoupon&view=history&layout=gift' );
	}
	function resend_giftcert() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) != 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_GBL_ERROR' ), 'error');
		} else {
	
			$model = $this->getModel('history');

			if(!$model->resend_giftcert(current($cid))) {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=history&layout=order' );
			}
			else {
				$this->setRedirect( 'index.php?option=com_awocoupon&view=history&layout=order', '1 '.JText::_('COM_AWOCOUPON_MSG_ITEMS_SENT') );
			}
		}
	}	
	
	function ajax_elements() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';
		$q = JRequest::getVar( 'term' );
		if(empty($q) || strlen($q)<2) exit;

		$type = JRequest::getVar( 'type' );
		
		$result = array();
		$dbresults = array();
		switch($type) {
			case 'product': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct'),null,$q,4); break; //AwocouponEstoreHelper::getEStoreProduct(null,$q,25); break;
			case 'productgift': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProductNotGift'),null,$q,25); break;
			case 'category': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreCategory'),null,$q,25); break;
			case 'manufacturer': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreManufacturer'),null,$q,25); break;
			case 'vendor': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreVendor'),null,$q,25); break;
			case 'shipping': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShipping'),null,$q,25); break;
			case 'user': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreUser'),null,$q,25); break;
			case 'usergroup': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShopperGroup'),null,$q,25); break;
			case 'parent':
				$db = JFactory::getDBO();
				$q = $db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $q ) ), true ).'%', false );
				$sql = 'SELECT id,coupon_code AS label
						  FROM #__awocoupon
						 WHERE estore="'.AWOCOUPON_ESTORE.'" AND published=1 AND function_type NOT IN ("parent","giftcert") AND LOWER(coupon_code) LIKE '.$q.' ORDER BY label,id LIMIT 25';
				$db->setQuery($sql);
				$dbresults = $db->loadObjectList();
				break;
			case 'coupons_noauto':
				$db = JFactory::getDBO();
				$q = $db->Quote( '%'.awolibrary::dbEscape( trim(JString::strtolower( $q ) ), true ).'%', false );
				$sql = 'SELECT c.id,c.coupon_code AS label
						  FROM #__awocoupon c
						  LEFT JOIN #__awocoupon_auto a ON a.coupon_id=c.id
						 WHERE c.estore="'.AWOCOUPON_ESTORE.'" AND c.published=1 AND a.id IS NULL AND LOWER(c.coupon_code) LIKE '.$q.' ORDER BY label,id LIMIT 25';
				$db->setQuery($sql);
				$dbresults = $db->loadObjectList();
				break;
		}
		if(!empty($dbresults)) {
			foreach($dbresults as $row) array_push($result, array("id"=>$row->id, "label"=>$row->label, "value" => strip_tags($row->label)));
		}

		echo json_encode($result);
		exit;	
	}
	function ajax_elements_all() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';

		$type = JRequest::getVar( 'type' );
		
		$result = array();
		$dbresults = array();
		switch($type) {
			case 'product': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct')); break;
			case 'category': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreCategory')); break;
			case 'manufacturer': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreManufacturer')); break;
			case 'vendor': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreVendor')); break;
			case 'shipping': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShipping')); break;
			case 'user': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreUser')); break;
			case 'usergroup': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShopperGroup')); break;
			case 'parent':
				$db = JFactory::getDBO();
				$sql = 'SELECT id,coupon_code AS label
						  FROM #__awocoupon
						 WHERE estore="'.AWOCOUPON_ESTORE.'" AND published=1 AND function_type NOT IN ("parent","giftcert") ORDER BY label,id';
				$db->setQuery($sql);
				$dbresults = $db->loadObjectList();
				break;
		}
		if(!empty($dbresults)) {
			foreach($dbresults as $row) array_push($result, array("id"=>$row->id, "label"=>$row->label, "value" => strip_tags($row->label)));
		}
		
		echo json_encode($result);
		exit;
	}



	function ajax_elements_grid() {
		if (!class_exists( AWOCOUPON_ESTOREHELPER )) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/helper.php';

		$type = JRequest::getVar( 'type' );
		$cur_page = (int)JRequest::getVar( 'cur_page',1 );
		$records_per_page = (int)JRequest::getVar( 'records_per_page',10);
		$sortBy = JRequest::getVar( 'sortBy','id' );
		$dir = strtolower(JRequest::getVar( 'dir','asc' ));
		if($dir!='asc' && $dir!='desc') $dir = 'asc';
		$limitstart = max(0,($cur_page-1)*$records_per_page);
		
		$result = array();
		$dbresults = array();
		switch($type) {
			case 'product': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreProduct'),null,null,$records_per_page,true,$limitstart,$sortBy,$dir); break;
			case 'category': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreCategory'),null,null,$records_per_page,$limitstart,$sortBy,$dir); break;
			case 'manufacturer': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreManufacturer'),null,null,$records_per_page,$limitstart,$sortBy,$dir); break;
			case 'vendor': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreVendor'),null,null,$records_per_page,$limitstart,$sortBy,$dir); break;
			case 'shipping': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShipping'),null,null,$records_per_page,$limitstart,$sortBy,$dir); break;
			case 'user': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreUser'),null,null,$records_per_page,$limitstart,$sortBy,$dir); break;
			case 'usergroup': $dbresults = call_user_func(array(AWOCOUPON_ESTOREHELPER,'getEStoreShopperGroup'),null,null,$records_per_page,$limitstart,$sortBy,$dir); break;
			case 'parent':
				$db = JFactory::getDBO();
				$sql = 'SELECT id,coupon_code AS label
						  FROM #__awocoupon
						 WHERE estore="'.AWOCOUPON_ESTORE.'" AND published=1 AND function_type NOT IN ("parent","giftcert")
						 ORDER BY '.(empty($sortBy) ? 'label,id' : $sortBy).' '.(!empty($dir) ? $dir : '').'
						'.(!empty($records_per_page) ? ' LIMIT '.(!empty($limitstart) ? $limitstart : '').' '.(int)$records_per_page.' ':'');
						 
				$db->setQuery($sql);
				$dbresults = $db->loadObjectList();
				break;
		}
		if(!empty($dbresults)) {
			$db = JFactory::getDBO();
			$db->setQuery('SELECT FOUND_ROWS()');
			$totalrecords = $db->loadResult();

			$result = array(
				'totalRecords'=>$totalrecords,
				'curPage'=>$cur_page,
				'data'=>array(),
			);
			foreach($dbresults as $r) $result['data'][] = array_values((array)$r);
		}
		
		echo json_encode($result);
		exit;
	}

	
	function ajax_generate_coupon_code() {
		require_once JPATH_ADMINISTRATOR.'/components/com_awocoupon/helpers/awolibrary.php';
		echo awoLibrary::generate_coupon_code(JRequest::getVar( 'estore' ));
		exit;
	}
	
	
	
	
	function exportreports() {
		
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$post = JRequest::get( 'post' );

		$model = $this->getModel('reports');
		$file = $model->export($post);

		if(empty($file)) {
			JFactory::getApplication()->enqueueMessage($model->getError(), 'error');
			$report_type = JRequest::getVar('report_type','default','post');
			$this->setRedirect( 'index.php?option=com_awocoupon&view=reports&report_type='.$report_type);
		}
		else {
			$filename = JRequest::getVar('filename','file.csv','post');
			
			// required for IE, otherwise Content-disposition is ignored
			if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

			//  default: $ctype="application/force-download";
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers 
			header("Content-Type: application/vnd.ms-excel");
			header("Content-Disposition: attachment; filename=\"".$filename."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".strlen($file));
			echo $file;
			exit();
		}
	}
	
	function couponsautoorderup() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('couponsauto');
			$model->move(-1,current($cid));
		}
		
		$this->setRedirect( 'index.php?option=com_awocoupon&view=couponsauto' );
		
		
	}
	function couponsautoorderdown() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('couponsauto');
			$model->move(1,current($cid));
		}
		
		$this->setRedirect( 'index.php?option=com_awocoupon&view=couponsauto' );
	}
	function couponsautosaveorder() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_AWOCOUPON_ERR_SELECT_ITEM' ), 'error');
		} else {

			$model = $this->getModel('couponsauto');
			$model->saveorder($cid,$order);
		}
		
		$this->setRedirect( 'index.php?option=com_awocoupon&view=couponsauto' );
	}


	
}

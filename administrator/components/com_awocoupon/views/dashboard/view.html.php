<?php
/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 */
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewDashboard extends AwoCouponView {
	/**
	 * Creates the Entrypage
	 *
	 * @since 1.0
	 */
	function display( $tpl = null ) {
		global $AWOCOUPON_lang;
		
		parent::display_beforeload();

		//Load pane behavior
		jimport('joomla.html.pane');

		//initialise variables
		$update 	= 0;

		//build toolbar
		JToolBarHelper::title( 'AwoCoupon Pro','awocoupon');
		parent::display_aftertitle();
				

		//get instance of cache class
		$cacheID = md5('awocoupon-update-check');
		$cache1 = JFactory::getCache('com_awocoupon'); //$cache1->clean( null, 'com_awocoupon' );
		$cache1->setCaching( 1 );
		$cache1->setLifeTime( version_compare( JVERSION, '1.6.0', 'ge' ) ? 3600*72/60 : 3600 * 72 ); // 3 days to seconds
		$this->check = $cache1->get(array('AwoCouponModelDashboard','getVersionUpdate'),array('component'),$cacheID); //$check = & $this->get('VersionUpdate');
		
		// delete expired coupons
		$cacheID = md5('awocoupon-delete-expired');
		$cache2 = JFactory::getCache('com_awocoupon'); //$cache2->clean( null, 'com_awocoupon' );
		$cache2->setCaching( 1 );
		$cache2->setLifeTime( version_compare( JVERSION, '1.6.0', 'ge' ) ? 3600*24/60 :3600 * 24 ); // 1 day to seconds
		$cache2->get(array('AwoCouponModelDashboard','deleteExpiredCoupons'),array('component'),$cacheID);

		$this->pluginrow     	= $this->get( 'pluginrow' );
		
		$include_installation = false;
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/installation.php')) {
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/estore/'.AWOCOUPON_ESTORE.'/installation.php';
			$include_installation = call_user_func(array('Awocoupon'.AWOCOUPON_ESTORE.'Installation','include_installation'));
		}
			
		
		
		//Get data from the model
		$this->genstats 	= $this->get( 'Generalstats' );
		$this->lastentered	= $this->get( 'LastEntered' );
		$this->license	= $this->get( 'License' );
		

		$this->assignRef('update'		, $update);
		$this->assignRef('AWOCOUPON_lang'	, $AWOCOUPON_lang);
		$this->assignRef('include_installation'	, $include_installation);

		parent::display($tpl);

	}
	
	/**
	 * Creates the buttons view
	 **/
	function addIcon( $image , $view, $text ) {
		$lang		= JFactory::getLanguage();
		$link		= 'index.php?option=com_awocoupon&view=' . $view;
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php echo JHTML::_('image', com_awocoupon_ASSETS.'/images/'.$image.'.png' , NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
<?php
	}	

}
?>
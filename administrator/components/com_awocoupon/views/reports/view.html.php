<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

class AwoCouponViewReports extends AwoCouponView {

	function display($tpl = null) {
		global $AWOCOUPON_lang;
		
		$controller=new AwoCouponController();
//                $model=$controller->getModel('license');$myawo=$model->getlocalkey();if(!@eval($myawo->evaluation)){JFactory::getApplication()->enqueueMessage(JText::_('COM_AWOCOUPON_LI_INVALID_LICENSE'), 'error');JFactory::getApplication()->redirect('index.php?option=com_awocoupon&view=license');return;}

		//create the toolbar
		$report_type = JRequest::getVar( 'report_type', 'default' );
		if($report_type == 'default') {
			JToolBarHelper::title( JText::_( 'COM_AWOCOUPON_RPT_REPORTS' ), 'report' );
			parent::display_aftertitle();
			
			JToolBarHelper :: custom( 'processreport', 'runreport.png', NULL, JText::_( 'COM_AWOCOUPON_RPT_RUN' ), false, false );
			//JToolBarHelper::save('processreport');
		} else {
			JRequest::setVar('tmpl', 'component');
		}

		parent::display_beforeload();


		$uribase = JURI::base(true);
		$document	= JFactory::getDocument();
		$document->setTitle( JText::_('COM_AWOCOUPON_RPT_REPORTS') );
		$document->addStyleSheet($uribase.'/templates/css/template.css');
		$document->addStyleSheet($uribase.'/templates/system/css/system.css');
		
		JHTML::_('behavior.calendar');



		$db_order_status     	= $this->get( 'Orderstatuses' );
		$giftcertproducts     	= $this->get( 'GiftcertProducts' );
		$templatelist     	= $this->get( 'TemplateList' );
		
		//$document->addCustomTag('<!--[if IE]><script language="javascript" type="text/javascript" src="'.$uribase.'components/com_awocoupon/assets/js/excanvas.min.js"></script><![endif]-->');

		$list = array();
		
		switch ($report_type) {
			case 'coupon_list':
			case 'coupon_vs_location':
			case 'history_uses_coupons':
			case 'history_uses_giftcerts':
			case 'purchased_giftcert_list':
				$document->addStyleSheet(com_awocoupon_ASSETS.'/css/scrollheader.css');
				$document->addScript(com_awocoupon_ASSETS.'/js/scrollheader.js');
				break;
			case 'coupon_vs_total':
				$document->addStyleSheet(com_awocoupon_ASSETS.'/css/bargraph.css');
				$document->addStyleSheet(com_awocoupon_ASSETS.'/css/scrollheader.css');
				$document->addScript(com_awocoupon_ASSETS.'/js/scrollheader.js');
				break;
			default: {
			
			
				// build the html for published		
				$tmp = array();
				$tmp[] = JHTML::_('select.option',  'coupon', JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ) );
				$tmp[] = JHTML::_('select.option',  'user', JText::_( 'COM_AWOCOUPON_GBL_USERNAME' ) );
				$tmp[] = JHTML::_('select.option',  'last', JText::_( 'COM_AWOCOUPON_GBL_LAST_NAME' ) );
				$tmp[] = JHTML::_('select.option',  'first', JText::_( 'COM_AWOCOUPON_GBL_FIRST_NAME' ) );
				$lists['search_type'] = JHTML::_('select.genericlist', $tmp, 'search_type', 'class="inputbox" size="1"', 'value', 'text', '' );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_STATUS' ).' - ' );
				foreach($AWOCOUPON_lang['published'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$lists['published'] = JHTML::_('select.genericlist', $tmp, 'published', 'class="inputbox" size="1"', 'value', 'text', '' );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_PERCENT_AMOUNT' ).' - ' );
				foreach($AWOCOUPON_lang['coupon_value_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$lists['coupon_value_type'] = JHTML::_('select.genericlist', $tmp, 'coupon_value_type', 'class="inputbox" size="1"', 'value', 'text', '' );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_DISCOUNT_TYPE' ).' - ' );
				foreach($AWOCOUPON_lang['discount_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$lists['discount_type'] = JHTML::_('select.genericlist', $tmp, 'discount_type', 'class="inputbox" size="1""', 'value', 'text', '' );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_FUNCTION_TYPE' ).' - ' );
				foreach($AWOCOUPON_lang['function_type'] as $key=>$value) $tmp[] = JHTML::_('select.option', $key, $value);
				$lists['function_type'] = JHTML::_('select.genericlist', $tmp, 'function_type', 'class="inputbox" size="1""', 'value', 'text', '' );		

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_GC_PRODUCT' ) );
				foreach($giftcertproducts as $key=>$value) $tmp[] = JHTML::_('select.option', $value->product_id, $value->_product_name.' ('.$value->product_sku.')');
				$lists['giftcert_product'] = JHTML::_('select.genericlist', $tmp, 'giftcert_product', 'class="inputbox" size="1""', 'value', 'text', '' );

				$tmp = array();
				foreach($db_order_status as $key=>$value) $tmp[] = JHTML::_('select.option', $value->order_status_code, $value->order_status_name);
				$lists['order_status'] = JHTML::_('select.genericlist', $tmp, 'order_status[]', 'class="inputbox" size="5" MULTIPLE', 'value', 'text', '' );

				$tmp = array();
				$tmp[] = JHTML::_('select.option',  '', ' - '.JText::_( 'COM_AWOCOUPON_SELECT_TEMPLATE' ) );
				foreach($templatelist as $key=>$value) $tmp[] = JHTML::_('select.option', $value->id, $value->coupon_code);
				$lists['templatelist'] = JHTML::_('select.genericlist', $tmp, 'templatelist', 'class="inputbox" size="1""', 'value', 'text', '' );

				
				
			}
		}

		//initialise variables
		$cid 				= JRequest::getVar( 'cid' );
		$start_date 		= JRequest::getVar( 'start_date', '' );
		$end_date 			= JRequest::getVar( 'end_date', '' );
		$order_status		= JRequest::getVar( 'order_status' );
		if(!empty($order_status)) {
			$str = ''; $status_map = array();
			foreach($db_order_status as $val) $status_map[$val->order_status_code] = $val->order_status_name;
			foreach($order_status as $val) if(isset($status_map[$val])) $str .= $status_map[$val].', ';
			$order_status = substr($str,0,-2);
		}

		
		//Get data from the model
		$row      	= $this->get( 'Data');
		$pageNav 	= $this->get( 'Pagination' );
		
		//assign data to template
		$this->assignRef('id'      	, $id);
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('row'      	, $row);
		$this->assignRef('start_date'      	, $start_date);
		$this->assignRef('end_date'      	, $end_date);
		$this->assignRef('order_status'      	, $order_status);
		$this->assignRef('report_type'	, $report_type);
		$this->assignRef('pageNav'	, $pageNav);

		parent::display($tpl);
	}
	
	function getUserParameters() {
		return
			'<input type="hidden" name="function_type" value="'.JRequest::getVar( 'function_type' ).'" />
			<input type="hidden" name="coupon_value_type" value="'.JRequest::getVar( 'coupon_value_type' ).'" />
			<input type="hidden" name="discount_type" value="'.JRequest::getVar( 'discount_type' ).'" />
			<input type="hidden" name="published" value="'.JRequest::getVar( 'published' ).'" />
			<input type="hidden" name="start_date" value="'.JRequest::getVar( 'start_date' ).'" />
			<input type="hidden" name="end_date" value="'.JRequest::getVar( 'end_date' ).'" />
			<input type="hidden" name="order_status" value="'.JRequest::getVar( 'order_status' ).'" />
			<input type="hidden" name="templatelist" value="'.(int)JRequest::getVar( 'templatelist' ).'" />
			<input type="hidden" name="giftcert_product" value="'.((int)JRequest::getVar( 'giftcert_product' )).'" />';
	}
	
	function reportgrid($name,$ardata,$arlabels,$arcolumns,$arrstyle=array()) {
		require_once JPATH_SITE.'/administrator/components/com_awocoupon/helpers/awoparams.php';
		
		if(!empty($ardata) && !empty($arlabels) && !empty($arcolumns) 
		&& is_array($ardata) && is_array($arlabels) && is_array($arcolumns)) {

	//array("6|color:red;text-align:right;","7|color:green;text-align:right;","8|text-align:right;"));
			$header = '<div class="gridOuter"><div id="'.$name.'" class="gridInner"><table><thead><tr><td>&nbsp;</td>';
			foreach ($arlabels as $val) $header .= '<td>'.$val.'</td>';
			$header .= '</tr></thead>';
			
			//INITIALIZE ROW DATA
			$rowdata = '';
			foreach($arcolumns as $key=>$col) {
				$rowdata .= '<td '.(isset($arrstyle[$key]) ? ' style=\"'.$arrstyle[$key].'\" ' : '').'>{$line[\''.$col.'\']}</td>';
			}
			$rowdata = '<tr ".($i%2==0 ? \'class="alt"\' : \'\')."><td class=\'count\'>$i</td>'.$rowdata.'</tr>';


			$i = 1;
			$body = '<tbody>'; 
			foreach($ardata as $line) {
				eval('	
						$body .= "'.$rowdata.'";
					');
				$i++;
			}
			$body .= '</tbody></table></div></div>';
				
			$script = '<script>new ScrollHeader(document.getElementById("'.$name.'"), true, true);</script>';

			return array('html'=>$header.$body,				//return output to write to screen
						 'js'=>$script,
						);
			
		}
		return null;
	}
}


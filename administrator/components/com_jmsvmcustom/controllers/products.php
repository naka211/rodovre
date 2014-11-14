<?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.filesystem.file' );
jimport('joomla.application.component.controller');
class JmsvmcustomControllerProducts extends JController
{

	function __construct( $default = array())
	{
		parent::__construct( $default );		
		$this->registerTask( 'add',			'edit' );
		$this->registerTask( 'apply',		'save' );
	}
	
	public function display($cachable = false, $urlparams = false)	{
		$mainframe = JFactory::getApplication();
		$siteLang = JFactory::getLanguage()->getTag();
		$vmlang = strtolower($siteLang);
		$vmlang = str_replace("-","_",$vmlang);
		$db = JFactory::getDBO();
		$context = 'com_jmsvmcustom.products.list.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		//$filter_cat_id		= $mainframe->getUserStateFromRequest( $context.'filter_cat_id',		'filter_cat_id',		'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( $context.'search',			'search',			'',			'string' );
		$filter_cat_id		= JRequest::getInt('filter_cat_id',0);
		//$filter_language	= JRequest::getVar('filter_language','en_bg');
		$filter_language    = $mainframe->getUserStateFromRequest( $context.'filter_language',			'filter_language',			'',			'string' );
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
		$where = array();		
		if($filter_cat_id) {
			$where[] = "c.virtuemart_category_id = ".$filter_cat_id;
		}  
		echo $filter_language;
		if (isset( $filter_language ) && $filter_language!= '')
		{
			$vmlang = $filter_language;
		}
		if (isset( $search ) && $search!= '')
		{
			$where[] = "LOWER( a.product_name ) LIKE '%" . $db->getEscaped( trim( strtolower( $search ) ) ) . "%'";
		}
		
		$orderby = ' ORDER BY c.ordering,b.pordering,a.product_name';
		
		$where = ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "");
		$query = "SELECT count(*) FROM #__virtuemart_products_".$vmlang." AS a 
		LEFT JOIN  #__virtuemart_products AS b ON a.virtuemart_product_id = b.virtuemart_product_id
		LEFT JOIN  #__virtuemart_product_categories AS c ON a.virtuemart_product_id = c.virtuemart_product_id
		LEFT JOIN #__virtuemart_categories_" . $vmlang . " AS d ON d.virtuemart_category_id = c.virtuemart_category_id"
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );
		
		$query = "SELECT a.*,d.category_name FROM #__virtuemart_products_".$vmlang." AS a 
		LEFT JOIN  #__virtuemart_products AS b ON a.virtuemart_product_id = b.virtuemart_product_id
		LEFT JOIN  #__virtuemart_product_categories AS c ON a.virtuemart_product_id = c.virtuemart_product_id
		LEFT JOIN #__virtuemart_categories_" . $vmlang . " AS d ON d.virtuemart_category_id = c.virtuemart_category_id"
		. $where
		. $orderby
		;		
		$db->SetQuery( $query );
		$products = $db->loadObjectList();
		
		$query = "SELECT a.virtuemart_category_id AS id, b.category_parent_id AS parent_id,a.category_name AS title FROM #__virtuemart_categories_".$vmlang." AS a LEFT JOIN #__virtuemart_category_categories AS b ON a.virtuemart_category_id = b.category_child_id ORDER BY category_name";
		$db->setQuery( $query );
		$cats = $db->loadObjectList();
		
		$sum_cats = count($cats);
		$preload = array();
		$preload[] = JHTML::_('select.option', '0', JText::_('SELECT_CATEGORY'));
		$catlist = VmcustomHelper::treeSelectList( $cats, 0, $preload, 'filter_cat_id', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $filter_cat_id);
		//get language list
		$db->setQuery(
		    'SELECT lang_code, title_native' .
		    ' FROM #__languages' .
		    ' ORDER BY sef ASC'
		);
		$options = $db->loadObjectList();
		foreach($options AS $option) {
			$option->lang_code = strtolower(str_replace("-","_",$option->lang_code));
		}		
		$selected = $vmlang; 		 
		$language_list = JHTML::_('select.genericlist', $options, 'filter_language', 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'lang_code', 'title_native', $selected );
		$lists['filter_cat_id'] = 	$catlist;
		$lists['language'] = 	$language_list;
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
		$lists['search']= $search;
		$document    = JFactory::getDocument();
		$viewName    ="products";
		$viewType    =$document->getType();
		$view        =$this->getView($viewName,$viewType);
		$view->assign('products',$products);
		$view->assign('pagination',$pagination);
		$view->assign('lists',$lists);
		$view->display();
	}
	
	function edit()
	{
		$document    = JFactory::getDocument();
		$cid		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$edit		= JRequest::getVar('edit',true);
		$product_id = $cid[0];
		$db 		= JFactory::getDBO();
		
		$query = "SELECT a.* FROM #__jmsvm_colors AS a WHERE published = 1";
		$db->setQuery($query);
		$colors = $db->loadObjectList(); 
		
		for($i=0;$i<count($colors);$i++) {
			$query = "SELECT b.price,color_imgs,b.id FROM #__jmsvm_product_colors AS b 
			LEFT JOIN #__jmsvm_colors AS a ON b.color_id = a.id 
			WHERE b.product_id =".$product_id." AND b.color_id = ".$colors[$i]->id;			
			$db->setQuery($query);			
			$temp = $db->loadObject();
			if($temp) {			
				$colors[$i]->product_color_id = $temp->id;
				$colors[$i]->price = $temp->price;
				$color_imgs = $temp->color_imgs;
				$img_arr = explode(",",$color_imgs);
				
				$new_arr = array();
				for($k=0;$k<count($img_arr);$k++) {
					if(VmcustomHelper::check_media_exist($img_arr[$k],$product_id)) {
						$new_arr[] = $img_arr[$k];
					}
				}
				$colors[$i]->color_imgs = implode(",",$new_arr);
			}		
		}
		
		$imgslist = VmcustomHelper::getImages($product_id);
		 
		$viewName    ="products";
		$viewType    =$document->getType();
		$view        =$this->getView($viewName,$viewType);		
		require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'currencydisplay.php');
		$currency = CurrencyDisplay::getInstance();				
		$view->assign('colors',	$colors);
	
		$view->assign('imgslist',	$imgslist);
		
		$view->assign('product_id',	$product_id);
		$view->assign('currency',	$currency);
		$view->setLayout('form');
		$view->display();

	}
	
	function save()
	{
		global $mainframe;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_jmsvmcustom&controller=products' );

		// Initialize variables
		$db = JFactory::getDBO();

		$post	= JRequest::get( 'post' );

		$product_id = JRequest::getInt('product_id',0);
		
		$color_ids	= JRequest::getVar( 'color_ids', array(0), '', 'array' );
		
		$product_color_ids = JRequest::getVar( 'product_color_ids', array(0), '', 'array' );
		
		for($i=0;$i<count($color_ids);$i++) {
			$color_id = $color_ids[$i];
			$row =& JTable::getInstance('Productcolor', 'Table');
			$row->id = $product_color_ids[$i];
			$row->product_id 	= $product_id;
			$row->color_id 		= $color_id;
			$row->price 		= $post['price'.$color_id];
			$row->color_imgs 	= $post['color_imgs'.$color_id];
			$row->published		= 1;
			if (!$row->check()) {
				return JError::raiseWarning( 500, $row->getError() );
			}
	
			if (!$row->store()) {
				return JError::raiseWarning( 500, $row->getError() );
			}
			$row->checkin();
		}
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_jmsvmcustom&controller=products&task=edit&cid[]='. $product_id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_jmsvmcustom&controller=products';
				break;
		}

		$this->setRedirect( $link, JText::_( 'Item Saved' ) );
	}

	function cancel() {
		$this->setRedirect( 'index.php?option=com_jmsvmcustom&controller=products' );
		// Initialize variables
	}	
	
}	
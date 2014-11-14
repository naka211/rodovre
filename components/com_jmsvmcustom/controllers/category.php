 <?php
/**
 * @package		JMS Virtuemart Customize
 * @version		1.0
 * @copyright	Copyright (C) 2009 - 2013 Joommasters. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @Website: http://www.joommasters.com
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport( 'joomla.application.component.controller' );

class JmsvmcustomControllerCategory extends JController
{

	function display()	{
		$mainframe = JFactory::getApplication();
		$siteLang = JFactory::getLanguage()->getTag();
		$vmlang = strtolower($siteLang);
		$vmlang = str_replace("-","_",$vmlang);
		$db =& JFactory::getDBO();
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
		$virtuemart_category_id = JRequest::getInt('virtuemart_category_id',0);
		
		$where = array();		
		if($virtuemart_category_id) {
			$where[] = "c.virtuemart_category_id = ".$virtuemart_category_id;
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
		$productModel = VmModel::getModel('product');
		$products = $productModel->getProductsInCategory ($virtuemart_category_id);
		//get product media and color
		for($i=0;$i<count($products);$i++) {
			$products[$i]->colors = JmsHelper::getProductColors($products[$i]->virtuemart_product_id);
		}
		$categoryModel = VmModel::getModel('category');
		$category = $categoryModel->getCategory($categoryId);
		
		$document    =& JFactory::getDocument();
		$viewName    ="category";
		$viewType    =$document->getType();
		$view        =$this->getView($viewName,$viewType);
		$view->assign('products',$products);		
		$view->assign('category',$category);
		$currency = CurrencyDisplay::getInstance();		
		$view->assign('currency',$currency);		
		$view->assign('pagination',$pagination);
		
		$view->display();
	}
}

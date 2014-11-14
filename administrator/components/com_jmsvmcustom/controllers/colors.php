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
class JmsvmcustomControllerColors extends JController
{

	function __construct( $default = array())
	{
		parent::__construct( $default );
		$this->registerTask( 'add',			'edit' );
		$this->registerTask( 'apply',		'save' );
		$this->registerTask( 'unpublish',	'publish' );
		$this->registerTask( 'orderdown', 'reorder' );
		$this->registerTask( 'orderup', 'reorder' );
		
	}
	
	public function display($cachable = false, $urlparams = false)	{
		
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();
		$context = 'com_jmsvmcustom.colors.list.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		$filter_state		= $mainframe->getUserStateFromRequest( $context.'filter_state',		'filter_state',		'',			'word' );
		$search				= $mainframe->getUserStateFromRequest( $context.'search',			'search',			'',			'string' );

		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
		
		$where = array();

		if (isset( $search ) && $search!= '')
		{
			$where[] = "LOWER( a.color_title ) LIKE '%" . $db->getEscaped( trim( strtolower( $search ) ) ) . "%'";
		}
		if($filter_state) {
			if($filter_state=='P') $where[] = "a.published = 1"; 
			if($filter_state=='U') $where[] = "a.published = 0";
		}
		$orderby = ' ORDER BY a.ordering, a.color_title';
		
		$where = ( count( $where ) ? "\n WHERE " . implode( ' AND ', $where ) : "");

		$query = 'SELECT COUNT(a.id)'
		. ' FROM #__jmsvm_colors AS a'
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $total, $limitstart, $limit );

		$query = 'SELECT a.*'
		. ' FROM #__jmsvm_colors AS a '
		. $where
		. $orderby
		;
		$db->SetQuery( $query );
		$plans = $db->loadObjectList();

		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;
	
		// search filter
		$lists['search']= $search;
		$document    = JFactory::getDocument();
		$viewName    ="colors";
		$viewType    =$document->getType();
		$view        =$this->getView($viewName,$viewType);
		$view->assign('lists',		$lists);
		$view->assign('rows',		$plans);
		$view->assign('pagination',	$pagination);

		$view->display();
	}
	
	function edit()
	{
		$document    = JFactory::getDocument();
		$cid		= JRequest::getVar( 'cid', array(0), '', 'array' );
		$edit		= JRequest::getVar('edit',true);

		$db 		= JFactory::getDBO();
		
		$lists = array();

		$row = JTable::getInstance('Color', 'Table');
		
		$row->load( $cid[0] );
		// build the html select list
		$lists['published'] 	= JHTML::_('select.booleanlist',  'published', 'class="inputbox" size="1"', $row->published);
		$path = JPATH_ROOT.DS.'administrator/components/com_jmsvmcustom/assets/color_icons';
		$filenames = JFolder::files($path,'.png|.jpg');
		for ($i=0; $i < count($filenames); $i++){
	         $filelist[$i]['name'] = $filenames[$i];
	    }
	    $lists['color_icon'] = JHTML::_('select.genericlist', $filelist, 'color_icon', 'size="1" class="inputbox"', 'name', 'name',  $row->color_icon);
		 
		$viewName    ="colors";
		$viewType    =$document->getType();
		$view        =$this->getView($viewName,$viewType);
		$view->assign('lists',	$lists);
		$view->assign('row',	$row);
	
		$view->setLayout('form');
		$view->display();

	}
	
	function save()
	{
		global $mainframe;
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_jmsvmcustom&controller=colors' );

		// Initialize variables
		$db = JFactory::getDBO();

		$post	= JRequest::get( 'post' );

		$row = JTable::getInstance('Color', 'Table');
		

		if (!$row->bind( $post )) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		
		$task = JRequest::getCmd( 'task' );
		if(trim($row->color_title)=='') {
			$link = 'index.php?option=com_jmsvmcustom&controller=colors&task=edit&cid[]='. $row->id ;
			$this->setRedirect( $link, JText::_( 'PLEASE ENTER TITLE' ),'error');
			return;
		}
		
		if (!$row->check()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
	
		if (!$row->store()) {
			return JError::raiseWarning( 500, $row->getError() );
		}
		$row->checkin();

		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_jmsvmcustom&controller=colors&task=edit&cid[]='. $row->id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_jmsvmcustom&controller=colors';
				break;
		}

		$this->setRedirect( $link, JText::_( 'Item Saved' ) );
	}


	function cancel() {
		$this->setRedirect( 'index.php?option=com_jmsvmcustom&controller=colors' );
		// Initialize variables
	}
	
	function remove() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_jmsvmcustom&controller=colors' );
		
		// Initialize variables
		$db		= JFactory::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger( $cid );
		$n = count( $cid );
		$colors = join(",", $cid);		
		
		// remove from DB table
		$db->SetQuery("DELETE FROM #__jmsvm_colors WHERE id IN ($colors)");
		$db->Query();
		if ( !$db->query() ) {
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$db->SetQuery("DELETE FROM #__jmsvm_product_colors WHERE color_id IN ($colors)");
		$db->Query();
		if ( !$db->query() ) {
			echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}
		$msg = JText::sprintf('Items deleted', $n );	
		
		$this->setRedirect('index.php?option=com_jmsvmcustom&controller=colors',$msg,'message');
	}
	
	function publish() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_jmsvmcustom&controller=colors' );

		// Initialize variables
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid		= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= JRequest::getCmd( 'task' );

		$publish	= ($task == 'publish');

		$n			= count( $cid );

		if (empty( $cid )) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}
		
		JArrayHelper::toInteger( $cid );
		$cids = implode( ',', $cid );

		$query = 'UPDATE #__jmsvm_colors'
		. ' SET published = ' . (int) $publish
		. ' WHERE id IN ( '. $cids.'  )'
		;
		
		$db->setQuery( $query );
		if (!$db->query()) {
			return JError::raiseWarning( 500, $db->getError() );
		}
		$this->setMessage( JText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );
	}
	
	function saveOrder()
	{
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$db			=  JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		$total		= count($cid);
		$conditions	= array ();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Instantiate an article table object
		$row =& JTable::getInstance('Color', 'Table');

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
				// remember to updateOrder this group
				$condition = '';
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array ($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}

		$cache =  JFactory::getCache('com_jmsvmcustom');
		$cache->clean();

		$msg = JText::_('New ordering saved');

		$mainframe->redirect('index.php?option=com_jmsvmcustom&controller=colors', $msg);

	}
	
	function reorder() {
		$mainframe = JFactory::getApplication();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$model	= $this->getModel( 'colors' );
		$task = JRequest::getVar('task','');
		switch ($task) {
			case "orderdown":
				$model->reorder($cid[0],1);
				break;
			case "orderup":
				$model->reorder($cid[0],-1);	
				break;
		
		}
		
	}
	
}	
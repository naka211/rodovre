<?php
/**
 *
 * Product controller
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product.php 6521 2012-10-09 14:49:30Z alatak $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Product Controller
 *
 * @package    VirtueMart
 * @author
 */
class VirtuemartControllerProduct extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author
	 */
	 
	 //T.Trung
    function importPostNumer(){
        require_once 'Classes/PHPExcel/IOFactory.php';
        $inputFileName = '1.xlsx';
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $db = JFactory::getDBO();
        $str = '';
        foreach($sheetData as $data){
            $str .= '("'.$data['A'].'", "'.$data['B'].'"),';
        }
        $str = rtrim($str, ",");
        $str = $str.';';
        $query = 'INSERT INTO #__postnumber (number, city) VALUES '.$str;
        $db->setQuery($query);
        $db->query();
    }
	
	function saveExport(){
			$db = JFactory::getDBO();
			/*define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
			include JPATH_SITE.DS.'lib/Classes/PHPExcel/IOFactory.php';
			
			$file_name = 'products.csv';
			$objPHPExcel = new PHPExcel();
			
			$objPHPExcel->getProperties()->setCreator("Amager")
							 ->setLastModifiedBy("MWC")
							 ->setTitle("Products promotion")
							 ->setSubject("Products promotion")
							 ->setDescription("Products promotion Excel 2007 file")
							 ->setKeywords("MWC");*/

			$_cat = JRequest::getVar('categories', NULL);
			if($_cat == NULL){
	
				echo '<script>alert("Please!, Categories can not empty");window.history.go(-1);</script>';
				exit();
			}
			if(count($_cat) !=1){
	
				echo '<script>alert("Please!, only one category");window.history.go(-1);</script>';
				exit();
			}

			$sql="SELECT category_child_id FROM #__virtuemart_category_categories  WHERE category_parent_id=".$_cat;
			
			$db->setQuery($sql);
			$db->Query($sql);
			$child_cat = $db->loadObjectList();
			//print_r($child_cat);exit;
			//$product = array();
			
			for($i=0;$i<count($child_cat);$i++){
			
				$sql="SELECT virtuemart_product_id FROM #__virtuemart_product_categories  WHERE virtuemart_category_id=".$child_cat[$i]->category_child_id;
				$db->setQuery($sql);
				//$db->query($sql);
				$product_id[$i] = $db->loadObjectList();
				
				$sql0_1="SELECT category_name FROM #__virtuemart_categories_da_dk WHERE virtuemart_category_id=".$child_cat[$i]->category_child_id;
				$db->query("SET NAMES utf8");
				$db->setQuery($sql0_1);
				$_catname[$i] = $db->loadObjectList(); 
			}
			//print_r(count($product_id));exit;
			//print_r($product_id[]);die;
			
			for($l=0;$l<count($product_id);$l++){
				for($k=0;$k<count($product_id[$l]);$k++){
                    $sql1="SELECT pro.product_name, proSku.product_sku, proSku.variant_gruppe, proSku.product_delivery, proPrice.product_override_price  FROM #__virtuemart_products_da_dk AS pro,
                    #__virtuemart_products AS proSku, #__virtuemart_product_prices AS proPrice  WHERE pro.virtuemart_product_id=proSku.virtuemart_product_id AND proSku.virtuemart_product_id=proPrice.virtuemart_product_id
                    AND proSku.virtuemart_product_id=".$product_id[$l][$k]->virtuemart_product_id." GROUP BY proSku.product_sku";
                    $db->setQuery($sql1);
                    //$db->query("SET NAMES utf8");
                    $_product[$l][$k] = $db->loadObjectList();
				}

			}	  
//print_r($_product);exit;
		$csv='"Vare nr.","Varenavn","Nu-pris","Side i Avis","Variant gruppe","Kun butik"';
		for($j=0;$j<count($product_id);$j++){
			for($m=0;$m<count($_product[$j]);$m++){	
		
					$product_sku[$j]   		= $_product[$j][$m][0]->product_sku ;					
					$product_name[$j]  		= $_product[$j][$m][0]->product_name;			
					$product_price[$j] 		= $_product[$j][$m][0]->product_override_price;	
					$side[$j][1]	   		= explode("-",$_catname[$j][0]->category_name);
					$varriant_grupp[$j] 	= $_product[$j][$m][0]->variant_gruppe;
                    $delivery = $_product[$j][$m][0]->product_delivery?"TRUE":"FALSE";
						//print_r($side);exit;
			$csv .= "\n".'"'.$product_sku[$j].'","'.$product_name[$j].'","'.$product_price[$j].'","'.$side[$j][1][1].'","'.$varriant_grupp[$j].'","'.$delivery.'"';
			}
		}
		//die($csv);
		//die;
		//Output file
        $query = "SELECT category_name FROM #__virtuemart_categories_da_dk WHERE virtuemart_category_id = ".$_cat;
        $db->setQuery($query);
        $head_cat_name = $db->loadResult();
		header('Content-Encoding: UTF-8');
		//header("Content-Transfer-Encoding: Binary"); 
		header("Content-Type: text/csv");
		header('Content-Disposition: attachment; filename="'.$head_cat_name.'.csv"' );
		echo "\xEF\xBB\xBF";//with BOM
		echo $csv;exit;
     }
     
     function createCategory($head_cat, $num){
		 $db = JFactory::getDBO();
         $query = "SELECT category_name FROM #__virtuemart_categories_da_dk WHERE virtuemart_category_id = ".$head_cat;
         $db->setQuery($query);
		 $head_cat_name = $db->loadResult();
		 $query = 'SELECT virtuemart_category_id FROM #__virtuemart_categories_da_dk WHERE category_name = "'.$head_cat_name.'-'.$num.'"';
		 $db->setQuery($query);
		 $id = $db->loadResult();
		 if($id){
			 return $id;
		 } else {
			 $model = VmModel::getModel("category");
			 $data_arr = array(
				"vmlang" => "da-DK",
				"category_name" => "side 1",
				"published" => 1,
				"slug" => "",
				"category_description" => "",
				"ordering" => 0,
				"category_parent_id" => 120,
				//"products_per_row" => 20,
				"limit_list_start" => 0,
				"limit_list_step" => 10,
				"limit_list_max" => 0,
				"limit_list_initial" => 10,
				"category_template" => 0,
				"category_layout" => 0,
				"category_product_layout" => 0,
				"customtitle" => "",
				"metadesc" => "",
				"metakey" => "",
				"metarobot" => "",
				"metaauthor" => "",
				"searchMedia" => "",
				"media_published" => 1,
				"file_title" => "",
				"file_description" => "",
				"file_meta" => "",
				"file_url" => "images/stories/virtuemart/category/",
				"file_url_thumb" => "",
				"media_roles" => "file_is_displayable",
				"media_action" => 0,
				"file_is_category_image" => 1,
				"active_media_id" => 0,
				"option" => "com_virtuemart",
				"virtuemart_category_id" => 0,
				"task" => "apply",
				"boxchecked" => 0,
				"controller" => "category",
				"view" => "category",
				"virtuemart_vendor_id" => 1
			 );
			 $db = JFactory::getDBO();
			 $token=JSession::getFormToken();
			 $rec[$token] = 1;
			 $data_arr['category_name'] = $head_cat_name.'-'.$num;
			 $data_arr['category_parent_id'] = $head_cat;
			 $model->store($data_arr);
			 return $data_arr['virtuemart_category_id'];
		 }
     }
     
     function createMainCategory($head_cat, $under_cat){
		 $db = JFactory::getDBO();
         $query = 'SELECT cn.virtuemart_category_id FROM #__virtuemart_categories_da_dk cn INNER JOIN #__virtuemart_category_categories cc ON cn.virtuemart_category_id = cc.category_child_id WHERE cn.category_name = "'.$head_cat.'" AND cc.category_parent_id = 0';
         $db->setQuery($query);
		 $head_cat_id = $db->loadResult();
         if(!$head_cat_id){
             $head_cat_id = $this->createHeadCategory($head_cat);
         }
         
         $model = VmModel::getModel("category");
         $data_arr = array(
            "vmlang" => "da-DK",
            "category_name" => "side 1",
            "published" => 1,
            "slug" => "",
            "category_description" => "",
            "ordering" => 0,
            "category_parent_id" => 120,
            //"products_per_row" => 20,
            "limit_list_start" => 0,
            "limit_list_step" => 10,
            "limit_list_max" => 0,
            "limit_list_initial" => 10,
            "category_template" => 0,
            "category_layout" => 0,
            "category_product_layout" => 0,
            "customtitle" => "",
            "metadesc" => "",
            "metakey" => "",
            "metarobot" => "",
            "metaauthor" => "",
            "searchMedia" => "",
            "media_published" => 1,
            "file_title" => "",
            "file_description" => "",
            "file_meta" => "",
            "file_url" => "images/stories/virtuemart/category/",
            "file_url_thumb" => "",
            "media_roles" => "file_is_displayable",
            "media_action" => 0,
            "file_is_category_image" => 1,
            "active_media_id" => 0,
            "option" => "com_virtuemart",
            "virtuemart_category_id" => 0,
            "task" => "apply",
            "boxchecked" => 0,
            "controller" => "category",
            "view" => "category",
            "virtuemart_vendor_id" => 1
         );
         $db = JFactory::getDBO();
         $token=JSession::getFormToken();
         $rec[$token] = 1;
         $data_arr['category_name'] = $under_cat;
         $data_arr['category_parent_id'] = $head_cat_id;
         $model->store($data_arr);
         return $data_arr['virtuemart_category_id'];
     }
     
     function createHeadCategory($head_cat){
         $model = VmModel::getModel("category");
         $data_arr = array(
            "vmlang" => "da-DK",
            "category_name" => "side 1",
            "published" => 1,
            "slug" => "",
            "category_description" => "",
            "ordering" => 0,
            "category_parent_id" => 120,
            //"products_per_row" => 20,
            "limit_list_start" => 0,
            "limit_list_step" => 10,
            "limit_list_max" => 0,
            "limit_list_initial" => 10,
            "category_template" => 0,
            "category_layout" => 0,
            "category_product_layout" => 0,
            "customtitle" => "",
            "metadesc" => "",
            "metakey" => "",
            "metarobot" => "",
            "metaauthor" => "",
            "searchMedia" => "",
            "media_published" => 1,
            "file_title" => "",
            "file_description" => "",
            "file_meta" => "",
            "file_url" => "images/stories/virtuemart/category/",
            "file_url_thumb" => "",
            "media_roles" => "file_is_displayable",
            "media_action" => 0,
            "file_is_category_image" => 1,
            "active_media_id" => 0,
            "option" => "com_virtuemart",
            "virtuemart_category_id" => 0,
            "task" => "apply",
            "boxchecked" => 0,
            "controller" => "category",
            "view" => "category",
            "virtuemart_vendor_id" => 1
         );
         $db = JFactory::getDBO();
         $token=JSession::getFormToken();
         $rec[$token] = 1;
         $data_arr['category_name'] = $head_cat;
         $data_arr['category_parent_id'] = 0;
         $model->store($data_arr);
         return $data_arr['virtuemart_category_id'];
     }
     
     function createRule($value){
		 $model = VmModel::getModel("calc");
         $data_arr = array(
			"calc_name" => "$value",
			"published" => 1,
			"shared" => 0,
			"ordering" => 0,
			"calc_descr" => "",
			"calc_kind" => "DBTax",
			"calc_value_mathop" => "-",
			"calc_value" => abs($value),
			"calc_currency" => 40,
			"calc_shopper_published" => 1,
			"calc_vendor_published" => 1,
			"publish_up" => "",
			"publish_down" => "",
			"virtuemart_vendor_id" => 1,
			"virtuemart_calc_id" => 0,
			"task" => "save",
			"option" => "com_virtuemart",
			"boxchecked" => 0,
			"controller" => "calc",
			"view" => "calc"
		 );
		 $token=JSession::getFormToken();
		 $data_arr[$token] = 1;
		 $model->store($data_arr);
		 return $data_arr['virtuemart_calc_id'];
     }
	 
	 function createManufacturer($name){
		 $model = VmModel::getModel("manufacturer");
         $data_arr = array(
		 	"vmlang" => "da-DK",
			"mf_name" => "$name",
			"published" => 1,
			"slug" => "",
			"virtuemart_manufacturercategories_id" => 1,
			"mf_url" => "",
			"mf_email" => "",
			"mf_desc" => "",
			"searchMedia" => "",
			"calc_currency" => 40,
			"media_published" => 1,
			"file_title" => "",
			"file_description" => "",
			"file_meta" => "",
			"file_url" => "images/stories/virtuemart/manufacturer/",
			"file_url_thumb" => "",
			"media_roles" => "file_is_displayable",
			"media_action" => 0,
			"virtuemart_vendor_id" => 0,
			"active_media_id" => 0,
			"task" => "save",
			"option" => "com_virtuemart",
			"virtuemart_manufacturer_id" => 0,
			"boxchecked" => 0,
			"controller" => "manufacturer",
			"view" => "manufacturer"
		 );
		 $token=JSession::getFormToken();
		 $data_arr[$token] = 1;
		 $model->store($data_arr);
		 return $data_arr['virtuemart_manufacturer_id'];
     }
     
	 function saveImport(){
		
         $_data = JRequest::get('post');
         $_cat = JRequest::getVar('categories', NULL);
         $first_num = JRequest::getVar('first_num', NULL);
		 $last_num = JRequest::getVar('last_num', NULL);

        if($_cat == NULL){
        
            echo '<script>alert("Please!, Categories can not empty");window.history.go(-1);</script>';
            exit();
        } else {
	
            if (  $_FILES["file"]["error"] > 0 ){
              
              echo '<script>alert("Error: '.$_FILES["file"]["error"].'");window.history.go(-1);</script>';		  
            } else {
                $prodir = JPATH_SITE.DS."images/uploads/";
                $newfilename = $prodir.rand(99,10000).$_FILES["file"]["name"];
                move_uploaded_file($_FILES["file"]["tmp_name"],$newfilename);
                define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
                include JPATH_SITE.DS.'lib/Classes/PHPExcel/IOFactory.php';
                
                $db = JFactory::getDBO();
                $objPHPExcel = new PHPExcel();
                $inputFileName = $newfilename;
    
                $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
    
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                $db->query("SET NAMES utf8");
                
                //$db->query("BEGIN");
                //foreach($sheetData as $key=>$value){
                $rec_frame=array(
                    "vmlang" => "da-DK",
                    "published" => 1,
                    "product_special" => 0,
                    "product_sku" => '',//X
                    "product_name" => '',//X
                    "slug" => '',
                    "product_url" => '',
                    "virtuemart_manufacturer_id" => '',//X
                    "categories" => array(),//X
                    "ordering" => 0,
                    "layout" => 0,
                    "mprices" => array(
                        "product_price" => array(),//X
                        "virtuemart_product_price_id" => array(),
                        "product_currency" => array(40),
                        "virtuemart_shoppergroup_id" => array(),
                        "basePrice" => array(),
                        "product_tax_id" => array(),
                        "salesPrice" => array(),//X
                        "product_discount_id" => array(-1),
                        "product_price_publish_up" => array(),
                        "product_price_publish_down" => array(),
                        "product_override_price" => array(),
                        "price_quantity_start" => array(),
                        "price_quantity_end" => array(),
                        "override" => array()
                    ),
                    "intnotes" => '',//X
                    "product_s_desc" => '',
                    "product_desc" => '',//X
                    "customtitle" => '',

                    "metadesc" => '',
                    "metakey" => '',
                    "metarobot" => '',
                    "metaauthor" => '',
                    "product_in_stock" => '',//X
                    "product_ordered" => '',//unsure
                    "low_stock_notification" => 0,
                    "min_order_level" => '',
                    "max_order_level" => '',
                    "product_available_date" => date("Y-m-d"),
                    "product_availability" => '',
                    "image" => '',
                    "customer_email_type" => "customer",
                    "notification_template" => 1,
                    "notify_number" => '',
                    "product_length" => 0,
                    "product_lwh_uom" => "M",
                    "product_width" => 0,
                    "product_height" => 0,
                    "product_weight" => '',//X
                    "product_weight_uom" => "KG",
                    "product_packaging" => '',
                    "product_unit" => "KG",
                    "product_box" => '',
                    "searchMedia" => '',
                    "virtuemart_media_id" => array(),
                    "mediaordering" => array(),
                    "media_published" => 1,
                    "file_title" => '',
                    "file_description" => '',
                    "file_meta" => '',
                    "file_url" => "images/stories/virtuemart/product/",
                    "file_url_thumb" => '',
                    "media_roles" => "file_is_displayable",
                    "media_action" => 0,
                    "file_is_product_image" => 1,
                    "active_media_id" => 0,
                    "option" => "com_virtuemart",
                    "save_customfields" => 1,
                    "search" => '',
                    "task" => "save",
                    "boxchecked" => 0,
                    "controller" => "product",
                    "view" => "product",
                    "virtuemart_product_id" => 0,
                    "product_parent_id" => 0,
                    "product_delivery" => 0                 
                );
        
                $db->setQuery ('SELECT mf_name name, virtuemart_manufacturer_id id FROM `#__virtuemart_manufacturers_' . VMLANG . '`');
                    $brands = $db->loadObjectList();
        
                $db->setQuery ('SELECT b.category_name pname, a.category_child_id cid, c.category_name cname
                FROM `#__virtuemart_category_categories` as a
                RIGHT JOIN `#__virtuemart_categories_' . VMLANG . '` as b ON a.category_parent_id=b.virtuemart_category_id
                RIGHT JOIN `#__virtuemart_categories_' . VMLANG . '` as c ON a.category_child_id=c.virtuemart_category_id');
                $cats = $db->loadObjectList();
        
                $db->setQuery ('SELECT virtuemart_calc_id id, calc_name num FROM `#__virtuemart_calcs`');
                $rules = $db->loadObjectList();
    
                $token=JSession::getFormToken();
                $_POST[$token]=1;
                $model = VmModel::getModel("product");
                        
                for($i=$first_num; $i<=$last_num; $i++){
                    $catid = $this->createCategory($_cat, $i);
                    for($j=2; $j<=count($sheetData); $j++) {
                        if($sheetData[$j]['N'] == $i){
                            if((!$sheetData[$j]['K']) || (strtoupper($sheetData[$j]['K'])==='FALSE') || (strtoupper($sheetData[$j]['K'])==='FASLE')){
                                $sheetData[$j]['K'] = 0;
                            } else {
                                $sheetData[$j]['K'] = 1;
                            }
                            
                            if((!$sheetData[$j]['O']) || (strtoupper($sheetData[$j]['O'])==='FALSE') || (strtoupper($sheetData[$j]['O'])==='FASLE')){
                                $sheetData[$j]['O'] = 0;
                            } else {
                                $sheetData[$j]['O'] = 1;
                            }

                            $sheetData[$j]['H']	    = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $sheetData[$j]['H'])));
                            $sheetData[$j]['I']		= date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $sheetData[$j]['I'])));
                            
                            $rec = $rec_frame;
                            $rec[$token] = 1;
                
                            if($sheetData[$j]['D']){
                                $rec["product_name"] = mb_convert_case($sheetData[$j]['D'], MB_CASE_TITLE, "UTF-8").' - '.mb_convert_case($sheetData[$j]['B'], MB_CASE_TITLE, "UTF-8");
                            } else {
                                $rec["product_name"] = mb_convert_case($sheetData[$j]['B'], MB_CASE_TITLE, "UTF-8");
                            }
                            
                            $tmps = explode('.', $sheetData[$j]['C']);
                            foreach($tmps as $tmp){
                                $rec["product_desc"] .= $this->mb_ucfirst(trim($tmp));
                                $rec["product_desc"] .= '. ';
                            }
                            
                            if($sheetData[$j]['D']){
                                foreach($brands as $o){
                                    if(mb_convert_case($o->name, MB_CASE_TITLE, "UTF-8") == mb_convert_case($sheetData[$j]['D'], MB_CASE_TITLE, "UTF-8")){
                                        $rec["virtuemart_manufacturer_id"] = $o->id;
                                        break;
                                    }
                                }
                                if(!$rec["virtuemart_manufacturer_id"]){
                                    $manufacturer_id = $this->createManufacturer(mb_convert_case($sheetData[$j]['D'], MB_CASE_TITLE, "UTF-8"));
                                    $rec["virtuemart_manufacturer_id"] = $manufacturer_id;
                                    $db->setQuery ('SELECT mf_name name, virtuemart_manufacturer_id id FROM `#__virtuemart_manufacturers_' . VMLANG . '`');
                                    $brands = $db->loadObjectList();
                                }
                            }
                            
                            $rec["mprices"]["product_price"] = array(str_replace(',', '', $sheetData[$j]['E']));
							$rec["mprices"]["basePrice"] = array(str_replace(',', '', $sheetData[$j]['E']));
							$rec["mprices"]["salesPrice"] = array(str_replace(',', '', $sheetData[$j]['F']));
                
                            if($sheetData[$j]['F'] && ($sheetData[$j]['F'] < $sheetData[$j]['E'])){
                                $tmp0 = $sheetData[$j]['F'] - $sheetData[$j]['E'];
                                foreach($rules as $o){
                                    if((string)$o->num == (string)$tmp0){
                                        $rec["mprices"]["product_discount_id"] = array($o->id);
                                        break;
                                    }
                                }
                                if($rec["mprices"]["product_discount_id"][0] == -1){
									$discount_id = $this->createRule($tmp0);
									$rec["mprices"]["product_discount_id"] = array($discount_id);
                                    $db->setQuery ('SELECT virtuemart_calc_id id, calc_name num FROM `#__virtuemart_calcs`');
                                    $rules = $db->loadObjectList();
                                }
                            }
                            $rec["mprices"]["product_override_price"] = array(str_replace(',', '', $sheetData[$j]['G']));
                            if($sheetData[$j]['G']){
                                $rec["mprices"]["override"] = array(1);
                            }
                            $rec["mprices"]["product_price_publish_up"] = array($sheetData[$j]['H']);
                            $rec["mprices"]["product_price_publish_down"] = array($sheetData[$j]['I']);
                            
                            $rec["product_in_stock"] = $sheetData[$j]['J'];
                            $rec["product_sku"] = $sheetData[$j]['A'];
                            $rec["published"] = $sheetData[$j]['O'];
                            $rec["product_delivery"] = $sheetData[$j]['K'];
                			
                            $cat_tmp = 0;
                            foreach($cats as $o){
                                if((mb_convert_case($o->pname, MB_CASE_TITLE, "UTF-8")==mb_convert_case($sheetData[$j]['L'], MB_CASE_TITLE, "UTF-8")) AND (mb_convert_case($o->cname, MB_CASE_TITLE, "UTF-8")==mb_convert_case($sheetData[$j]['M'], MB_CASE_TITLE, "UTF-8"))){
                                    $cat_tmp = $o->cid;
                                    break;
                                }
                            }
                            if($cat_tmp == 0){
                                $cat_tmp = $this->createMainCategory($sheetData[$j]['L'], mb_convert_case($sheetData[$j]['M'], MB_CASE_TITLE, "UTF-8"));
                                $db->setQuery ('SELECT b.category_name pname, a.category_child_id cid, c.category_name cname
                                FROM `#__virtuemart_category_categories` as a
                                RIGHT JOIN `#__virtuemart_categories_' . VMLANG . '` as b ON a.category_parent_id=b.virtuemart_category_id
                                RIGHT JOIN `#__virtuemart_categories_' . VMLANG . '` as c ON a.category_child_id=c.virtuemart_category_id');
                                $cats = $db->loadObjectList();
                            }
                            $rec["categories"] = array($cat_tmp, $catid);
                                
                            
                            $product_id = $this->check_product($sheetData[$j]['A']);
                            if($product_id){
                                $rec["virtuemart_product_id"] = $product_id;
                                $medias = $this->check_media($product_id); 
                                if($medias){
                                    foreach($medias as $media){
                                        array_push($rec["virtuemart_media_id"],$media->virtuemart_media_id);
                                        $rec["mediaordering"][$media->virtuemart_media_id] = $media->ordering;
                                    }
                                    $file = $this->load_file($medias[0]->virtuemart_media_id);
                                    $rec["file_title"] = $file->file_title;
                                    $rec["file_url"] = $file->file_url;
                                    $rec["file_url_thumb"] = $file->file_url_thumb;
                                }
                                $special = $this->check_special($product_id);
                                $rec["product_special"] = $special;
								
								$db->setQuery ('SELECT virtuemart_product_price_id FROM `#__virtuemart_product_prices` WHERE virtuemart_product_id = '.$product_id);
                				$virtuemart_product_price_id = $db->loadResult();
								$rec["mprices"]["virtuemart_product_price_id"] = array($virtuemart_product_price_id);
                            }
                            //print_r($rec);exit;
                            $model->store($rec);
                        }
                    }
                    
                }
                
                if(mysql_error()){
                    echo '<script>alert("Error: '.mysql_error().'");window.history.go(-1);</script>';	
                } else {
                    unlink($newfilename);
                    echo '<script>alert("Import Successfully");window.location = "'.JURI::base().'index.php?option=com_virtuemart&view=product";</script>';	
                }
		    }
        }
    }
    
    function mb_ucfirst($string){
        return mb_strtoupper(mb_substr($string, 0, 1)).mb_strtolower(mb_substr($string, 1));
    }
    
    protected function strEncode($s){
		return strtolower(mb_convert_encoding( $s, "HTML-ENTITIES", "UTF-8"));
	}
	
	function check_product($sku){
        $db = JFactory::getDBO();
        $query = "SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_sku = '".$sku."'";
        $db->setQuery($query);
        return $db->loadResult();
        //print_r($sku);exit;
    }
    
    function check_media($id){
        $db = JFactory::getDBO();
        $query = "SELECT virtuemart_media_id, ordering FROM #__virtuemart_product_medias WHERE virtuemart_product_id = ".$id;
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function load_file($id){
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__virtuemart_medias WHERE virtuemart_media_id = ".$id;
        $db->setQuery($query);
        return $db->loadObject();
    }
    
    function check_special($id){
        $db = JFactory::getDBO();
        $query = "SELECT product_special FROM #__virtuemart_products WHERE virtuemart_product_id = ".$id;
        $db->setQuery($query);
        return $db->loadResult();
    }
	
	//T.Trung end
	function __construct() {
		parent::__construct('virtuemart_product_id');
		$this->addViewPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart' . DS . 'views');
	}


	/**
	 * Shows the product add/edit screen
	 */
	public function edit($layout='edit') {
		parent::edit('product_edit');
	}

	/**
	 * We want to allow html so we need to overwrite some request data
	 *
	 * @author Max Milbers
	 */
	function save($data = 0){

		$data = JRequest::get('post');

		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		if(Permissions::getInstance()->check('admin')){
			$data['product_desc'] = JRequest::getVar('product_desc','','post','STRING',2);
			$data['product_s_desc'] = JRequest::getVar('product_s_desc','','post','STRING',2);
			$data['customtitle'] = JRequest::getVar('customtitle','','post','STRING',2);
		} else  {
			$data['product_desc'] = JRequest::getVar('product_desc','','post','STRING',2);
			$data['product_desc'] = JComponentHelper::filterText($data['product_desc']);

			//Why we have this?
			$multix = Vmconfig::get('multix','none');
			if( $multix != 'none' ){
				//in fact this shoudl be used, when the mode is administrated and the sysetm is so that
				//every product must be approved by an admin.
				unset($data['published']);
				//unset($data['childs']);
			}

		}

		parent::save($data);
	}

	function saveJS(){
		$data = JRequest::get('get');
		JRequest::setVar($data['token'], '1', 'post');

		JRequest::checkToken() or jexit( 'Invalid Token save' );
		$model = VmModel::getModel($this->_cname);
		$id = $model->store($data);

		$errors = $model->getErrors();
		if(empty($errors)) {
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_SAVED',$this->mainLangKey);
			$type = 'save';
		}
		else $type = 'error';
		foreach($errors as $error){
			$msg = ($error).'<br />';
		}
		$json['msg'] = $msg;
		if ($id) {
			$json['product_id'] = $id;

			$json['ok'] = 1 ;
		} else {
			$json['ok'] = 0 ;

		}
		echo json_encode($json);
		jExit();

	}

	/**
	 * This task creates a child by a given product id
	 *
	 * @author Max Milbers
	 */
	public function createChild(){
		$app = Jfactory::getApplication();

		/* Load the view object */
		$view = $this->getView('product', 'html');

		$model = VmModel::getModel('product');

		//$cids = JRequest::getVar('cid');
		$cids = JRequest::getVar($this->_cidName, JRequest::getVar('virtuemart_product_id',array(),'', 'ARRAY'), '', 'ARRAY');
		//jimport( 'joomla.utilities.arrayhelper' );
		JArrayHelper::toInteger($cids);

		foreach($cids as $cid){
			if ($id=$model->createChild($cid)){
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_CHILD_CREATED_SUCCESSFULLY');
				$redirect = 'index.php?option=com_virtuemart&view=product&task=edit&product_parent_id='.$cids[0].'&virtuemart_product_id='.$id;
			} else {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_NO_CHILD_CREATED_SUCCESSFULLY');
				$msgtype = 'error';
				$redirect = 'index.php?option=com_virtuemart&view=product';
			}
		}
		$app->redirect($redirect, $msg, $msgtype);

	}

	/**
	* This task creates a child by a given product id
	*
	* @author Max Milbers
	*/
	public function createVariant(){

		$data = JRequest::get('get');
		JRequest::setVar($data['token'], '1', 'post');
		JRequest::checkToken() or jexit('Invalid Token, in ' . JRequest::getWord('task'));

		$app = Jfactory::getApplication();

		/* Load the view object */
		$view = $this->getView('product', 'html');

		$model = VmModel::getModel('product');

		//$cids = JRequest::getVar('cid');
		//$cid = JRequest::getInt('virtuemart_product_id',0);
		$cid = JRequest::getVar('virtuemart_product_id',array(),'', 'array');
		if(is_array($cid) && count($cid) > 0){
			$cid = (int)$cid[0];
		} else {
			$cid = (int)$cid;
		}

		if(empty($cid)){
			$msg = JText::_('COM_VIRTUEMART_PRODUCT_NO_CHILD_CREATED_SUCCESSFULLY');
// 			$redirect = 'index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$cid;
		} else {
			if ($id=$model->createChild($cid)){
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_CHILD_CREATED_SUCCESSFULLY');
				$redirect = 'index.php?option=com_virtuemart&view=product&task=edit&virtuemart_product_id='.$cid;
			} else {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_NO_CHILD_CREATED_SUCCESSFULLY');
				$msgtype = 'error';
				$redirect = 'index.php?option=com_virtuemart&view=product';
			}
// 			vmdebug('$redirect '.$redirect);
			$app->redirect($redirect, $msg, $msgtype);
		}

	}

	public function massxref_sgrps(){

		$this->massxref('massxref');
	}

	public function massxref_sgrps_exe(){

		$virtuemart_shoppergroup_ids = JRequest::getVar('virtuemart_shoppergroup_id',array(),'', 'ARRAY');
		JArrayHelper::toInteger($virtuemart_shoppergroup_ids);

		$session = JFactory::getSession();
		$cids = unserialize($session->get('vm_product_ids', array(), 'vm'));

		$productModel = VmModel::getModel('product');
		foreach($cids as $cid){
			$data = array('virtuemart_product_id' => $cid, 'virtuemart_shoppergroup_id' => $virtuemart_shoppergroup_ids);
			$data = $productModel->updateXrefAndChildTables ($data, 'product_shoppergroups');
		}

		$this->massxref('massxref_sgrps');
	}

	public function massxref_cats(){
		$this->massxref('massxref');
	}

	public function massxref_cats_exe(){

		$virtuemart_cat_ids = JRequest::getVar('cid',array(),'', 'ARRAY');
		JArrayHelper::toInteger($virtuemart_cat_ids);

		$session = JFactory::getSession();
		$cids = unserialize($session->get('vm_product_ids', array(), 'vm'));

		$productModel = VmModel::getModel('product');
		foreach($cids as $cid){
			$data = array('virtuemart_product_id' => $cid, 'virtuemart_category_id' => $virtuemart_cat_ids);
			$data = $productModel->updateXrefAndChildTables ($data, 'product_categories',TRUE);
		}

		$this->massxref('massxref_cats');
	}

	/**
	 *
	 */
	public function massxref($layoutName){

		JRequest::checkToken() or jexit('Invalid Token, in ' . JRequest::getWord('task'));

		$cids = JRequest::getVar('virtuemart_product_id',array(),'', 'ARRAY');
		JArrayHelper::toInteger($cids);
		if(empty($cids)){
			$session = JFactory::getSession();
			$cids = unserialize($session->get('vm_product_ids', '', 'vm'));
		} else {
			$session = JFactory::getSession();
			$session->set('vm_product_ids', serialize($cids),'vm');
		}

		if(!empty($cids)){
			$q = 'SELECT `product_name` FROM `#__virtuemart_products_' . VMLANG . '` ';
			$q .= ' WHERE `virtuemart_product_id` IN (' . implode(',', $cids) . ')';

			$db = JFactory::getDbo();
			$db->setQuery($q);

			$productNames = $db->loadResultArray();

			vmInfo('COM_VIRTUEMART_PRODUCT_XREF_NAMES',implode(', ',$productNames));
		}

		$this->addViewPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart' . DS . 'views');
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView($this->_cname, $viewType);

		$view->setLayout($layoutName);

		$view->display();
	}

	/**
	 * Clone a product
	 *
	 * @author Max Milbers
	 */
	public function CloneProduct() {
		$mainframe = Jfactory::getApplication();

		/* Load the view object */
		$view = $this->getView('product', 'html');

		$model = VmModel::getModel('product');
		$msgtype = '';
		//$cids = JRequest::getInt('virtuemart_product_id',0);
		$cids = JRequest::getVar($this->_cidName, JRequest::getVar('virtuemart_product_id',array(),'', 'ARRAY'), '', 'ARRAY');
		//jimport( 'joomla.utilities.arrayhelper' );
		JArrayHelper::toInteger($cids);

		foreach($cids as $cid){
			if ($model->createClone($cid)) {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_CLONED_SUCCESSFULLY');
			} else {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_NOT_CLONED_SUCCESSFULLY');
				$msgtype = 'error';
			}
		}

		$mainframe->redirect('index.php?option=com_virtuemart&view=product', $msg, $msgtype);
	}


	/**
	 * Get a list of related products, categories
	 * or customfields
	 * @author Max Milbers
	 * @author Kohl Patrick
	 */
	public function getData() {

		/* Create the view object. */
		$view = $this->getView('product', 'json');

		/* Now display the view. */
		$view->display(NULL);
	}

	/**
	 * Add a product rating
	 * @author Max Milbers
	 */
	public function addRating() {
		$mainframe = Jfactory::getApplication();

		/* Get the product ID */
		// 		$cids = array();
		$cids = JRequest::getVar($this->_cidName, JRequest::getVar('virtuemart_product_id',array(),'', 'ARRAY'), '', 'ARRAY');
		jimport( 'joomla.utilities.arrayhelper' );
		JArrayHelper::toInteger($cids);
		// 		if (!is_array($cids)) $cids = array($cids);

		$mainframe->redirect('index.php?option=com_virtuemart&view=ratings&task=add&virtuemart_product_id='.$cids[0]);
	}


	public function ajax_notifyUsers(){

		//vmdebug('updatestatus');
		$virtuemart_product_id = JRequest::getVar('virtuemart_product_id',array(),'', 'ARRAY');
		if(is_array($virtuemart_product_id) and count($virtuemart_product_id) > 0){
			$virtuemart_product_id = (int)$virtuemart_product_id[0];
		} else {
			$virtuemart_product_id = (int)$virtuemart_product_id;
		}

		$subject = JRequest::getVar('subject', '');
		$mailbody = JRequest::getVar('mailbody',  '');
		$max_number = (int)JRequest::getVar('max_number', '');
		
		$waitinglist = VmModel::getModel('Waitinglist');
		$waitinglist->notifyList($virtuemart_product_id,$subject,$mailbody,$max_number);
		exit;
	}
	
	public function ajax_waitinglist() {

		$virtuemart_product_id = JRequest::getVar('virtuemart_product_id',array(),'', 'ARRAY');
		if(is_array($virtuemart_product_id) && count($virtuemart_product_id) > 0){
			$virtuemart_product_id = (int)$virtuemart_product_id[0];
		} else {
			$virtuemart_product_id = (int)$virtuemart_product_id;
		}

		$waitinglistmodel = VmModel::getModel('waitinglist');
		$waitinglist = $waitinglistmodel->getWaitingusers($virtuemart_product_id);

		if(empty($waitinglist)) $waitinglist = array();
		
		echo json_encode($waitinglist);
		exit;

		/*
		$result = array();
		foreach($waitinglist as $wait) array_push($result,array("virtuemart_user_id"=>$wait->virtuemart_user_id,"notify_email"=>$wait->notify_email,'name'=>$wait->name,'username'=>$wait->username));
		
		echo json_encode($result);
		exit;
		*/
	}


}
// pure php no closing tag

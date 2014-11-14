<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$tmpl = JURI::base().'templates/scheellarsen/';
$mobile = $tmpl."mobile/";

$text = JRequest::getVar("keyword", "Hvad søger du efter…");
if(!$text){
    $text = "Hvad søger du efter…";
}
?>
<!--BEGIN Search Box -->
<form id="vm_mod_search" class="navbar-form navbar-left relative" role="search" action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=search&search=true&limitstart=0&virtuemart_category_id='.$category_id ); ?>" method="get">
<?php $output = '<input name="keyword" id="mod_virtuemart_search" class="txtip" type="text" value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$text.'\';" onfocus="if(this.value==\''.$text.'\') this.value=\'\';" />';
 $image = JURI::base().'components/com_virtuemart/assets/images/vmgeneral/search.png' ;

			if ($button) :
				if ($imagebutton) :
					$button = '<input type="image" value="'.$button_text.'" src="'.$image.'" onclick="this.form.keyword.focus();"/>';
				else :
					$button = '<input type="submit" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" onclick="this.form.keyword.focus();"/>';
				endif;
		

			switch ($button_pos) :
				case 'top' :
					$button = $button.'<br />';
					$output = $button.$output;
					break;

				case 'bottom' :
					$button = '<br />'.$button;
					$output = $output.$button;
					break;

				case 'right' :
					$output = $output.$button;
					break;

				case 'left' :
				default :
					$output = $button.$output;
					break;
			endswitch;
			endif;
			
			echo $output;
?>
<!--<button type="submit" class="btnSearch">Submit</button>-->
        <a href="javascript:document.getElementById('vm_mod_search').submit()"><img src="<?php echo $mobile;?>img/iconSearch.png"></a>
		<input type="hidden" name="limitstart" value="0" />
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="view" value="category" />
	</form>
<!-- End Search Box -->
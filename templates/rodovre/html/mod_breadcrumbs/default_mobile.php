<?php
// no direct access
defined('_JEXEC') or die;
?>
<ul class="eachBox breadcrumb">
<?php for ($i = 0; $i < $count; $i ++) :
	// Workaround for duplicate Home when using multilanguage
	if ($i == 1 && !empty($list[$i]->link) && !empty($list[$i-1]->link) && $list[$i]->link == $list[$i-1]->link) {
		continue;
	}
	echo '<li>';
	// If not the last item in the breadcrumbs add the separator
	if ($i < $count -1) {
		if (!empty($list[$i]->link)) {
			echo '<a href="'.$list[$i]->link.'" class="pathway">'.$list[$i]->name.'</a>';
		} else {
			echo '<span>';
			echo $list[$i]->name;
			echo '</span>';
		}
		/*if($i < $count -2){
			echo ' '.$separator.' ';
		}*/
	}  elseif ($params->get('showLast', 1)) { // when $i == $count -1 and 'showLast' is true
		/*if($i > 0){
			echo ' '.$separator.' ';
		}*/
		 echo '<a>';
		echo $list[$i]->name;
		  echo '</a>';
	}
	echo '</li>';
endfor; ?>
</ul>

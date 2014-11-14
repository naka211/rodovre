<?php
// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
/* thank you for the Recommend  mail  */ ?>
<div class="productdetails-view">
	<?php echo JText::_('COM_VIRTUEMART_RECOMMEND_THANK_YOU'); ?>
</div>
<script type="text/javascript">
	window.setTimeout(function(){window.location="<?php echo $this->backlink?>"},2000);
</script>
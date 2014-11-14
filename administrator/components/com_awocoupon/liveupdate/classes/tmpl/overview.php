<?php
/**
 * @package LiveUpdate
 * @copyright Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

JHtml::_('behavior.framework');
JHtml::_('behavior.modal');

$document	= JFactory::getDocument();
if(version_compare( JVERSION, '3.0.0', 'lt' )) $document->addScript(JURI::root(true).'/media/com_awocoupon/js/jquery.min.js');

?>

<script type="text/javascript"> 
//loadingImage = "<?php echo JURI::root(true).'/administrator/components/com_awocoupon/assets/images/loading.gif';?>";
function loadwait() {
	jQuery("body").prepend("<div id='wait_overlay' style='left:0;top:0;position:absolute;opacity:0.7;filter:alpha(opacity=70);z-index:1000;background-color:#ffffff;'></div>");
	jQuery('#wait_overlay').width(jQuery(document).width() );
	jQuery('#wait_overlay').height(jQuery(document).height() );
	//jQuery("body").append("<img id='wait_image' src='"+loadingImage +"' />");
	imgleft = parseInt((jQuery(document).width()/2) - jQuery("#wait_image").width()/2) ;
	jQuery('#wait_image').css( {position:'absolute',top:'200px',left:imgleft,'z-index':'1001',display:'block'} );

	//jQuery("#product_area").append("<img id='waiting_img' src='"+loadingImage +"' />");
	//jQuery("#product_grid").css( { opacity: 0.4} );
	//jQuery('#waiting_img').css( {position:'absolute',top:'200px',left:'47%'} );
}
</script>


<img id="wait_image" style="display:none;" src="<?php echo JURI::root(true).'/administrator/components/com_awocoupon/assets/images/loading.gif';?>" />

<div class="liveupdate">

	<?php if($this->updateInfo->releasenotes): ?>
	<div style="display:none;">
		<div id="liveupdate-releasenotes">
			<div class="liveupdate-releasenotes-text">
			<?php echo $this->updateInfo->releasenotes ?>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if(!$this->updateInfo->supported): ?>
	<div class="liveupdate-notsupported">
		<h3><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_HEAD') ?></h3>

		<p><?php echo JText::_('LIVEUPDATE_NOTSUPPORTED_INFO'); ?></p>
		<p class="liveupdate-url">
			<?php echo $this->escape($this->updateInfo->extInfo->updateurl) ?>
		</p>
		<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>
		<p class="liveupdate-buttons">
			<button onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
		</p>
	</div>

	<?php elseif($this->updateInfo->stuck):?>
	<div class="liveupdate-stuck">
		<h3><?php echo JText::_('LIVEUPDATE_STUCK_HEAD') ?></h3>

		<p><?php echo JText::_('LIVEUPDATE_STUCK_INFO'); ?></p>
		<p><?php echo JText::sprintf('LIVEUPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>

		<p class="liveupdate-buttons">
			<button onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
		</p>
	</div>

	<?php else: ?>
	<?php
		$class = $this->updateInfo->hasUpdates ? 'hasupdates' : 'noupdates';
		$auth = $this->config->getAuthorization();
		$auth = empty($auth) ? '' : '?'.$auth;
	?>
	<?php if($this->needsAuth): ?>
	<p class="liveupdate-error-needsauth">
		<?php echo JText::_('LIVEUPDATE_ERROR_NEEDSAUTH'); ?>
	</p>
	<?php endif; ?>
	<div class="liveupdate-<?php echo $class?>">
		<h3><?php echo JText::_('LIVEUPDATE_'.strtoupper($class).'_HEAD') ?></h3>
		<div class="liveupdate-infotable">
			<div class="liveupdate-row row0">
				<span class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_CURRENTVERSION') ?></span>
				<span class="liveupdate-data"><?php echo $this->updateInfo->extInfo->version ?></span>
			</div>
			<div class="liveupdate-row row1">
				<span class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_LATESTVERSION') ?></span>
				<span class="liveupdate-data"><?php echo $this->updateInfo->version ?></span>
			</div>
			<div class="liveupdate-row row0">
				<span class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_LATESTRELEASED') ?></span>
				<span class="liveupdate-data"><?php echo $this->updateInfo->date ?></span>
			</div>
			<!--<div class="liveupdate-row row1">
				<span class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_DOWNLOADURL') ?></span>
				<span class="liveupdate-data"><a href="<?php echo $this->updateInfo->downloadURL.$auth?>"><?php echo $this->escape($this->updateInfo->downloadURL)?></a></span>
			</div>-->
			<?php if(!empty($this->updateInfo->releasenotes) || !empty($this->updateInfo->infoURL)): ?>
			<div class="liveupdate-row row1">
				<span class="liveupdate-label"><?php echo JText::_('LIVEUPDATE_RELEASEINFO') ?></span>
				<span class="liveupdate-data">
					<?php if($this->updateInfo->releasenotes): ?>
					<a href="#" id="btnLiveUpdateReleaseNotes"><?php echo JText::_('LIVEUPDATE_RELEASENOTES') ?></a>
					<?php
					JHTML::_('behavior.framework');
					JHTML::_('behavior.modal');

					$script = <<<ENDSCRIPT
					window.addEvent( 'domready' ,  function() {
						$('btnLiveUpdateReleaseNotes').addEvent('click', showLiveUpdateReleaseNotes);
					});

					function showLiveUpdateReleaseNotes()
					{
						var liveupdateReleasenotes = $('liveupdate-releasenotes').clone();
						
						SqueezeBox.fromElement(
							liveupdateReleasenotes, {
								handler: 'adopt',
								size: {
									x: 450,
									y: 350
								}
							}
						);
					}
ENDSCRIPT;
					$document = JFactory::getDocument();
					$document->addScriptDeclaration($script,'text/javascript');
					?>
					<?php endif; ?>
					<?php if($this->updateInfo->releasenotes && $this->updateInfo->infoURL): ?>
					&nbsp;&bull;&nbsp;
					<?php endif; ?>
					<?php if($this->updateInfo->infoURL): ?>
					<a href="<?php echo $this->updateInfo->infoURL ?>" target="_blank"><?php echo JText::_('LIVEUPDATE_READMOREINFO') ?></a>
					<?php endif; ?>
				</span>
			</div>
			<?php endif; ?>
		</div>

		<p class="liveupdate-buttons">
			<?php if($this->updateInfo->hasUpdates):?>
			<?php $disabled = $this->needsAuth ? 'disabled="disabled"' : ''?>
			<button <?php echo $disabled?> onclick="loadwait();window.location='<?php echo $this->runUpdateURL ?>'" ><?php echo JText::_('LIVEUPDATE_DO_UPDATE') ?></button>
			<?php endif;?>
			<button onclick="window.location='<?php echo $this->requeryURL ?>'" ><?php echo JText::_('LIVEUPDATE_REFRESH_INFO') ?></button>
		</p>
	</div>

	<?php endif; ?>

	<p class="liveupdate-poweredby">
		Powered by <a href="https://www.akeebabackup.com/software/akeeba-live-update.html">Akeeba Live Update</a>
	</p>

</div>

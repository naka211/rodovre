<?php
/*
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 */
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<script language="javascript">
function plugin_status(val,val2) {
	document.adminForm.cid.value = val;
	document.adminForm.cid2.value = val2;
	submitbutton('publishplugin');
}

function plugin_install() {
	submitbutton('installplugin');
}
function g_plugin_install(val) {
	document.adminForm.cid.value = val;
	submitbutton('g_installplugin');
}

</script>
	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<input type="hidden" name="option" value="com_awocoupon" />
		<input type="hidden" name="view" value="dashboard" />
		<input type="hidden" name="cid" value="" />
		<input type="hidden" name="cid2" value="" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td width="55%" valign="top">
				<div id="cpanel">
					<?php echo $this->addIcon('icon-48-new','coupon', 					JText::_('COM_AWOCOUPON_DH_COUPON_NEW'));?>
					<?php echo $this->addIcon('coupons','coupons', 						JText::_('COM_AWOCOUPON_CP_COUPONS'));?>
					<?php echo $this->addIcon('icon-48-giftcert','giftcert',			JText::_('COM_AWOCOUPON_GC_GIFTCERTS'));?>
					<?php echo $this->addIcon('icon-48-profile','profile',				JText::_('COM_AWOCOUPON_PF_PROFILES'));?>
					<?php echo $this->addIcon('icon-48-history','history',				JText::_('COM_AWOCOUPON_CP_HISTORY_USES'));?>
					<?php echo $this->addIcon('icon-48-import','import', 				JText::_('COM_AWOCOUPON_IMP_IMPORT'));?>
					<?php echo $this->addIcon('icon-48-report','reports', 				JText::_('COM_AWOCOUPON_RPT_REPORTS'));?>
					<?php echo $this->addIcon('icon-48-config','config', 				JText::_('COM_AWOCOUPON_CFG_CONFIGURATION'));?>
					<?php if ($this->include_installation) echo $this->addIcon('icon-48-installation','installation', 	JText::_('COM_AWOCOUPON_FI_INSTALLATION_CHECK')); ?>
				</div>
			</td>
			<td width="45%" valign="top">
			
			
			
			
			
				<?php
				if(version_compare( JVERSION, '1.6.0', 'ge' )) {
					echo JHtml::_('sliders.start', 'genstat-pane');
					echo JHtml::_('sliders.panel', JText::_( 'COM_AWOCOUPON_DH_GENERAL_STATISTICS' ), 'unapproved');
				}
				else {
					$this->pane   	= JPane::getInstance('sliders');
					echo $this->pane->startPane( 'genstat-pane' );
					echo $this->pane->startPanel( JText::_( 'COM_AWOCOUPON_DH_GENERAL_STATISTICS' ), 'unapproved' );
				}
				?>
				<div id="dash_generalstats" class="postbox " >
					<div class="inside">
						<div class="table">
							<table>
							<tr class="first">
								<td class="first b"><a href="index.php?option=com_awocoupon&view=coupons&filter_state=&filter_coupon_value_type=&filter_discount_type=&filter_function_type=&filter_template="><?php echo $this->genstats['total']; ?></a></td>
								<td class="t"><?php echo JText::_('COM_AWOCOUPON_DH_COUPON_TOTAL');?></td>
							</tr>
							<tr><td class="first b"><a href="index.php?option=com_awocoupon&view=coupons&filter_state=1&filter_coupon_value_type=&filter_discount_type=&filter_function_type=&filter_template="><?php echo $this->genstats['active']; ?></a></td>
								<td class=" t approved"><?php echo JText::_('COM_AWOCOUPON_DH_COUPON_ACTIVE');?></td>
							</tr>
							<tr><td class="first b"><a href="index.php?option=com_awocoupon&view=coupons&filter_state=-1&filter_coupon_value_type=&filter_discount_type=&filter_function_type=&filter_template="><?php echo $this->genstats['inactive']; ?></a></td>
								<td class=" t inactive"><?php echo JText::_('COM_AWOCOUPON_DH_COUPON_INACTIVE');?></td>
							</tr>
							<tr><td class="first b"><a href="index.php?option=com_awocoupon&view=coupons&filter_state=-2&filter_coupon_value_type=&filter_discount_type=&filter_function_type=&filter_template="><?php echo $this->genstats['templates']; ?></a></td>
								<td class=" t template"><?php echo JText::_('COM_AWOCOUPON_CP_TEMPLATES');?></td>
							</tr>
							</table>
						</div>
					</div>
				</div>
				<?php
				if(version_compare( JVERSION, '1.6.0', 'ge' )) {
					echo JHtml::_('sliders.panel', JText::_( 'COM_AWOCOUPON_DH_COUPON_RECENT' ), 'mostpop-pane' );
				} else {
					echo $this->pane->endPanel();
					echo $this->pane->startPanel( JText::_( 'COM_AWOCOUPON_DH_COUPON_RECENT' ), 'mostpop-pane' );
				}
				?>
				<table class="adminlist">
					<thead>
						<tr>
							<td class="title"><strong><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON_CODE' ); ?></strong></td>
							<td class="title"><strong><?php echo JText::_( 'COM_AWOCOUPON_CP_FUNCTION_TYPE' ); ?></strong></td>
							<td class="title"><strong><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE_TYPE' ); ?></strong></td>
							<td class="title"><strong><?php echo JText::_( 'COM_AWOCOUPON_CP_VALUE' ); ?></strong></td>
						</tr>
					</thead>
					<tbody>
						<?php
						$k = 0;
						for ($i=0, $n=count($this->lastentered); $i < $n; $i++) {
						$row = $this->lastentered[$i];
						$link 		= 'index.php?option=com_awocoupon&amp;controller=coupons&amp;task=editcoupon&amp;cid[]='. $row->id;
						$function_type = $this->AWOCOUPON_lang['function_type'][$row->function_type];
						$coupon_value_type = $row->function_type=='parent' ? '' : $this->AWOCOUPON_lang['coupon_value_type'][$row->coupon_value_type];
					?>
						<tr class="row<?php echo $k; ?>">
							<td width="40%">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?>::<?php echo $row->coupon_code; ?>">
									<a href="<?php echo $link; ?>">
										<?php echo htmlspecialchars($row->coupon_code, ENT_QUOTES, 'UTF-8'); ?>
									</a>
								</span>
							</td>
							<td align=""><?php echo $function_type; ?></td>
							<td align=""><?php echo $coupon_value_type; ?></td>
							<td width="5%" align="right"><?php echo $row->coupon_value; ?></td>
						</tr>
						<?php $k = 1 - $k; } ?>
					</tbody>
				</table>
				<?php
				if(version_compare( JVERSION, '1.6.0', 'ge' )) {
					echo JHtml::_('sliders.end');
				}
				else {
					echo $this->pane->endPanel(); 
				}
				
				?>
		
		
				<table class="adminlist">
				<thead><tr><th colspan="2"><?php echo JText::_( 'COM_AWOCOUPON_RPT_STATUS'); //JText::_( 'COM_AWOCOUPON_DH_CHECK_UPDATE' ); ?></th></tr></thead>
					<?php
					//printrx($this->pluginrow);
					foreach($this->pluginrow['folders'] as $plgname) {
						$plugin_text = '';
						$plugin_button = '';
						$plgname .= '-awocoupon';
						if(empty($this->pluginrow['data'][$plgname])) {
							$plugin_text = '<span style="color:red;"><b>'.JText::_('COM_AWOCOUPON_FI_NOT_INSTALLED').'</b></span> (<a href="javascript:g_plugin_install(\''.$plgname.'\');">'.JText::_('COM_AWOCOUPON_GBL_INSTALL').'</a>)';
						} else {
							if($this->pluginrow['data'][$plgname]->enabled == 1) {
								$plugin_text = '<span style="color:green;"><b>'.JText::_('COM_AWOCOUPON_CP_PUBLISHED').'</b></span>';
								$plugin_button = '<img src="'.com_awocoupon_ASSETS.'/images/published.png" width="16" height="16"  border="0" 
														alt="'.JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ).'" title="'.JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ).'"
														/>
												<img src="'.com_awocoupon_ASSETS.'/images/unpublished.png" width="16" height="16" class="hand" border="0" 
														alt="'.JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ).'" title="'.JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ).'" 
														onclick="plugin_status(0,'.$this->pluginrow['data'][$plgname]->id.')" style="cursor:pointer;" />
														';
							} else {
								$plugin_text = '<span style="color:red;"><b>'.JText::_('COM_AWOCOUPON_CP_UNPUBLISHED').'</b></span>';
								$plugin_button = '<img src="'.com_awocoupon_ASSETS.'/images/published.png" width="16" height="16" class="hand" border="0" 
														alt="'.JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ).'" title="'.JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ).'"
														onclick="plugin_status(1,'.$this->pluginrow['data'][$plgname]->id.')" style="cursor:pointer;" />
												<img src="'.com_awocoupon_ASSETS.'/images/unpublished.png" width="16" height="16" border="0" 
														alt="'.JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ).'" title="'.JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' ).'" 
														/>
														';
							}
						}
						echo '<tr>
								<td width="33%">'.(empty($this->pluginrow['data'][$plgname]) ? $plgname : $this->pluginrow['data'][$plgname]->name).'</td>
								<td> <div style="float:left;padding-top:5px;">'.$plugin_text.'</div>
									'.(!empty($plugin_button) ? '<div style="padding:5px 0 0 10px;float:left;vertical-align:top;">[ '.$plugin_button.' ]</div>' : '').'
									<div style="clear:both;"></div>
								</td>
							</tr>';
					}
					?>
				</table>
		
		
		
				<!--<table class="adminlist">
				<thead><tr><th colspan="2"><?php echo JText::_( 'COM_AWOCOUPON_LI_LICENSE' ); ?></th></tr></thead>
				<tbody>
				<?php if(!empty($this->license->l)) { ?>
					<tr><td width="33%"><?php echo JText::_( 'COM_AWOCOUPON_LI_LICENSE' ); ?>:</td>
						<td><?php echo $this->license->l; ?></td>
					</tr>
					<tr><td width="33%"><?php echo JText::_( 'COM_AWOCOUPON_LI_WEBSITE' ); ?>:</td>
						<td><?php echo $this->license->url; ?></td>
					</tr>
					<tr><td width="33%"><?php echo JText::_( 'COM_AWOCOUPON_CP_EXPIRATION' ).':'; ?></td>
						<td><b><?php echo empty($this->license->exp) 
										? '<font color="green">'.JText::_( 'COM_AWOCOUPON_LI_PERMANENT' ).'</font>' 
										: '<font color="red">'.date('Y-m-d H:i:s',$this->license->exp).'</font> <span style="font-size:17px;">&raquo;</span> <a href="http://awodev.com/documentation/frequently-asked-questions#license-permanent" target="_blank">'.JText::_( 'COM_AWOCOUPON_LI_PERMANENT_MAKE' ).'</a>';?></b>
						</td>
					</tr>
				<?php }  else { ?>
					<tr><td><b><span style="color:red;font-size:16px;"><?php echo JText::_( 'COM_AWOCOUPON_LI_INVALID_LICENSE' ); ?></span></b></td></tr>
				<?php } ?>
				</tbody>
				</table>-->


		
		
		
		
		
				<!--<table class="adminlist">
				<thead><tr><th colspan="2"><?php echo JText::_( 'COM_AWOCOUPON_DH_CHECK_UPDATE' ); ?></th></tr></thead>
				<tbody>
				<?php if($this->check['connect'] == 0) : ?>
					<tr><td colspan="2"><?php echo '<b><font color="red">'.JText::_( 'COM_AWOCOUPON_DH_CONNECTION_FAILED' ).'</font></b>'; ?></td></tr>
				<?php elseif ($this->check['enabled'] == 1) : ?>
					<tr><td colspan="2"><?php
								if ($this->check['current'] == 0) {
									echo '<strong><font color="green">'.JText::_( 'COM_AWOCOUPON_DH_LATEST_VERSION_INSTALLED' ).'</font></strong>';
								} elseif( $this->check['current'] == -1 ) {
									require_once JPATH_COMPONENT_ADMINISTRATOR.'/liveupdate/liveupdate.php';
									echo '<b><font color="red">'.JText::_( 'COM_AWOCOUPON_DH_CURRENT_VERSION_INSTALLED' ).'</font></b> &nbsp; &nbsp; [ <a href="http://awodev.com/documentation/awocoupon-pro/release-notes" target="_blank">release notes</a> ] <div style="float:right;">'.LiveUpdate::getIcon().'</div>';
								} else {
									echo '<b><font color="orange">'.JText::_( 'COM_AWOCOUPON_DH_NEWER_VERSION' ).'</font></b>';
								}
							?>
						</td>
					</tr>
					<?php if ($this->check['current'] != 0) : ?>
					<tr>
						<td width="33%"><?php echo JText::_( 'COM_AWOCOUPON_DH_LATEST_VERSION' ).':'; ?></td>
						<td><?php echo $this->check['version'].' ('.$this->check['released'].')'; ?></td>
					</tr>
				<?php 	endif;
					endif; 
				?>
				<tr>
					<td width="33%"><?php echo JText::_( 'COM_AWOCOUPON_DH_INSTALLED_VERSION' ).':'; ?></td>
					<td><?php echo $this->check['current_version']; ?></td>
				</tr>
				</tbody>
				</table>-->
		
		
		
		
		
			</td>
		</tr>
	</table>
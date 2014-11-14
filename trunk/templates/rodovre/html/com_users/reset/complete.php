<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.noframes');
if(JRequest::getVar('complete')){
?>
<div id="newsletter-page">
    <div id="w-newsletter">
        <div class="newsletter-title">
            <h2>Kvittering</h2>
        </div>
        <div class="newsletter-content">
            <p>Kære bruger</p>
            <p>Dit password er blevet ændret....venligst login <a href="javascript:void(0);" data-reveal-id="myModal">her</a></p>
            <p>Med venlig hilsen<br />Amager Isenkram</p>
        </div>
    </div>
</div>
<?php 
} else {
?>
<div id="newsletter-page">
<div class="reset-complete<?php echo $this->pageclass_sfx?>" id="w-newsletter">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>
	<div class="newsletter-title">
        <h2>Glemt din adgangskode?</h2>
        <div class="come-back2">
            <a href="javascript:history.back();">Tilbage</a>
        </div><!--.come-back2-->
    </div>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.complete'); ?>" method="post" class="form-validate  newsletter-content">

		<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
		<p><?php echo JText::_($fieldset->label); ?></p>		<fieldset>
			<dl>
			<?php foreach ($this->form->getFieldset($fieldset->name) as $name => $field): ?>
				<dt><?php echo $field->label; ?></dt>
				<dd><?php echo $field->input; ?></dd>
			<?php endforeach; ?>
			</dl>
		</fieldset>
		<?php endforeach; ?>

		<div>
			<button type="submit" class="validate bnt-send2"  style="border:none; cursor:pointer;"></button>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
</div>
<?php }?>
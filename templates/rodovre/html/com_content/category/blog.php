<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

?>
<style>

</style>
<div id="news-page">
    <ul>
    	<?php foreach ($this->intro_items as $key => &$item) :
		$this->item = &$item; 
		$images = json_decode($this->item->images);
		?>
        <li>
            <div class="news-item-img">
                <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>"><img width="192" height="127" alt="" src="<?php echo htmlspecialchars($images->image_intro); ?>"></a>
            </div><!--.news-item-img-->
            <div class="news-item-content">
                <h2><a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>"><?php echo $this->escape($this->item->title); ?></a></h2>
                <h3><?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC4'))); ?></h3>
                <p><?php echo $this->item->introtext; ?>
                <span><a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">Læs mere</a></span></p>
            </div><!--.news-item-content-->
        </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
<?php  endif; ?>
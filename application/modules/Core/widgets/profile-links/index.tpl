<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9162 2011-08-15 20:58:43Z shaun $
 * @author     John
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_links').getParent();
    $('profile_links_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_links_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_links_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_links_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>


<ul class="profile_links" id="profile_links">
  <?php foreach( $this->paginator as $link ): ?>
    <li>
      <?php if($link->photo_id != 0):?>
      <div class="profile_links_photo">
        <?php echo $this->htmlLink($link->getHref(), $this->itemPhoto($link)) ?>
      </div>
      <?php endif;?>
      <div class="profile_links_info">
        <div class="profile_links_title">
          <?php echo $this->htmlLink($link->getHref(), $link->getTitle()) ?>
        </div>
        <div class="profile_links_description">
          <?php echo $this->htmlLink($link->getHref(), $link->getDescription()) ?>
        </div>
        <?php if( !$link->getOwner()->isSelf($link->getParent()) ): ?>
        <div class="profile_links_author">
          <?php echo $this->translate('Posted by %s', $link->getOwner()->__toString()) ?>
          <?php echo $this->timestamp($link->creation_date) ?>
        </div>
        <?php endif; ?>
      </div>

      <?php
      if ($link->isDeletable()){
        echo "<br/>".$this->htmlLink(array('route' => 'default', 'module' => 'core', 'controller' => 'link', 'action' => 'delete', 'link_id' => $link->link_id, 'format' => 'smoothbox'), $this->translate('Delete Link'), array(
          'class' => 'buttonlink smoothbox icon_delete'
        ));
      }
      ?>
    </li>
  <?php endforeach; ?>
</ul>

<div>
  <div id="profile_links_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_links_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>

<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    Rss
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9339 2011-09-29 23:03:01Z john $
 * @author     John
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    $$('.rss_desc').enableLinks();
  });
</script>

<?php if( !empty($this->channel) ): ?>
  <ul>
    <?php $count=0;foreach( $this->channel['items'] as $item ): $count++ ?>
      <li class="rss_item">
        <div class="rss_item_<?php echo $count ?>">
          <?php echo $this->htmlLink($item['guid'], $item['title'],
              array('target' => '_blank', 'class' => 'rss_link_' . $count)) ?>
          <p class="rss_desc">
            <?php if( $this->strip ): ?>
              <?php echo $this->string()->truncate($this->string()->stripTags($item['description']), 350) ?>
            <?php else: ?>
              <?php echo $item['description'] ?>
            <?php endif ?>
          </p>
        </div>
        <div class="rss_time">
          <?php echo $this->locale()->toDateTime(strtotime($item['pubDate']), array('size' => 'long')) ?>
        </div>
      </li>
    <?php endforeach; ?>
    <li class="rss_last_row">
      <div>
        &nbsp;
      </div>
      <div>
        &#187; <?php echo $this->htmlLink($this->channel['link'], $this->translate("More")) ?>
      </div>
    </li>
  </ul>
<?php endif; ?>

<?php if( false ): ?>
  <br />
  <span class="rss_fetched_timestamp">
    <?php if( $this->isCached ): ?>
      <?php echo $this->translate('Results last fetched at %1$s',
          $this->locale()->toDateTime($this->channel['fetched'])) ?>
    <?php else: ?>
      <?php echo $this->translate('Results are current') ?>
    <?php endif ?>
  </span>
<?php endif ?>

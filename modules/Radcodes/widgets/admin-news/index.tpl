<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

<div class="admin_home_news">
  <h3 class="sep">
    <span><?php echo $this->translate("News & Updates") ?></span>
  </h3>

  <?php if( !empty($this->channel) ): ?>
    <ul>
      <?php foreach( $this->channel['items'] as $item ): ?>
        <li>
          <div class="admin_home_news_date">
            <?php echo $this->locale()->toDate(strtotime($item['pubDate']), array('size' => 'long')) ?>
          </div>
          <div class="admin_home_news_info">
            <a href="<?php echo $item['guid'] ?>" target="_blank">
              <?php echo $item['title'] ?>
            </a>
            <span class="admin_home_news_blurb">
              <?php $desc = strip_tags($item['description']); if( Engine_String::strlen($desc) > 350 ): ?>
                <?php echo Engine_String::substr($desc, 0, 350) ?>...
              <?php else: ?>
                <?php echo $desc ?>
              <?php endif; ?>
            </span>
          </div>
        </li>
      <?php endforeach; ?>
      <li>
        <div class="admin_home_news_date">
          &nbsp;
        </div>
        <div class="admin_home_news_info">
          &#187; <a href="http://www.socialengine.net"><?php echo $this->translate("More Radcodes News") ?></a>
        </div>
      </li>
    </ul>

  <?php elseif( $this->badPhpVersion ): ?>

  <div>
    <?php echo $this->translate('The news feed requires the PHP DOM extension.') ?>
  </div>

  <?php else: ?>

  <div>
    <?php echo $this->translate('There are no news items, or we were unable to fetch the news.') ?>
  </div>

  <?php endif; ?>
</div>
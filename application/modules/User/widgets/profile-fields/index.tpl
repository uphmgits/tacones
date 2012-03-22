<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>
<div id='profile_photo_holder'>
  <?=$this->itemPhoto($this->subject(), 'normal')?>
</div>
<div id='profile_fields_details'>
  <h2><?=$this->subject()->getTitle()?></h2>
  <?php if( !empty($this->aliasValues['favdesign']) ) : ?>
    <h4><?=$this->translate('favorite designer')?></h4>
    <?php $favList = explode(",", $this->aliasValues['favdesign']);?>
    <ul class="profile-favorites">
      <?php foreach($favList as $fav) : ?>
        <li><?=trim($fav)?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if ( !empty($this->aliasValues['about_me']) ) : ?>
    <h4><?=$this->translate('about me')?></h4>
    <div><?=trim($this->aliasValues['about_me'])?></div>
  <?php endif; ?>

  <table>
    <tr>
      <th>
        <h4 style="display: inline"><?=$this->translate('rating')?></h4>
        <span class="review_rating_star_big">
          <span style="width: <?php echo $this->average_rating * 20 ?>%"></span>
        </span>
      </th>
      <th><h4><?=$this->translate('following')?></h4></th>
      <th><h4><?=$this->translate('followers')?></h4></th>
      <th><h4><?=$this->translate('comments')?></h4></th>
    </tr>
    <tr>
      <td><h4 style="display: inline"><?=$this->translate('reviews')?></h4> <?=$this->total_review?></td>
      <td>0</td>
      <td>0</td>
      <td>0</td>
    </tr>
  </table>  
</div>

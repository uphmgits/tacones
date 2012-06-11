
<div id='profile_photo_holder'>
  <?=$this->itemPhoto($this->subject(), 'thumb_profile')?>
</div>
<div id='profile_fields_details'>
  <?php if( $this->canBan ) : ?>
    <form action="" method="post">
      <button type="submit" name="ban_option">
        <?php if( $this->notBanned ) echo $this->translate('Ban'); else echo $this->translate('Unban');?>
      </button>
    </form>
  <?php endif; ?>
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
        <h4 style="display: inline"><?=$this->translate('rated')?></h4>
        <span class="review_rating_star">
          <span style="width: <?php echo $this->average_rating * 20 ?>%"></span>
        </span>
      </th>
      <th><h4><?=$this->translate('following')?></h4></th>
      <th><h4><?=$this->translate('followers')?></h4></th>
      <th><h4><?=$this->translate('comments')?></h4></th>
    </tr>
    <tr>
      <td><h4 style="display: inline"><?=$this->translate('reviews')?></h4>  <?=$this->total_review?></td>
      <td><center><?=$this->countFollowing?></center></td>
      <td><center><?=$this->countFollowers?></center></td>
      <td><center><?=$this->countComents?></center></td>
    </tr>
  </table>  
</div>

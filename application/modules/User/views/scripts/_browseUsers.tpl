<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _browseUsers.tpl 8139 2011-01-05 02:01:48Z jung $
 * @author     John
 */
?>
<h3>
  <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
</h3>

<ul id="browsemembers_ul">
  <?php foreach( $this->users as $user ): ?>
    <li>
      <?=$this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile'))?>

      <?php
        $total_review = Engine_Api::_()->review()->getUserReviewCount($user);
        $average_rating = Engine_Api::_()->review()->getUserAverageRating($user);
        $total_recommend = Engine_Api::_()->review()->getUserRecommendCount($user);	
      ?>
      <div class="review_profile_rating">
        <a href="<?php echo $this->url(array('id'=>$user->getIdentity()), 'review_user', true)?>">
          <span class="review_rating_star_small"><span style="width: <?=$average_rating * 20?>%"></span></span>
        </a>
      </div>

      <div class='browsemembers_results_info'>
          <?=$this->htmlLink($user->getHref(), $user->getTitle())?>
          
          <?php /*echo $user->status; ?>
          <?php if( $user->status != "" ): ?>
            <div>
              <?php echo $this->timestamp($user->status_date) ?>
            </div>
          <?php endif;*/ ?>

      </div>

      <?php /*if( $this->viewer()->getIdentity() ): ?>
        <div class='browsemembers_results_links'>
          <?php echo $this->userFriendship($user) ?>
        </div>
      <?php endif;*/ ?>
    </li>
  <?php endforeach; ?>
</ul>

<?php if( $this->users ): ?>
  <div class='browsemembers_viewmore' id="browsemembers_viewmore">
    <?=$this->paginationControl($this->users)?>
  </div>
<?php endif; ?>

<script type="text/javascript">
  page = '<?php echo sprintf('%d', $this->page) ?>';
  totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
  userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
</script>

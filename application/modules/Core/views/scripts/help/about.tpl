<?php
/**
 * SocialEngineMarket
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2012 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */
?>

<h2><?php echo $this->translate('About Service') ?></h2>
<p>
  <?php 
  $str = $this->translate('_CORE_ABOUT_SERVICE');
  if ($str == strip_tags($str)) {
    // there is no HTML tags in the text
    echo nl2br($str);
  } else {
    echo $str;
  }
  ?>
</p>
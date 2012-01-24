<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<ul class="generic_list_widget">
  <?php foreach( $this->themes as $theme ): ?>
    <li>
      <?php if( $theme['theme_id'] == $this->activeTheme['theme_id'] ): ?>
        <?php echo $this->htmlLink(null, $theme['title']) ?>
      <?php else: ?>
        <?php echo $this->htmlLink(array(
          'reset' => true,
          'route' => 'default',
          'module' => 'core',
          'controller' => 'utility',
          'action' => 'theme',
          'theme_id' => $theme['theme_id'],
          'return_url' => base64_encode($_SERVER['REQUEST_URI']),
        ), $theme['title']) ?>
      <?php endif ?>
    </li>
  <?php endforeach ?>
</ul>

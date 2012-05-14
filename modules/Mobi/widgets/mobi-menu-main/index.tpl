<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9115 2011-07-28 02:30:13Z shaun $
 * @author     Charlotte
 */
?>

<ul class="navigation">
  <?php $count = 0;
    foreach( $this->navigation as $item ):
      $count++;
      $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
      'reset_params', 'route', 'module', 'controller', 'action', 'type',
      'visible', 'label', 'href'
      )));
      if( !isset($attribs['active']) ){
        $attribs['active'] = false;
      }

      // support allow custom menu items to be highlighted
      if( false !== strpos($attribs['class'], 'custom_') ){
        $uri = parse_url($attribs['uri'], PHP_URL_PATH);
        if( isset($_SERVER['REQUEST_URI']) && false !== strpos($_SERVER['REQUEST_URI'], $uri)){
          $attribs['active'] = true;
        }
      }
    ?>
      <li<?php echo($attribs['active']?' class="active"':'')?> style="width:<?php echo(($count == count($this->navigation))?(100-($count-1)*floor(100/count($this->navigation))):floor(100/count($this->navigation)))?>%;">
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
      </li>
  <?php endforeach; ?>
</ul>
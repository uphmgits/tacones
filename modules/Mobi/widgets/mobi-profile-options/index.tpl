<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8948 2011-05-19 21:47:38Z jung $
 * @author     Charlotte
 */
?>

<div id='profile_options'>


<ul>
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
    ?>
      <li<?php echo ($attribs['active'] ? ' class="active"' : '')?>>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
      </li>
  <?php endforeach; ?>
</ul>
    
</div>
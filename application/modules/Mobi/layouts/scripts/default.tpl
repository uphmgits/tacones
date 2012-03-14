<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: default.tpl 9379 2011-10-14 00:17:09Z john $
 * @author     Charlotte
 */
?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
  <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

  
  <?php // ALLOW HOOKS INTO META ?>
  <?php echo $this->hooks('onRenderLayoutMobileDefault', $this) ?>


  <?php // TITLE/META ?>
  <?php
    $counter = (int) $this->layout()->counter;
    $staticBaseUrl = $this->layout()->staticBaseUrl;
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->headTitle()
      ->setSeparator(' - ');
    $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
        . '-' . $request->getControllerName();
    $pageTitle = $this->translate($pageTitleKey);
    if( $pageTitle && $pageTitle != $pageTitleKey ) {
      $this
        ->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
    }
    $this
      ->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      ;
    $this->headMeta()
      ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', 'en-US');
    
    // Make description and keywords
    $description = '';
    $keywords = '';
    
    $description .= ' ' .$this->layout()->siteinfo['description'];
    $keywords = $this->layout()->siteinfo['keywords'];

    if( $this->subject() && $this->subject()->getIdentity() ) {
      $this->headTitle($this->subject()->getTitle());
      
      $description .= ' ' .$this->subject()->getDescription();
      if (!empty($keywords)) $keywords .= ',';
      $keywords .= $this->subject()->getKeywords(',');
    }
    
    $this->headMeta()->appendName('description', trim($description));
    $this->headMeta()->appendName('keywords', trim($keywords));
    $this->headMeta()->appendName('viewport', 'width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;');

    // Get body identity
    if( isset($this->layout()->siteinfo['identity']) ) {
      $identity = $this->layout()->siteinfo['identity'];
    } else {
      $identity = $request->getModuleName() . '-' .
          $request->getControllerName() . '-' .
          $request->getActionName();
    }
  ?>
  <?php echo $this->headTitle()->toString()."\n" ?>
  <?php echo $this->headMeta()->toString()."\n" ?>


  <?php // LINK/STYLES ?>
  <?php
    $this->headLink(array(
      'rel' => 'favicon',
      'href' => ( isset($this->layout()->favicon)
        ? $this->baseUrl() . $this->layout()->favicon
        : '/favicon.ico' ),
      'type' => 'image/x-icon'),
      'PREPEND');
    $themes = array();
    if( !empty($this->layout()->themes) ) {
      $themes = $this->layout()->themes;
    } else {
      $themes = array('default');
    }
    
    foreach( $themes as $theme ) {
      /*$this->headLink()
        ->prependStylesheet($staticBaseUrl . 'application/css.php?request=application/themes/'.$theme.'/mobile.css');
      */
      $this->headLink()
        ->prependStylesheet($staticBaseUrl . 'css.php?request=application/themes/'.$theme.'/mobile.css');
      if( $orientation == 'rtl' ) {
        // @todo add include for rtl
      }
    }
    

    // Process
    foreach( $this->headLink()->getContainer() as $dat ) {
      if( !empty($dat->href) ) {
        if( false === strpos($dat->href, '?') ) {
          $dat->href .= '?c=' . $counter;
        } else {
          $dat->href .= '&c=' . $counter;
        }
      }
    }
  ?>
  <?php echo $this->headLink()->toString()."\n" ?>
  <?php echo $this->headStyle()->toString()."\n" ?>

  <?php // TRANSLATE ?>
  <?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>

  <?php // SCRIPTS ?>
  <script type="text/javascript">
    <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>

    Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');
    
    en4.orientation = '<?php echo $orientation ?>';
    en4.core.environment = '<?php echo APPLICATION_ENV ?>';
    en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');
    en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
    en4.core.staticBaseUrl = '<?php echo $this->escape($staticBaseUrl) ?>';
    en4.core.loader = new Element('img', {src: en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'});
    
    <?php if( $this->subject() ): ?>
      en4.core.subject = {
        type : '<?php echo $this->subject()->getType(); ?>',
        id : <?php echo $this->subject()->getIdentity(); ?>,
        guid : '<?php echo $this->subject()->getGuid(); ?>'
      };
    <?php endif; ?>
    <?php if( $this->viewer()->getIdentity() ): ?>
      en4.user.viewer = {
        type : '<?php echo $this->viewer()->getType(); ?>',
        id : <?php echo $this->viewer()->getIdentity(); ?>,
        guid : '<?php echo $this->viewer()->getGuid(); ?>'
      };
    <?php endif; ?>
    if( <?php echo ( Zend_Controller_Front::getInstance()->getRequest()->getParam('ajax', false) ? 'true' : 'false' ) ?> ) {
      en4.core.dloader.attach();
    }
    
    <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
  </script>
  <?php
    $this->headScript()
      ->prependFile($staticBaseUrl . 'externals/smoothbox/smoothbox4.js')
      ->prependFile($staticBaseUrl . 'application/modules/User/externals/scripts/core.js')
      ->prependFile($staticBaseUrl . 'application/modules/Core/externals/scripts/core.js')
      ->prependFile($staticBaseUrl . 'externals/chootools/chootools.js')
      ->prependFile($staticBaseUrl . 'externals/mootools/mootools-1.2.5.1-more-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
      ->prependFile($staticBaseUrl . 'externals/mootools/mootools-1.2.5-core-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js');
    //->prependFile($staticBaseUrl . 'externals/mootools/mootools-more-1.3.2.1-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
    //->prependFile($staticBaseUrl . 'externals/mootools/mootools-core-1.3.2-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js');
    // Process
    foreach( $this->headScript()->getContainer() as $dat ) {
      if( !empty($dat->attributes['src']) ) {
        if( false === strpos($dat->attributes['src'], '?') ) {
          $dat->attributes['src'] .= '?c=' . $counter;
        } else {
          $dat->attributes['src'] .= '&c=' . $counter;
        }
      }
    }
  ?>
  <?php echo $this->headScript()->toString()."\n" ?>
  
</head>
<body id="global_page_<?php echo $identity ?>">
  <div id="global_header">
    <?php echo $this->content('header_mobi') ?>
  </div>
  <div id='global_wrapper'>
    <div id='global_content'>
      <?php //echo $this->content('global-user', 'before') ?>
      <?php echo $this->layout()->content ?>
      <?php //echo $this->content('global-user', 'after') ?>
    </div>
  </div>
  <div id="global_footer">
    <?php echo $this->content('footer_mobi') ?>
  </div>
</body>
</html>

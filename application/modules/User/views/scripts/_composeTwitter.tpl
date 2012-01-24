<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composeFacebook.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
?>

<?php
  $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
  $twitter = $twitterTable->getApi();

  // Not connected
  if( !$twitter || !$twitterTable->isConnected() ) {
    return;
  }

  // Disabled
  if( 'publish' != Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable ) {
    return;
  }

  // Add script
  $this->headScript()
      ->appendFile($this->baseUrl(). '/application/modules/User/externals/scripts/composer_twitter.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Twitter({
      lang : {
        'Publish this on Twitter' : '<?php echo $this->translate('Publish this on Twitter') ?>'
      }
    }));
  });
</script>

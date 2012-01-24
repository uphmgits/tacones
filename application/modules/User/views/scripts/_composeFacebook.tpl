<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composeFacebook.tpl 8968 2011-06-02 00:48:35Z john $
 * @author     Steve
 */
?>

<?php
  $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
  $facebookApi = $facebookTable->getApi();
  // Disabled
  if( !$facebookApi ||
      'publish' != Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
    return;
  }
  // Not logged in
  if( !$facebookTable->isConnected() ) {
    return;
  }
  // Not logged into correct facebook account
  if( !$facebookTable->checkConnection() ) {
    return; 
  }

  // Add script
  $this->headScript()
      ->appendFile($this->baseUrl() . '/application/modules/User/externals/scripts/composer_facebook.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Facebook({
      lang : {
        'Publish this on Facebook' : '<?php echo $this->translate('Publish this on Facebook') ?>'
      }
    }));
  });
</script>

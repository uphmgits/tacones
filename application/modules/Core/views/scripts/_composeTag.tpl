<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composeTag.tpl 9325 2011-09-27 00:11:15Z john $
 * @author     John
 */
?>

<?php $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer_tag.js') ?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Tag({
      suggestOptions : {
        'url' : '<?php echo $this->url(array(), 'default', true) . '/user/friends/suggest' ?>',
        'data' : {
          'format' : 'json'
        }
      },
      'suggestProto' : 'local',
      'suggestParam' : <?php echo $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) ?>
    }));
  });
</script>
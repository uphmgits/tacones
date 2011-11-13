<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 9330 2011-09-27 23:33:35Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'utility', 'action' => 'advertisement'), 'default', true) ?>';
    var processClick = window.processClick = function(adcampaign_id, ad_id) {
      (new Request.JSON({
        'format': 'json',
        'url' : url,
        'data' : {
          'format' : 'json',
          'adcampaign_id' : adcampaign_id,
          'ad_id' : ad_id
        }
      })).send();
    }
  });
</script>

<div onclick="javascript:processClick(<?php echo $this->campaign->adcampaign_id.", ".$this->ad->ad_id?>)">
  <?php echo $this->ad->html_code; ?>
</div>

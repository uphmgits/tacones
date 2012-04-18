<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

?>
<script type="text/javascript" language="javascript">
function <?php echo $this->function; ?>()
{
  var pint = <?php echo $this->marker->getName(); ?>.getPosition();
  $('<?php echo $this->lat_id; ?>').set('value', pint.lat().toFixed(6));
  $('<?php echo $this->lng_id; ?>').set('value', pint.lng().toFixed(6));  
}
</script>

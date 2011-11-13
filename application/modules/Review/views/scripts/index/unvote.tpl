<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
?>

<?php echo $this->partial('index/feedback.tpl', 'review', array(
  'review'=>$this->review,
  'error'=>$this->error,
  'message'=>$this->message
))?>

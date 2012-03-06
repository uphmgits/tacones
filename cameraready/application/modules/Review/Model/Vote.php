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
 
 
 
class Review_Model_Vote extends Core_Model_Item_Abstract
{
  // Properties

  protected $_parent_type = 'user';
  
  protected $_owner_type = 'user';

  protected $_searchTriggers = false;

  protected $_parent_is_owner = true;

  
}
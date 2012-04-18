<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: install.php 7244 2011-03-19 01:49:53Z nexus $
 * @author     Nexus
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 
 * * 
 */

class Marketplaceups_Installer extends Engine_Package_Installer_Module
{
  function onInstall()
  { 
  
    $plugin = "Marketplaceups";
    $server = $_SERVER['SERVER_NAME'];
    $f = file("http://socialenginemarket.com/licenses/".md5($server.$plugin));

    if (trim($f[0]) != "1" && 0) {
    	return $this->_error('Please register your copy of plugin - <a class="smoothbox" href="http://socialenginemarket.com/user_product_register.php?plugin='.$plugin.'&server='.$server.'">Click Here</a>');  
    }
    
    parent::onInstall();
  }
}
 
 
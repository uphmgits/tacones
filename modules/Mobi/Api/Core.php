<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 8931 2011-05-12 20:26:03Z jung $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Mobi_Api_Core extends Core_Api_Abstract
{
  public function isMobile()
  {
    // No UA defined?
    if( !isset($_SERVER['HTTP_USER_AGENT']) ) {
      return false;
    }

    // Windows is (generally) not a mobile OS
    if( false !== stripos($_SERVER['HTTP_USER_AGENT'], 'windows') &&
        false === stripos($_SERVER['HTTP_USER_AGENT'], 'windows phone os')) {
      return false;
    }

    // Sends a WAP profile header
    if( isset($_SERVER['HTTP_PROFILE']) ||
        isset($_SERVER['HTTP_X_WAP_PROFILE']) ) {
      return true;
    }

    // Accepts WAP as a valid type
    if( isset($_SERVER['HTTP_ACCEPT']) &&
        false !== stripos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') ) {
      return true;
    }

    // Is Opera Mini
    if( isset($_SERVER['ALL_HTTP']) &&
        false !== stripos($_SERVER['ALL_HTTP'], 'OperaMini') ) {
      return true;
    }

    if( preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', $_SERVER['HTTP_USER_AGENT']) ) {
      return true;
    }

    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
      'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird',
      'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric',
      'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c',
      'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi',
      'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana',
      'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany',
      'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal',
      'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-',
      'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr',
      'webc', 'winw', 'winw', 'xda ', 'xda-'
    );

    if( in_array($mobile_ua, $mobile_agents) ) {
      return true;
    }
    
    return false;
  }
}

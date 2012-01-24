<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IpnController.php 9039 2011-06-29 23:38:56Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Payment_IpnController extends Core_Controller_Action_Standard
{
  public function __call($method, array $arguments)
  {
    $params = $this->_getAllParams();
    $gatewayType = $params['action'];
    $gatewayId = ( !empty($params['gateway_id']) ? $params['gateway_id'] : null );
    unset($params['module']);
    unset($params['controller']);
    unset($params['action']);
    unset($params['rewrite']);
    unset($params['gateway_id']);
    if( !empty($gatewayType) && 'index' !== $gatewayType ) {
      $params['gatewayType'] = $gatewayType;
    } else {
      $gatewayType = null;
    }

    // Log ipn
    $ipnLogFile = APPLICATION_PATH . '/temporary/log/payment-ipn.log';
    file_put_contents($ipnLogFile,
        date('c') . ': ' .
        print_r($params, true),
        FILE_APPEND);

    try {

      // Get gateways
      $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
      $gateways = $gatewayTable->fetchAll(array('enabled = ?' => 1));

      // Try to detect gateway
      $activeGateway = null;
      foreach( $gateways as $gateway ) {
        $gatewayPlugin = $gateway->getPlugin();

        // Action matches end of plugin
        if( $gatewayType &&
            substr(strtolower($gateway->plugin), - strlen($gatewayType)) == strtolower($gatewayType) ) {
          $activeGateway = $gateway;
        } else if( $gatewayId && $gatewayId == $gateway->gateway_id ) {
          $activeGateway = $gateway;
        } else if( method_exists($gatewayPlugin, 'detectIpn') &&
            $gatewayPlugin->detectIpn($params) ) {
          $activeGateway = $gateway;
        }
      }

    } catch( Exception $e ) {
      // Gateway detection failed
      file_put_contents($ipnLogFile,
          date('c') . ': ' .
          'Gateway detection failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }

    // Gateway could not be detected
    if( !$activeGateway ) {
      file_put_contents($ipnLogFile,
          date('c') . ': ' .
          'Active gateway could not be detected.',
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }

    // Validate ipn
    try {
      $gateway = $activeGateway;
      $gatewayPlugin = $gateway->getPlugin();
      
      $ipn = $gatewayPlugin->createIpn($params);
    } catch( Exception $e ) {
      // IPN validation failed
      file_put_contents($ipnLogFile,
          date('c') . ': ' .
          'IPN validation failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }

    
    // Process IPN
    try {
      $gatewayPlugin->onIpn($ipn);
    } catch( Exception $e ) {
      $gatewayPlugin->getGateway()->getLog()->log($e, Zend_Log::ERR);
      // IPN validation failed
      file_put_contents($ipnLogFile,
          date('c') . ': ' .
          'IPN processing failed: ' . $e->__toString(),
          FILE_APPEND);
      echo 'ERR';
      exit(1);
    }

    // Exit
    echo 'OK';
    exit(0);
  }
}
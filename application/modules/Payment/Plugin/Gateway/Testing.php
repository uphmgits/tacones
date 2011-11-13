<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Testing.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Payment_Plugin_Gateway_Testing extends Engine_Payment_Plugin_Abstract
{
  protected $_gatewayInfo;
  
  protected $_gateway;


  
  // General

  /**
   * Constructor
   */
  public function __construct(Zend_Db_Table_Row_Abstract $gatewayInfo)
  {
    $this->_gatewayInfo = $gatewayInfo;
  }
  
  /**
   * Get the service API
   *
   * @return Engine_Service_Testing
   */
  public function getService()
  {
    return $this->getGateway()->getService();
  }

  /**
   * Get the gateway object
   *
   * @return Engine_Payment_Gateway_Testing
   */
  public function getGateway()
  {
    if( null === $this->_gateway ) {
      $class = 'Engine_Payment_Gateway_Testing';
      Engine_Loader::loadClass($class);
      $gateway = new $class(array(
        'config' => (array) $this->_gatewayInfo->config,
        'testMode' =>  true, //$this->_gatewayInfo->test_mode,
        'currency' => Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'),
      ));
      if( !($gateway instanceof Engine_Payment_Gateway) ) {
        throw new Engine_Exception('Plugin class not instance of Engine_Payment_Gateway');
      }
      $this->_gateway = $gateway;
    }

    return $this->_gateway;
  }



  // Actions

  /**
   * Create a transaction object from specified parameters
   *
   * @return Engine_Payment_Transaction
   */
  public function createTransaction(array $params)
  {
    $transaction = new Engine_Payment_Transaction($params);
    $transaction->process($this->getGateway());
    return $transaction;
  }

  /**
   * Create an ipn object from specified parameters
   *
   * @return Engine_Payment_Ipn
   */
  public function createIpn(array $params)
  {
    $ipn = new Engine_Payment_Ipn($params);
    $ipn->process($this->getGateway());
    return $ipn;
  }

  public function detectIpn(array $params)
  {
    return false; // Never detect this as an IPN, or it will break real IPNs
  }



  // SE Specific

  /**
   * Create a transaction for a subscription
   *
   * @param User_Model_User $user
   * @param Zend_Db_Table_Row_Abstract $subscription
   * @param Zend_Db_Table_Row_Abstract $package
   * @param array $params
   * @return Engine_Payment_Gateway_Transaction
   */
  public function createSubscriptionTransaction(User_Model_User $user,
      Zend_Db_Table_Row_Abstract $subscription,
      Payment_Model_Package $package,
      array $params = array())
  {
    // Create transaction
    $transaction = $this->createTransaction($params);
    return $transaction;
  }

  /**
   * Process return of subscription transaction
   *
   * @param Payment_Model_Order $order
   * @param array $params
   */
  public function onSubscriptionTransactionReturn(
      Payment_Model_Order $order, array $params = array())
  {
    // Check that gateways match
    if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }

    // Get related info
    $user = $order->getUser();
    $subscription = $order->getSource();
    $package = $subscription->getPackage();

    // Check subscription state
    if( $subscription->status == 'active' ||
        $subscription->status == 'trial') {
      return 'active';
    } else if( $subscription->status == 'pending' ) {
      return 'pending';
    }

    // Let's log it
    $this->getGateway()->getLog()->log('Return: '
        . print_r($params, true), Zend_Log::INFO);
    
    // Should we accept this?
    
    // Update order with profile info and complete status?
    $order->state = 'complete';
    $order->gateway_order_id = 0; // Hack
    $order->save();
    
      // Insert transaction
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'payment');
    $transactionsTable->insert(array(
      'user_id' => $order->user_id,
      'gateway_id' => $this->_gatewayInfo->gateway_id,
      'timestamp' => new Zend_Db_Expr('NOW()'),
      'order_id' => $order->order_id,
      'type' => 'payment',
      'state' => 'okay',
      'gateway_transaction_id' => crc32(microtime() . $order->order_id), // Hack
      'amount' => $package->price,
      'currency' => $this->getGateway()->getCurrency(),
    ));

    // Get benefit setting
    $giveBenefit = Engine_Api::_()->getDbtable('transactions', 'payment')
        ->getBenefitStatus($user);
    
    $giveBenefit = true; // Need this 
    
    // Enable now
    if( $giveBenefit ) {

      // Update subscription
      $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
      $subscription->gateway_profile_id = crc32(time() . $order->order_id); // Hack
      $subscription->onPaymentSuccess();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_active', array(
          'subscription_title' => $package->title,
          'subscription_description' => $package->description,
          'subscription_terms' => $package->getPackageDescription(),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }

      return 'active';
    }

    // Enable later
    else {

      // Update subscription
      $subscription->gateway_id = $this->_gatewayInfo->gateway_id;
      $subscription->gateway_profile_id = crc32(time() . $order->order_id); // Hack
      $subscription->onPaymentPending();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'payment_subscription_pending', array(
          'subscription_title' => $package->title,
          'subscription_description' => $package->description,
          'subscription_terms' => $package->getPackageDescription(),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }

      return 'pending';
    }
  }

  /**
   * Process ipn of subscription transaction
   *
   * @param Payment_Model_Order $order
   * @param Engine_Payment_Ipn $ipn
   */
  public function onSubscriptionTransactionIpn(
      Payment_Model_Order $order,
      Engine_Payment_Ipn $ipn)
  {
    throw new Engine_Payment_Plugin_Exception('Not implemented');
  }
  
  /**
   * Cancel a subscription (i.e. disable the recurring payment profile)
   *
   * @params $transactionId
   * @return Engine_Payment_Plugin_Abstract
   */
  public function cancelSubscription($transactionId)
  {
    return $this;
  }

  /**
   * Generate href to a page detailing the order
   *
   * @param string $transactionId
   * @return string
   */
  public function getOrderDetailLink($orderId)
  {
    return false;
  }

  /**
   * Generate href to a page detailing the transaction
   *
   * @param string $transactionId
   * @return string
   */
  public function getTransactionDetailLink($transactionId)
  {
    return false;
  }

  /**
   * Get raw data about an order or recurring payment profile
   *
   * @param string $orderId
   * @return array
   */
  public function getOrderDetails($orderId)
  {
    return false;
  }

  /**
   * Get raw data about a transaction
   *
   * @param $transactionId
   * @return array
   */
  public function getTransactionDetails($transactionId)
  {
    return false;
  }



  // IPN

  /**
   * Process an IPN
   *
   * @param Engine_Payment_Ipn $ipn
   * @return Engine_Payment_Plugin_Abstract
   */
  public function onIpn(Engine_Payment_Ipn $ipn)
  {
    throw new Engine_Payment_Plugin_Exception('Not implemented');
  }



  // Forms

  /**
   * Get the admin form for editing the gateway info
   *
   * @return Engine_Form
   */
  public function getAdminGatewayForm()
  {
    return new Payment_Form_Admin_Gateway_Testing();
  }
  
  public function processAdminGatewayForm(array $values)
  {
    return $values;
  }
}
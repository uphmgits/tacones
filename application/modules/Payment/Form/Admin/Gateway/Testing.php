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
class Payment_Form_Admin_Gateway_Testing extends Payment_Form_Admin_Gateway_Abstract
{
  public function init()
  {
    parent::init();
    
    $this->setTitle('Payment Gateway: Testing');
    $this->setDescription('PAYMENT_FORM_ADMIN_GATEWAY_TESTING_DESCRIPTION');

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
  }
}
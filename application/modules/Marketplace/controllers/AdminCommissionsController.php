<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */
class Marketplace_AdminCommissionsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_commissions');

    $this->view->form = $form = new Marketplace_Form_Admin_Commissions();

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();

      $commissionTable = Engine_Api::_()->getDbtable('commissions', 'marketplace');
      foreach ($values as $key => $value) {
        if( preg_match('/^marketplace_commission_(\d)$/i', $key, $matches) ) {
          $level_id = $matches[1];
          $check = $commissionTable->select()->where('level_id = '.$level_id)->query()->fetch();
          if( !empty($check) ) {
            $commissionTable->update( array('commission' => $value), 'level_id = '.$level_id );
          } else {
            $commissionTable->insert( array('level_id' => $level_id, 'commission' => $value));      
          }
        }
        if( preg_match('/^marketplace_commission_vip_(\d)$/i', $key, $matches) ) {
          $level_id = $matches[1];
          $check = $commissionTable->select()->where('level_id = '.$level_id)->query()->fetch();
          if( !empty($check) ) {
            $commissionTable->update( array('commission_vip' => $value), 'level_id = '.$level_id );
          } else {
            $commissionTable->insert( array('level_id' => $level_id, 'commission_vip' => $value));      
          }
        }
      }

    }

  }
}

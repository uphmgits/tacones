<?php
/**
 * SocialEngineMarket
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */

class Marketplace_Form_Admin_Commissions extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Commission Settings')
      ->setDescription('Set commission(%) for user-levels.');
    

    $commissionTable = Engine_Api::_()->getDbtable('commissions', 'marketplace');
    $commissionTableName = $commissionTable->info('name');

    $levelsTable = Engine_Api::_()->getDbtable('levels', 'authorization');
    $levelsTableName = $levelsTable->info('name');

    $commissions = $levelsTable->getAdapter()
                               ->select()
                               ->from($levelsTableName, '*')
                               ->joinleft($commissionTableName, "{$commissionTableName}.level_id = {$levelsTableName}.level_id", "commission")
                               ->query()
                               ->fetchAll()
    ;  
    
    foreach($commissions as $commission) {
      $this->addElement('Text', 'marketplace_commission_'.$commission['level_id'], array(
		    'label' => $commission['title'],
		    'value' => $commission['commission'] ? $commission['commission'] : 0,
		    'required' => true,
        'validators' => array(
          'float',
        )
		  ));
    }

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Commissions',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}

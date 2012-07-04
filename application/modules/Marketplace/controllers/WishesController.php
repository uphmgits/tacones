<?php

class Marketplace_WishesController extends Core_Controller_Action_Standard {

    public function indexAction() 
    { 
      $page = $this->_getParam('page', 1);

      $viewer = Engine_Api::_()->user()->getViewer();
      if( !$viewer->getIdentity() ) return $this->_helper->redirector->gotoRoute(array(), 'marketplace_browse');

      $wishesTable = Engine_Api::_()->getDbtable('wishes', 'marketplace');
      $select = $wishesTable->select()->where('user_id = ?', $viewer->getIdentity());

      $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage(20);
      $paginator->setCurrentPageNumber( $page );
      $this->view->paginator = $paginator;
    }

    public function addAction() 
    {
        $item = (int)$this->_getParam('item', 0);
        if( !$item ) die();

        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) die();

        $marketplacesTable = Engine_Api::_()->getDbtable('marketplaces', 'marketplace');
        $wishesTable = Engine_Api::_()->getDbtable('wishes', 'marketplace');  

        $mp = $marketplacesTable->select()->where('marketplace_id = ?', $item)->query()->fetchAll();
        if( empty($mp) ) die();

        $wish = $wishesTable->select()->where("user_id = {$viewer->getIdentity()} AND marketplace_id = {$item}")->query()->fetchAll();
        if( !empty($wish) ) die();

        $wishesTable->insert(array('user_id' => $viewer->getIdentity(), 'marketplace_id' => $item));
        die();
    } 

    public function removeAction() 
    { 
        $item = (int)$this->_getParam('item', 0);
        if( !$item ) die();

        $viewer = Engine_Api::_()->user()->getViewer();
        if( !$viewer->getIdentity() ) die();

        $wishesTable = Engine_Api::_()->getDbtable('wishes', 'marketplace');  

        $wishesTable->delete("user_id = {$viewer->getIdentity()} AND marketplace_id = {$item}");
        die();
    }

}

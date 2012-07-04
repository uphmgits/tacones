<?php
/**
 * SocialEngineMarket
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */

class Marketplace_AdminOrdermanagementController extends Core_Controller_Action_Admin
{
  public function indexAction() 
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_ordermanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_ordermanagement', array(), 'marketplace_admin_main_ordermanagement_settings');

    $this->view->form = $form = new Marketplace_Form_Admin_Inspection();

    if( $this->getRequest()->isPost() and $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      Engine_Api::_()->getApi('settings', 'core')->setSetting('marketplace.inspectionenable', (int)$values['marketplace_inspection_enable']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('marketplace.inspection', $values['marketplace_inspection']);
    }

  }

  public function inspectionbrowseAction()
  {
    // navigation menus
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_ordermanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_ordermanagement', array(), 'marketplace_admin_main_ordermanagement_inspectionbrowse');


    $page = $this->_getParam('page', 1);
    $status_filter = $this->_getParam('status_filter', 'all');
    $period_filter = $this->_getParam('period_filter', 'all');
  
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'marketplace');
    $ordersTableName = $ordersTable->info('name');

    $this->view->pdfMainPath = $pdfMainPath = APPLICATION_PATH . "/public/invoices/invoice_";
    $this->view->pdfMainUrl = $pdfMainUrl = $this->view->baseUrl() . "/public/invoices/invoice_";

    // post request
    if( $this->getRequest()->isPost() ) {

      // classes for 
      $mpdfClass = APPLICATION_PATH . '/externals/mpdf/mpdf.php';
      include $mpdfClass;

      $values = $this->getRequest()->getPost();

      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $file_content = '';

      foreach ($values as $key=>$value) :

        if( $key == 'modify_' . $value ) :

          $order = $ordersTable->select()->where('order_id = ?', (int) $value )->query()->fetch();

          if( !empty($order) and $order['to_file_transfer'] == 0 ) {

              $marketplace = Engine_Api::_()->getItem('marketplace', $order['marketplace_id']);
              $owner = Engine_Api::_()->getItem('user', $order['owner_id']);

              if( !$owner or !$marketplace) continue;
                
              if( $values['submit_button'] == 'add_to_sold_file' and $order['status'] == 'sold') 
              {
                  // ADD TO SOLD FILE
                  $commission = $order['price'] * Engine_Api::_()->marketplace()->getCommissionFee($owner) / 100;
                  $summ = $order['count'] * ( $order['price'] - $commission + $order['shipping'] );

                  $file_content .= $marketplace->business_email . " " . number_format($summ, 2) . " " . "USD" . " " . $order['order_id'] . " ";
                  $file_content .= "buyerEmail:" . $order['contact_email'] . 
                                   "|buyerID:" . $order['user_id'] .
                                   "|sellerID:" . $owner->getIdentity() . 
                                   "|sellerName:" . $owner->getTitle() . 
                                   "|sh:" . $order['inspection'] . 
                                   "|commission:" . $commission . 
                                   "|count:" . $order['count']
                  ; 
                  $file_content .= "\n";
                  $ordersTable->update( array('to_file_transfer' => 1), 'order_id = '.$value);
              }

              if( $values['submit_button'] == 'add_to_return_file' and $order['status'] == 'return' and $order['contact_email'] ) 
              {
                  // ADD TO RETURN FILE
                  $return_summ = $order['count'] * ( $order['price'] - $order['shipping'] ); // Buyer pay 2 shippings when he's not happy
                  if( $return_summ < 0 ) continue;
                  $file_content .= $order['contact_email'] . " " . number_format($return_summ, 2) . " " . "USD" . " " . $order['order_id'] . " ";
                  $file_content .= "buyerEmail:" . $order['contact_email'] . 
                                   "|buyerID:" . $order['user_id'] .
                                   "|sellerID:" . $owner->getIdentity() . 
                                   "|sellerName:" . $owner->getTitle() . 
                                   "|inspection:" . $order['inspection'] .
                                   "|shipping:" . $order['shipping'] . 
                                   "|count:" . $order['count']
                  ; 
                  $file_content .= "\n";

                  if( $order['shipping'] > 0 ) {
                      $file_content .= $marketplace->business_email . " " . number_format($order['shipping'], 2) . " " . "USD" . " " . $order['order_id'] . " ";
                      $file_content .= "buyerEmail:" . $order['contact_email'] . 
                                       "_buyerID:" . $order['user_id'] .
                                       "_sellerID:" . $owner->getIdentity() . 
                                       "_sellerName:" . $owner->getTitle() . 
                                       "_inspection:" . $order['inspection'] . 
                                       "_shipping:" . $order['shipping'] . 
                                       "_count:" . $order['count']
                      ; 
                      $file_content .= "\n";
                  }
                  $ordersTable->update( array('to_file_transfer' => 2), 'order_id = '.$value);
              }

              if( $values['submit_button'] == 'punish' ) {
                  // PUNISH
                  if( $owner and $owner->getIdentity() ) {
                      $usersTable = Engine_Api::_()->getDbtable('users', 'user');
                      $marketplacesTable = Engine_Api::_()->getDbtable('marketplaces', 'marketplace');
                      $usersTable->update( array('enabled' => '0'), "user_id = ".$owner->getIdentity() );
                      $ordersTable->update( array('status' => 'punished'), 'owner_id = '.$owner->getIdentity() );
                      $marketplacesTable->update( array('closed' => 1), 'owner_id = '.$owner->getIdentity() );
                  }
              }
          }

        endif;

        // CHANGE STATUS
        if( $values['submit_button'] == 'change_status' and preg_match("/^smod_(\d+)/i", $key, $matches ) ) :

            $order = $ordersTable->select()
                                 ->where("order_id = {$matches[1]}")
                                 ->query()
                                 ->fetch()
            ;
            $status = $order['status'];

            // the correct new status
            if( ( ($status == 'wait' or $status == 'cancelrequest') and $value == 'inprogress' ) or
                ( $value == 'canceled' ) or
                ( $status == 'approved' and $value == 'admin_sent' ) or
                ( $status == 'inprogress' and ($value == 'approved' or $value == 'failed') ) or
                ( $status == 'admin_sent' and ($value == 'sold' or $value == 'return') )
              ) {

              $ordersTable->update( array('status' => $value), 'order_id = '.$matches[1]);

              // ready for inspection
              if( $status == 'wait' and $value == 'inprogress' ) {
                $marketplace = Engine_Api::_()->getItem('marketplace', $order['marketplace_id']);
                $owner = Engine_Api::_()->getItem('user', $order['owner_id']);
                $buyer = Engine_Api::_()->getItem('user', $order['user_id']);
                if( $owner and $buyer and $marketplace ) {
                    $notifyApi->addNotification($owner, $buyer, $marketplace, 'ready_for_inspection');
                    $notifyApi->addNotification($buyer, $owner, $marketplace, 'ready_for_inspection');
                }
              }

              // product is approved
              if( $status == 'inprogress' and $value == 'approved' ) {
                $marketplace = Engine_Api::_()->getItem('marketplace', $order['marketplace_id']);
                $owner = Engine_Api::_()->getItem('user', $order['owner_id']);
                $buyer = Engine_Api::_()->getItem('user', $order['user_id']);
                if( $owner and $buyer and $marketplace ) {
                    $notifyApi->addNotification($buyer, $owner, $marketplace, 'inspection_approving');
                }
              }

              // product is failed
              if( $status == 'inprogress' and $value == 'failed' ) {
                $marketplace = Engine_Api::_()->getItem('marketplace', $order['marketplace_id']);
                $owner = Engine_Api::_()->getItem('user', $order['owner_id']);
                $buyer = Engine_Api::_()->getItem('user', $order['user_id']);
                if( $owner and $buyer and $marketplace ) {
                    $notifyApi->addNotification($owner, $buyer, $marketplace, 'item_not_legitimate_to_owner');
                    $notifyApi->addNotification($buyer, $owner, $marketplace, 'item_not_legitimate_to_buyer');
                }
              }

              // product canceled
              if( $status == 'cancelrequest' and $value == 'canceled' ) {
                $marketplace = Engine_Api::_()->getItem('marketplace', $order['marketplace_id']);
                $owner = Engine_Api::_()->getItem('user', $order['owner_id']);
                $buyer = Engine_Api::_()->getItem('user', $order['user_id']);
                if( $owner and $buyer and $marketplace ) {
                    $notifyApi->addNotification($owner, $buyer, $marketplace, 'order_canceled_to_owner');
                    $notifyApi->addNotification($buyer, $owner, $marketplace, 'order_canceled_to_buyer');
                }
              }

            }
        endif;

        if( $key == 'createInvoice' ) {

            $pdfPath = $pdfMainPath . $value . ".pdf";

            $html_body = "<html>";
            $html_body .= "<head>";
            $html_body .= "</head>";
            $html_body .= "<body>";
            $html_body .= "<h1>ORDER #{$value}</h1>";
            $html_body .= "</body>";
            $html_body .= "</html>";

            $mpdf = new mPDF('c','A4','','',10, 10, 7, 7, 10, 10); 
            $mpdf->SetDisplayMode('fullpage');
        	  $mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
            $mpdf->WriteHTML($html_body);
            $mpdf->Output( $pdfPath );
        }

      endforeach; 

      if( $file_content ) {
          if( $values['submit_button'] == 'add_to_sold_file' ) {
              $path = $this->view->baseUrl() . '/public/masspay/masspay_' . date('Y-m-d_H-i-s') . '.txt';
          } else {
              $path = $this->view->baseUrl() . '/public/masspay_return/masspay_return_' . date('Y-m-d_H-i-s') . '.txt';
          }
          $file_name = $_SERVER['DOCUMENT_ROOT'] . $path;
          $this->view->file_name = 'http://' . $_SERVER['HTTP_HOST'] . $path;

          ob_start();
          print_r($file_content);
          $c = ob_get_clean();
          file_put_contents($file_name, $c);
          chmod($file_name, 0777);
      }
    }

    $select = $ordersTable->select();
    //$select = $ordersTable->select()->where('inspection > 0');

    $formFilter = new Marketplace_Form_Filter();//User_Form_Admin_Manage_Filter();
    $formFilter->addElement('hidden', 'status_filter', array('value' => $status_filter ) );
    $this->view->formFilter = $formFilter;

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'order_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $this->view->status_filter = $status_filter;
    switch( $status_filter ) {
      case 'wait'       :
      case 'inprogress' :
      //case 'sold'       :
      //case 'return'     :
      case 'approved'   :
      case 'done_failed':
      case 'admin_sent' :
      case 'punished'   : 
      //case 'canceled'   :
      case 'cancelrequest': $select->where("status = '{$status_filter}'"); break;
      default: $select->where("status NOT LIKE '%done%' AND status <> 'sold' AND status <> 'return' AND status <> 'canceled'");
    }

    $this->view->period_filter = $period_filter;
    switch( $period_filter ) {
      case 'day'     : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW()) AND DAY(date) = DAY(NOW())"); break;
      case 'week'    : $select->where("YEAR(date) = YEAR(NOW()) AND WEEK(date, 1) = WEEK(NOW(), 1)"); break;
      case 'mount'   : $select->where("YEAR(date) = YEAR(NOW()) AND MONTH(date) = MONTH(NOW())"); break;
      case 'quarter' : $select->where("YEAR(date) = YEAR(NOW()) AND QUARTER(date) = QUARTER(NOW())"); break;
      case 'year'    : $select->where("YEAR(date) = YEAR(NOW())"); break;
    }

    $select->order(( !empty($values['order']) ? $values['order'] : 'order_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'ASC' ));

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

  }

  public function viewShippingInfoAction() {
    $sInfoId = (int)$this->_getParam('siid', 0);
    if( $sInfoId ) {
        $shippinginfoTable = Engine_Api::_()->getDbtable('shippinginfo', 'marketplace');
        $this->view->info = $shippinginfoTable->select()->where( "shippinginfo_id = {$sInfoId}" )->query()->fetch();
        if( $this->view->info ) return;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
    ));
  }

  public function orderstreeAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_ordermanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_ordermanagement', array(), 'marketplace_admin_main_ordermanagement_orderstree');
  }

  public function ajaxOrderstreeItemAction() {

    // year_quarter_month_week_day
    $period = $this->_getParam('id', '');
    $node = array();

    $ordersTable = Engine_Api::_()->getDbtable('orders', 'marketplace');
    $ordersTableName = $ordersTable->info('name');

    if( empty($period) ) {
        $name = "2012";
        $node[] = null;  
        $node[] = '{"id" : "' . $name . '", "title": "' . $name . '", "isFolder": 1}';
    } else {
        $periodsParts = explode('_', $period);
        $year = (int)$periodsParts[0];
        // get quarter list
        if( count($periodsParts) == 1 ) {
            $name = $year . "_";

            $res = $ordersTable->select()->where("YEAR(date) = {$year}")->order('order_id DESC')->query()->fetchAll();
            $node[] = $this->_prepareData($res);

            $node[] = '{"id" : "' . $name . '1", "title": "1 quarter", "isFolder": 1}';
            $node[] = '{"id" : "' . $name . '2", "title": "2 quarter", "isFolder": 1}';
            $node[] = '{"id" : "' . $name . '3", "title": "3 quarter", "isFolder": 1}';
            $node[] = '{"id" : "' . $name . '4", "title": "4 quarter", "isFolder": 1}';
        }
        // get months list
        if( count($periodsParts) == 2 ) {
            $quarter = $periodsParts[1];
            $name = $year . "_" . $quarter . "_";
            $res = $ordersTable->select()
                               ->where("YEAR(date) = {$year} AND QUARTER(date) = $quarter")
                               ->order('order_id DESC')
                               ->query()
                               ->fetchAll();
            $node[] = $this->_prepareData($res);

            $finish = 3 * $quarter;
            for($i = $finish - 2; $i <= $finish; $i++) {
                $node[] = '{"id" : "' . $name . $i . '", "title": "' . date("F",mktime(0,0,0,$i,1)) . '", "isFolder": 1}';
            }
        }
        if( count($periodsParts) == 3 ) {
            $quarter = (int)$periodsParts[1];
            $month = (int)$periodsParts[2];
            $name = $year . "_" . $quarter . "_" . $month . "_";

            $res = $ordersTable->select()
                               ->where("YEAR(date) = {$year} AND QUARTER(date) = $quarter AND MONTH(date) = $month")
                               ->order('order_id DESC')
                               ->query()
                               ->fetchAll();
            $node[] = $this->_prepareData($res);

            $dayPerMonth = date('t', mktime(0,0,0,$month,1));
            $start  = $firstWeek = (int)date('W',mktime(0,0,0,$month,1));
            $finish = $lastWeek = (int)date('W',mktime(0,0,0,$month,$dayPerMonth));

            if( $month == 1  and $firstWeek > 50) $start  = 0;
            if( $month == 12 and $lastWeek  < 5 ) $finish = 52;

            for($i = $start; $i <= $finish ; $i++) {
                if( $i == $start ) {
                  $node[] = '{"id" : "' . $name . $firstWeek . '", "title": "week #1", "isFolder": 1}';
                  continue;
                }
                if( $i == $finish ) {
                  $node[] = '{"id" : "' . $name . $lastWeek . '", "title": "week #' . ($finish - $start + 1) . '", "isFolder": 1}';
                  continue;
                }
                $node[] = '{"id" : "' . $name . $i . '", "title": "week #' . ($i - $start + 1) . '", "isFolder": 1}';
            }
        }
        if( count($periodsParts) == 4 ) {
            $quarter = (int)$periodsParts[1];
            $month = (int)$periodsParts[2];
            $week = (int)$periodsParts[3];
            $name = $year . "_" . $quarter . "_" . $month . "_" . $week . "_";

            $res = $ordersTable->select()
                               ->where("YEAR(date) = {$year} AND QUARTER(date) = $quarter AND MONTH(date) = $month AND WEEK(date) = $week")
                               ->order('order_id DESC')
                               ->query()
                               ->fetchAll();
            $node[] = $this->_prepareData($res);

            $dayPerMonth = date('t', mktime(0,0,0,$month,1));
            for($i = 1; $i <= $dayPerMonth ; $i++) {
                $dayWeek = (int)date('W',mktime(0,0,0,$month,$i));
                if( $week == $dayWeek) {
                  $node[] = '{"id" : "' . $name . $i . '", "title": "' . $i . ' (' . date('l',mktime(0,0,0,$month,$i)) . ')", "isFolder": 1}';
                }
            }
        }
        if( count($periodsParts) == 5 ) {
            $quarter = (int)$periodsParts[1];
            $month = (int)$periodsParts[2];
            $week = (int)$periodsParts[3];
            $day = (int)$periodsParts[4];
            $name = $year . "_" . $quarter . "_" . $month . "_" . $week . "_" . $day . "_";

            $res = $ordersTable->select()
                               ->where("YEAR(date) = {$year} AND 
                                        QUARTER(date) = {$quarter} AND 
                                        MONTH(date) = {$month} AND 
                                        WEEK(date) = {$week} AND
                                        DAY(date) = {$day}")
                               ->order('order_id DESC')
                               ->query()
                               ->fetchAll();
            $node[] = $this->_prepareData($res);
            $node[] = '{"id" : "' . $name . '0", "title": "day info", "isFolder": 0}';
        }
    }
    //usleep(500000);
    echo '['.implode(',',$node).']';
    die();
  }

  private function _prepareData($orders) {
      $res = array();
      foreach($orders as $order) {
          $marketplace = Engine_Api::_()->getItem('marketplace', $order['marketplace_id']);
          $owner = Engine_Api::_()->getItem('user', $order['owner_id']);
          $buyer = Engine_Api::_()->getItem('user', $order['user_id']);
  
          $order['owner_id'] = $owner->getTitle();
          $order['user_id'] = $buyer->getTitle();
          $order['marketplace_id'] = $marketplace ? $marketplace->getTitle() : "Deleted";
          switch( $order['status'] ) {
            case 'wait'       : $order['status'] = "Bougnt"; break;
            case 'inprogress' : $order['status'] = "Upheels received"; break;
            case 'sold'       : $order['status'] = "Complete"; break;
            case 'done_sold'  : $order['status'] = "Complete (done)"; break;
            case 'done_return_s' :
            case 'done_return_b' :
            case 'return'     : $order['status'] = "Return"; break;
            case 'done_return': $order['status'] = "Return (done)"; break;

            case 'approved'   : $order['status'] = "Passed"; break;
            case 'failed'     : $order['status'] = "Failed"; break;
            case 'done_failed': $order['status'] = "Failed (done)"; break;
            case 'admin_sent' : $order['status'] = "Upheels sent"; break;
            case 'punished'   : $order['status'] = "Punished"; break;
            case 'canceled'   : $order['status'] = "Canceled"; break;
            case 'done_canceled' : $order['status'] = "Canceled (done)"; break;
            case 'cancelrequest': $order['status'] = "Cancel Request"; break;
          }
          $res[] = $order;
      }
      return json_encode($res);
  }


  /*public function uninspectionbrowseAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_ordermanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_ordermanagement', array(), 'marketplace_admin_main_ordermanagement_uninspectionbrowse');

    $page = $this->_getParam('page', 1);
    $status_filter = $this->_getParam('status_filter', 0);
    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();

      $status_filter = isset($values['status_filter']) ? $values['status_filter'] : 0;

      $ordersTable = Engine_Api::_()->getDbtable('orders', 'marketplace');
      $file_content = '';

      foreach ($values as $key=>$value) :

        if( $key == 'modify_' . $value ) :

          $order = $ordersTable->select()->where('order_id = ?', (int) $value )->query()->fetch();

          if( !empty($order) and $order['to_file_transfer'] == 0 ) {

              $owner = Engine_Api::_()->getItem('user', $order['owner_id']);
              $marketplace = Engine_Api::_()->getItem('marketplace', $order['marketplace_id']);
              
              if( $values['submit_button'] == 'add_to_sold_file' ) {
                  // ADD TO SOLD FILE
                  if( $owner and $owner->getIdentity() and $marketplace and $marketplace->getIdentity() ) {

                    $commission = $order['price'] * Engine_Api::_()->marketplace()->getCommissionFee($owner) / 100;
                    $summ = $order['count'] * ( $order['price'] - $commission + $order['shipping'] );

                    $file_content .= $marketplace->business_email . " " . number_format($summ, 2) . " " . "USD" . " " . $order['order_id'] . " ";
                    $file_content .= "buyerEmail:" . $order['contact_email'] . 
                                     "|buyerID:" . $order['user_id'] .
                                     "|sellerID:" . $owner->getIdentity() . 
                                     "|sellerName:" . $owner->getTitle() . 
                                     "|inspection:0" . 
                                     "|shipping:" . $order['shipping'] .
                                     "|commission:" . $commission .  
                                     "|count:" . $order['count']
                    ; 
                    $file_content .= "\n";
                    $ordersTable->update( array('to_file_transfer' => 1), 'order_id = '.$value);
                  }

              }
              if( $values['submit_button'] == 'add_to_return_file' ) {

                  // ADD TO RETURN FILE
                  if( $owner and $owner->getIdentity() and $marketplace and $marketplace->getIdentity() and $order['contact_email'] ) {
                    $return_summ = $order['count'] * ( $order['price'] - $order['shipping'] );
                    if( $return_summ < 0 ) continue;

                    $file_content .= $order['contact_email'] . " " . number_format($return_summ, 2) . " " . "USD" . " " . $order['order_id'] . " ";
                    $file_content .= "buyerEmail:" . $order['contact_email'] . 
                                     "|buyerID:" . $order['user_id'] .
                                     "|sellerID:" . $owner->getIdentity() . 
                                     "|sellerName:" . $owner->getTitle() . 
                                     "|inspection:0" . 
                                     "|count:" . $order['count']
                    ; 
                    $file_content .= "\n";

                    if( $order['shipping'] > 0 ) {
                        $file_content .= $marketplace->business_email . " " . number_format($order['shipping'], 2) . " " . "USD" . " " . $order['order_id'] . " ";
                        $file_content .= "buyerEmail:" . $order['contact_email'] . 
                                         "|buyerID:" . $order['user_id'] .
                                         "|sellerID:" . $owner->getIdentity() . 
                                         "|sellerName:" . $owner->getTitle() . 
                                         "|inspection:0" . 
                                         "|shipping:" . $order['shipping'] . 
                                         "|count:" . $order['count']
                        ; 
                        $file_content .= "\n";
                    }
                    $ordersTable->update( array('to_file_transfer' => 2), 'order_id = '.$value);
                  }
              }
          }
        endif;

        if( $values['submit_button'] == 'change_status' and preg_match("/^status_modify_(\d+)/i", $key, $matches ) ) {
            if($value == 0 or $value == 1) $status = $value;
            else $status = 2;
            $ordersTable->update( array('status' => $status), 'order_id = '.$matches[1]);
        }


      endforeach;

      if( $file_content ) {
          if( $values['submit_button'] == 'add_to_sold_file' ) {
              $path = $this->view->baseUrl() . '/public/masspay/masspay_' . date('Y-m-d_H-i-s') . '.txt';
          } else {
              $path = $this->view->baseUrl() . '/public/masspay_return/masspay_return_' . date('Y-m-d_H-i-s') . '.txt';
          }
          $file_name = $_SERVER['DOCUMENT_ROOT'] . $path;
          $this->view->file_name = 'http://' . $_SERVER['HTTP_HOST'] . $path;

          ob_start();
          print_r($file_content);
          $c = ob_get_clean();
          file_put_contents($file_name, $c);
          chmod($file_name, 0777);
      }
    }


    $table = $this->_helper->api()->getDbtable('orders', 'marketplace');
    $select = $table->select()->where('inspection = 0');

    $formFilter = new Marketplace_Form_Filter();//User_Form_Admin_Manage_Filter();
    $formFilter->addElement('hidden', 'status_filter', array('value' => $status_filter ) );
    $this->view->formFilter = $formFilter;

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'order_id',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $this->view->status_filter = $status_filter;
    switch( $status_filter ) {
      case 1 : $select->where('status = 0'); break;
      case 2 : $select->where('status = 1 and to_file_transfer = 0'); break;
      case 3 : $select->where('status = 2'); break;
    }

    $select->order(( !empty($values['order']) ? $values['order'] : 'order_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'ASC' ));

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(20);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );

  }*/


}

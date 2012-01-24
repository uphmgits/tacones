<?php

class Slider_Model_DbTable_Slides extends Engine_Db_Table
{
  protected $_rowClass = 'Slider_Model_Slide';

  public function fetchAll($where = null, $order = 'order ASC', $count = null, $offset = null) {
      return parent::fetchAll($where, $order, $count, $offset);
  }
  public function save_order($slides_order) {
      if (is_array($slides_order) and count($slides_order) > 0) {
          $i = 1;
          foreach ($slides_order as $slide_id) {
              preg_match ("/slide_(\d+)/", $slide_id, $id);
              $this->update(array('order' => $i), array('slide_id = ?' => (int)$id[1]));
              $i++;
          }
      }
      else {
          throw new Zend_Db_Table_Exception("Incorrect data format. Please try again.");
      }
      return true;
  }
}
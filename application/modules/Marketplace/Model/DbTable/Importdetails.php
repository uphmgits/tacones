<?php
class Marketplace_Model_DbTable_Importdetails extends Engine_Db_Table
{
  protected $_rowClass = 'Marketplace_Model_Importdetails';
  protected $_name = 'marketplace_importdetails';
  
  public function writeLogMessage($values) {
	$table = Engine_Api::_()->getDbtable('importdetails', 'marketplace');
	$dtl = $table->createRow();
    $dtl->setFromArray($values);
    return $dtl->save();
  }
}
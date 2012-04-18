<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Jobs.php 8975 2011-06-08 03:18:27Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_Jobs extends Engine_Db_Table
{
  //protected $_serializedColumns = array('data');

  public function addJob($type, $params)
  {
    // Get job type
    $jobtype = Engine_Api::_()->getDbtable('jobTypes', 'core')->select()
      ->where('enabled = ?', 1)
      ->where('type = ?', $type)
      ->limit(1)
      ->query()
      ->fetch();

    // Missing job type
    if( !$jobtype ) {
      return false;
    }

    // Separate params from allowed columns
    $allowedColumns = array('priority');
    $data = array_intersect_key($params, array_flip($allowedColumns));
    $params = array_diff_key($params, array_flip($allowedColumns));

    // Add other data
    $data['jobtype_id'] = $jobtype['jobtype_id'];
    $data['creation_date'] = new Zend_Db_Expr('NOW()');
    $data['data'] = Zend_Json::encode($params);

    $job = $this->createRow();
    $job->setFromArray($data);
    $job->save();

    return $job;
  }

  public function getActiveJobs($params)
  {
    $select = $this->select()
        ->where('state IN(?)', array('active', 'sleeping', 'pending'))
        ;

    if( !empty($params['jobtype_id']) ) {
      if( is_array($params['jobtype_id']) ) {
        $select->where('jobtype_id IN(?)', $params['jobtype_id']);
      } else {
        $select->where('jobtype_id = ?', $params['jobtype_id']);
      }
    }

    if( !empty($params['jobtype']) ) {
      $select
        ->join('engine4_core_jobtypes', 'engine4_core_jobs.jobtype_id=engine4_core_jobtypes.jobtype_id', null)
        ;
      if( is_array($params['jobtype']) ) {
        $select->where('engine4_core_jobtypes.type IN(?)', $params['jobtype']);
      } else {
        $select->where('engine4_core_jobtypes.type = ?', $params['jobtype']);
      }
    }

    return $this->fetchAll($select);
  }
}
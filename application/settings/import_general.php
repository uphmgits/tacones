<?php defined('_ENGINE') or die('Access Denied'); return array (
  	'source'	  => array('UPHEELS_MANUAL' => 1,
						   'UPHEELS_FILEUPLOAD' => 2,
						   'EBAY_IMPORT' => 3),
	'jobStates'	  => array('IMPORT_FETCHING_INPROCESS' => 1,
						   'IMPORT_FETCHING_COMPLETED' => 2,
						   'IMPORT_IMPORTING_INPROCESS' => 3,
						   'IMPORT_IMPORTING_COMPLETED' => 4));
 ?>
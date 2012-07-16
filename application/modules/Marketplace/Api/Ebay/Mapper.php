<?php
define('IMPORT_FETCHING_INPROCESS', 1);
define('IMPORT_FETCHING_COMPLETED', 2);
define('IMPORT_IMPORTING_INPROCESS', 3);
define('IMPORT_IMPORTING_COMPLETED', 4);

class Marketplace_Api_Ebay_Mapper {
	
	private $_meta;
	private $_db;
	private $_categoryspecs;
	private $_ebaydataretrieved;
	private $_upheelsSpecs;
	private $_sellerid;
	private $_jobstatus;
	private $_listingsRetrieved;
	private $_listingsMapped;
	private $_impOptions;
	private $_listingsPostedFrom;
	private $_listingsPostedThru;
	private $_upheelsUserID;
	private $_importSource;
	private $_importSellerID;
	private $_alreadyin;
	private $_schema;
	
	public function __construct() {
    	
		$this->setListingsRetrieved(0);
		$this->setListingsMapped(0);
		
		$file = APPLICATION_PATH . '/application/settings/database.php';
	    $options = include $file;
	    $this->_db = Zend_Db::factory($options['adapter'], $options['params']);
	    $this->_schema = $options['params']['dbname'];
	}
	
	private function setFieldsMeta() {
		$fieldsmeta = Engine_Api::_()->getDbtable('fields_meta', 'marketplace');
    	$metadata = $fieldsmeta->getAllFieldMeta();
    	foreach($metadata as $meta) {
    		$this->_meta[$meta->category_id][$meta->label] = $meta->field_id;
    	}
	}
	
	private function setFullCategorySpecs() {
		$this->_categoryspecs = array();
		$select = $this->_db->select()
			->from(array('m' => $this->_schema.'.engine4_marketplace_fields_meta'), array('m.category_id', 'm.label as spec', 'm.field_id'))
			->join(array('o' =>$this->_schema.'.engine4_marketplace_fields_options'), 'm.field_id=o.field_id', 
					array('o.option_id as specvalue_id', 'o.label as spec_value'))
			->order('m.category_id asc');

		$rows = $this->_db->fetchAll($select);
		
		foreach($rows as $row) {
			$this->_categoryspecs[$row['category_id']][$row['spec']][] = 
			  array('field_id' =>$row['field_id'], 'option_id' => $row['specvalue_id'], 'spec_value' => $row['spec_value']);
		}
	}
	
	public function createImportjobRecord($jobstate) {
		$currtime = date('Y-m-d H:i:s');
		//print "<br>$currtime"; exit;
		$values = array('importjobs_id' => null,
						'import_source' => $this->_importSource,
						'upheels_sellerid' => $this->_upheelsUserID,
						'import_source_sellerid' => $this->_importSellerID,
						'import_source_datefrom' => $this->_listingsPostedFrom,
						'import_source_dateto' => $this->_listingsPostedThru,
						'listings_retrieved' => $this->getListingsRetrieved(),
						'listings_imported' => $this->getListingsMapped(),
						'job_status' => $jobstate,
						'job_createtime' => $currtime,
						'job_lastupdatetime' => $currtime);
		$table = Engine_Api::_()->getDbtable('importjobs', 'marketplace');
		$job = $table->createRow();
        $job->setFromArray($values);
        return $job->save();
	}
	
	public function importjobRecordFetchCompleted($jobid, $jobstate, $count) {
		$currtime = date('Y-m-d H:i:s');
		$updatedFields = array( 'job_status' => $jobstate,
								'listings_retrieved' => $count,
								'job_lastupdatetime' => $currtime);
		$this->_updateJobRecord($jobid, $updatedFields);
	}
	
	public function importjobRecordStartImport($jobid, $jobstate) {
		$currtime = date('Y-m-d H:i:s');
		$updatedFields = array( 'job_status' => $jobstate,
								'job_lastupdatetime' => $currtime);
		$this->_updateJobRecord($jobid, $updatedFields);
	}
	
	public function importjobRecordImportCompleted($jobid, $jobstate, $count) {
		$currtime = date('Y-m-d H:i:s');
		$updatedFields = array( 'job_status' => $jobstate,
								'listings_imported' => $count,
								'job_lastupdatetime' => $currtime);
		$this->_updateJobRecord($jobid, $updatedFields);
	}
	
	private function _updateJobRecord($jobid, $values) {
		$table = Engine_Api::_()->getDbtable('importjobs', 'marketplace');
		$where = $table->getAdapter()->quoteInto('importjobs_id = ?', $jobid); 
		$table->update($values, $where);
	}
	
	
	public function map() {
		$mappeddata = array();
		$this->setFullCategorySpecs();
		$this->_updateEbaySpecLabelToUpheelsLabel();
		
		// Build listingIDs that already have been imported into marketplace table for this seller
		//$this->_alreadyImported();  // moved this logic to API class
		
		foreach($this->_ebaydataretrieved as $itemID => $ebaydata) {
			$mappeddata[$itemID] = $ebaydata;
			$upheelsCat = $this->_mapCat($ebaydata['Category']);
			$mappeddata[$itemID]['uph_category_id'] = $upheelsCat['category_id_hidden'];
			$mappeddata[$itemID]['uph_category'] = $upheelsCat['category'];
			$mappeddata[$itemID]['uph_price'] = $ebaydata['BINPrice']!=0?$ebaydata['BINPrice']:$ebaydata['CurrentPrice'];
			// make sure item specifications exist in Upheels database
			foreach($ebaydata['itemSpecifics'] as $speclabel => $specvalue) {
				$mapping = $this->_mapSpecLabelValue($upheelsCat['category_id_hidden'], $speclabel, $specvalue );
				
				if(!empty($mapping)) {
					$mappeddata[$itemID]['specs'][] = $mapping;
				}
			}
			// pictures from eBay listing
			$piccount = 0;
			foreach($ebaydata['Pictures'] as $picurl) {
				$picindex = 'photo'.$piccount;
				if($piccount==0) {
					$mappeddata[$itemID]['pictures']['photo'] = $picurl;
				}
				else {
					
					$mappeddata[$itemID]['pictures'][$picindex] = $picurl;
				}
				$piccount++;
			}
		}
		//print "MAPPED SPECS: <pre>"; print_r($mappeddata);exit;
		
		return $mappeddata;
	}
	
	private function _alreadyImported() {
		
		$select = $this->_db->select()
			->from(array('m' => $this->_schema.'.engine4_marketplace_marketplaces'),array('entry_source_ref'))
			->where('m.owner_id='.$this->_upheelsUserID . ' and entry_source_ref is not null');

		$this->_alreadyin = $this->_db->fetchAll($select);
	}
	
	private function _mapSpecLabelValue($catID, $speclabel, $specvalue) {
		$mappedSpec = array();
		//print "<br>catID: $catID  specLabel: $speclabel  specValue: $specvalue";
		
		//print "<pre>"; print_r($this->_upheelsSpecs[$catID]); exit;
		foreach($this->_upheelsSpecs[$catID] as $uphlabel) {
			//print "<pre>"; print_r($this->_categoryspecs[$catID][$uphlabel]);exit;
			$labelMatched = false;
			if(stristr($uphlabel, $speclabel) !== false) {
				$labelMatched = true;
				$valueMatched = false;
				//print "<pre>"; print_r($this->_categoryspecs[$catID][$uphlabel]);exit;
				// ebay and upheels labels seem to match. check 2 spec values
				foreach($this->_categoryspecs[$catID][$uphlabel] as $specoptions) {
					//if(strcasecmp($specvalue, $specoptions['spec_value'])===0) {
					//print "<br>Comparing Ebay Spec: " . $specvalue . "  with UPH Spec:  " . $specoptions['spec_value'] . "</b>";
					if(stristr((string)$specvalue, (string)$specoptions['spec_value']) !== false ||
					   stristr((string)$specoptions['spec_value'],(string)$specvalue) !== false) {
						// spec value matches!!!
						$valueMatched = true;
						//$mappedSpec[$uphlabel] = $specoptions['spec_value'];
						$mappedSpec[$uphlabel] = array('field_id' => $specoptions['field_id'], 'option_id' => $specoptions['option_id'], 'option_value' => $specoptions['spec_value']);
						break;
					}
					// special mapping logic for "Condition"
					/*if(stristr($specoptions['spec_value'], "Condition") !== false) {
						
					}*/
				}
				if($valueMatched == false) {
					// label matched, but not the value
					//$mappedSpec = array('field_id' => $specoptions['field_id'], 'option_id' => '', 'option_value' => '');
				}
				break;
			}
		}
		if($labelMatched == false) {
			//$mappedSpec = array('field_id' => '', 'option_id' => '', 'option_value' => '');
		}
		//print "<pre>"; print_r($mappedSpec);
		return $mappedSpec;
	}
	
	/**
	 * Change ebay's spec label to Upheels's spec label, if possible, otherwise remove that spec from retrieved data
	 * @return unknown_type
	 */
	private function _updateEbaySpecLabelToUpheelsLabel() {
		// build upheels spec list for each category
		foreach($this->_categoryspecs as $catID => $specs) {
			$this->_upheelsSpecs[$catID]= array_keys($specs);
		}
	} 
	
	private function _mapCat($ebaycategoryname) {
		$upheelsCat = array();
		$catParts = explode(':', $ebaycategoryname,20);
		if(empty($catParts)) {
			$catParts = explode('>', $ebaycategoryname,20);
		}
		if(empty($catParts)) {
			// cant figure out ebay to upheels category mapping
			$upheelsCat['category_id_hidden'] = 0; // "ALL"
    		$upheelsCat['category'] = '';
		}
		else {
			// start traversing the exploded array in reverse order
			$catParts = array_reverse($catParts, true);
			foreach($catParts as $catLabel) {
				if(stripos($catLabel, 'Bag') !== false) {
					$upheelsCat['category_id_hidden'] = 5; // Bags
    				$upheelsCat['category'] = 'Bags';
    				break;
				}
				else if(stripos($catLabel, 'Shoe') !== false) {
					$upheelsCat['category_id_hidden'] = 1; // Shoes
    				$upheelsCat['category'] = 'Shoes';
    				break;
				}
				else if(stripos($catLabel, 'Cloth') !== false) {
					$upheelsCat['category_id_hidden'] = 3; // Clothes
    				$upheelsCat['category'] = 'Clothes';
    				break;
				}
				else if(stripos($catLabel, 'Accessor') !== false) {
					$upheelsCat['category_id_hidden'] = 8; // Accessories
    				$upheelsCat['category'] = 'Accessories';
    				break;
				}
			}
			if(!isset($upheelsCat['category_id_hidden'])) {
				$upheelsCat['category_id_hidden'] = 0; // "ALL"
    			$upheelsCat['category'] = '';
			}
		}
		/*
		// Strip the first part of eBay's category name: Clothing, Shoes & Accessories:
		$ebaycategoryname = stristr($ebaycategoryname, ':');
		if(stristr($ebaycategoryname, 'Bag') !== false) {
    		$upheelsCat['category_id_hidden'] = 5; // Bags
    		$upheelsCat['category'] = 'Bags';
    	}
    	else if(stristr($ebaycategoryname, 'Shoe') !== false) {
    		$upheelsCat['category_id_hidden'] = 1; // Shoes
    		$upheelsCat['category'] = 'Shoes';
    	}
    	else if(stristr($ebaycategoryname,'Cloth') !== false) {
    		$upheelsCat['category_id_hidden'] = 3; // Clothes
    		$upheelsCat['category'] = 'Clothes';
    	}
    	else if(stristr($ebaycategoryname, 'Accessor') !== false) {
    		$upheelsCat['category_id_hidden'] = 8; // Accessories
    		$upheelsCat['category'] = 'Accessories';
    	}
    	else {
    		$upheelsCat['category_id_hidden'] = 0; // "ALL"
    		$upheelsCat['category'] = '';
    	}*/
    	return $upheelsCat;
	}
	
	public function setListingDateRange($start, $end) {
		$this->_listingsPostedFrom = date('Y-m-d', strtotime($start));
		$this->_listingsPostedThru = date('Y-m-d', strtotime($end));;
	}
	
	public function setImportSource($source) {
		$this->_importSource = $source;
	}
	public function setUpheelsUser($upheelsuserid) {
		$this->_upheelsUserID = $upheelsuserid;
	}
	public function setImportSellerId($sellerid) {
		$this->_importSellerID = $sellerid;
	}
	
	public function setEbaydata($ebaydata) {
		$this->_ebaydataretrieved = $ebaydata;
	}
	public function getEbaydata() {
		return $this->_ebaydataretrieved;
	}
	public function _setSellerId($seller) {
		$this->_sellerid = $seller;
	}
	public function getSellerId() {
		return $this->_sellerid;
	}
	public function getJobStatus() {
		return $this->_jobstatus;
	}
	public function setJobStatus($status) {
		$this->_jobstatus = $status;
	}
	public function setListingsRetrieved($count) {
		$this->_listingsRetrieved = $count;
	}
	public function getListingsRetrieved() {
		return $this->_listingsRetrieved;
	}
	public function getListingsMapped() {
		return $this->_listingsMapped;
	}
	public function setListingsMapped($count) {
		$this->_listingsMapped = $count;
	}
}
?>
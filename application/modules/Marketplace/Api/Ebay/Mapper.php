<?php

class Marketplace_Api_Ebay_Mapper {
	
	/*select m.category_id, m.label, m.field_id, o.label from engine4_marketplace_fields_meta m, engine4_marketplace_fields_options o 
where m.field_id=o.field_id order by m.category_id asc;*/
	
	private $_meta;
	private $_db;
	private $_categoryspecs;
	private $_ebaydataretrieved;
	private $_upheelsSpecs;
	
	public function __construct() {
		//$this->setFieldsMeta();
		$file = APPLICATION_PATH . '/application/settings/database.php';
	    $options = include $file;
	
	    $this->_db = Zend_Db::factory($options['adapter'], $options['params']);
	    //$this->_upheelsSpecs = array('')
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
			->from(array('m' => 'zapato_social.engine4_marketplace_fields_meta'), array('m.category_id', 'm.label as spec', 'm.field_id'))
			->join(array('o' =>'zapato_social.engine4_marketplace_fields_options'), 'm.field_id=o.field_id', 
					array('o.option_id as specvalue_id', 'o.label as spec_value'))
			->order('m.category_id asc');

		$rows = $this->_db->fetchAll($select);
		
		foreach($rows as $row) {
			$this->_categoryspecs[$row['category_id']][$row['spec']][] = 
			  array('field_id' =>$row['field_id'], 'option_id' => $row['specvalue_id'], 'spec_value' => $row['spec_value']);
		}
	}
	
	public function map() {
		$mappeddata = array();
		$this->setFullCategorySpecs();
		$this->_updateEbaySpecLabelToUpheelsLabel();
		/*print "<pre>";
		print_r($this->_ebaydataretrieved);exit;*/
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
			// main picture from eBay listing
			$mappeddata[$itemID]['picture'] = $ebaydata[Pictures][0];
		}
		/*print "MAPPED SPECS: <pre>"; print_r($mappeddata);exit;
		exit;*/
		return $mappeddata;
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
    	}
    	return $upheelsCat;
	}
	
	public function setEbaydata($ebaydata) {
		$this->_ebaydataretrieved = $ebaydata;
	}
	public function getEbaydata() {
		return $this->_ebaydataretrieved;
	}
}
?>
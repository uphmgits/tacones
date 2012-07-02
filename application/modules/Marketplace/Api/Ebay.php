<?php
define ('EBAYCATEGORY_FASHION', '92732');
define ('EBAYCATEGORY_WOMEN_HANDBAGS', '63852');
define ('EBAYCATEGORY_WOMENS_ACCESSORIES', 4251);
define ('EBAYCATEGORY_WOMENS_CLOTHING', 15724);
define ('EBAYCATEGORY_WOMENS_SHOES', 3034);
class Marketplace_Api_Ebay {
	
	private $_sandbox;
	private $_eBayUri;
	private $_usertoken;
	private $_seller;
	private $_namespace;
	private $_timeout;
	private $_apiVersion;
	private $_apiDevName;
	private $_apiAppName;
	private $_apiCertName;
	private $_apiSiteID;
	private $_apiCallSellerList;
	private $_apiCallGetItem;
	private $_apiCallGetCategories;
	private $_apiCallGetCategorySpecifics;
	private $_httpclient;
	private $_reqAuth;
	private $_starttime;	// listing from
	private $_endtime;		// listing to
	private $_xpable                = '<?xml version="1.0" encoding="utf-8"?>';
	private $_getListReqRoot        = '<GetSellerListRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
	private $_getItemDetailsReqRoot = '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
	private $_getCategoriesReqRoot  = '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
	private $_getCategorySpecificsReqRoot = '<GetCategorySpecificsRequest  xmlns="urn:ebay:apis:eBLBaseComponents">';
	private $_topCats;
	private $_fullCatList;
	private $_upheelsUserId;
	private $_alreadyImported;
	
	public function __construct($sandbox=false) {
		$file = APPLICATION_PATH . '/application/settings/import_auth.php';
    	$options = include $file;
		$this->setSandbox(APPLICATION_ENV=='development'&&$sandbox===true?true:false);
		if($this->getSandbox()===true) {
			// sandbox
			$this->_eBayUri = $options['ebayDev']['uri'];
			$this->_usertoken = $options['ebayDev']['usertoken'];
			$this->_seller = $options['ebayDev']['seller'];
			$this->_namespace = $options['ebayDev']['namespace'];
			$this->_timeout = $options['ebayDev']['timeout'];
			$this->_apiVersion = $options['ebayDev']['apiVersion'];
			$this->_apiAppName = $options['ebayDev']['apiAppName'];
			$this->_apiDevName = $options['ebayDev']['apiDevName'];
			$this->_apiCertName = $options['ebayDev']['apiCertName'];
			$this->_apiSiteID = $options['ebayDev']['apiSiteID'];
			$this->_apiCallSellerList = $options['ebayDev']['apiCallSellerList'];
			$this->_apiCallGetItem = $options['ebayDev']['apiCallGetItem'];
			$this->_apiCallGetCategories = $options['ebayDev']['apiCallGetCategories'];
			$this->_apiCallGetCategorySpecifics = $options['ebayDev']['apiCallGetCategorySpecifics'];
		}
		else {
			// production
			$this->_eBayUri = $options['ebayProd']['uri'];
			$this->_usertoken = $options['ebayProd']['usertoken'];
			$this->_seller = $options['ebayProd']['seller'];
			$this->_namespace = $options['ebayProd']['namespace'];
			$this->_timeout = $options['ebayProd']['timeout'];
			$this->_apiVersion = $options['ebayProd']['apiVersion'];
			$this->_apiAppName = $options['ebayProd']['apiAppName'];
			$this->_apiDevName = $options['ebayProd']['apiDevName'];
			$this->_apiCertName = $options['ebayProd']['apiCertName'];
			$this->_apiSiteID = $options['ebayProd']['apiSiteID'];
			$this->_apiCallSellerList = $options['ebayProd']['apiCallSellerList'];
			$this->_apiCallGetItem = $options['ebayProd']['apiCallGetItem'];
			$this->_apiCallGetCategories = $options['ebayProd']['apiCallGetCategories'];
			$this->_apiCallGetCategorySpecifics = $options['ebayProd']['apiCallGetCategorySpecifics'];
		}
		$this->_httpclient = new Zend_Http_Client();
		$this->_httpclient->setConfig(array('timeout'=>$this->_timeout,'keepalive'=>true));
		$this->_httpclient->setUri($this->_eBayUri);
		$this->_setXHttpHeaders();
		
		//auth stuff
		$this->_reqAuth = '<RequesterCredentials><eBayAuthToken>'.$this->_usertoken.'</eBayAuthToken></RequesterCredentials>';
		
		// init topCat array
		$this->_topCats = array(array('ID' => EBAYCATEGORY_WOMENS_SHOES, 'name'=>"Women's Shoes"),
								array('ID'=>EBAYCATEGORY_WOMEN_HANDBAGS,'name'=>"Women's Handbags"), 
								array('ID'=>EBAYCATEGORY_WOMENS_ACCESSORIES,'name'=>"Women's Accessories"), 
								array('ID'=>EBAYCATEGORY_WOMENS_CLOTHING, 'name'=>"Women's Clothing"));
	}
	
	public function getCats() {
		$this->_fullCatList = array();
		foreach($this->_topCats as $parentCats => $parentCat) {
			//$this->_fullCatList[] = array($parentCat['name']);
			
			$subs = $this->getSubCategories($parentCat['ID']);
			
			foreach($subs as $sub => $subdata) {
				$parentname = $parentCat['name'];
				$subname = $subdata['categoryName'];
				
				$subb = $this->getSubCategories($subdata['categoryID']);
				if(sizeof($subb) > 0) {
					$this->_fullCatList[$parentname][$subname]['subb'] = $subb;
				}
				else {
					$this->_fullCatList[$parentname][$subname] = array('id'=>$subdata['categoryID']);
					$this->_fullCatList[$parentname][$subname]['specifics'] = $this->_getSpecifics($subdata['categoryID']);
					/*print "<pre>";
					print_r($this->_fullCatList);exit;*/
				}
			}
			
		}
		print "<pre>";
		print_r($this->_fullCatList);exit;
	}
	
	private function _getSpecifics($catID) {
		$specifics = array();
		
		$this->_httpclient->setHeaders(array('X-EBAY-API-CALL-NAME:'.$this->_apiCallGetCategorySpecifics));
		$getitmlistxml = $this->_xpable . $this->_getCategorySpecificsReqRoot . $this->_reqAuth . $this->_buildGetCategorySpecificsReqXml($catID); // XML POST for getting ebay categories
		$this->_httpclient->setRawData($getitmlistxml, 'text/xml');
		$res = $this->_httpclient->request(Zend_Http_Client::POST);
		$resb = $res->getBody();
		
		if($res->isSuccessful()) {
			$specsDom = new DOMDocument();                
			$specsDom->loadXML( $resb);
			if($this->_isAckSuccess($specsDom)) {
				$specsList = $specsDom->getElementsByTagName(/*$this->_namespace,*/ 'NameRecommendation');
				foreach($specsList as $s) {
					$specsTree = simplexml_import_dom($s); 
					/*print "<pre>";
					print_r($specsTree);exit;*/
					$specname = (string)$specsTree->Name;
					//print "<br> SPECNAME: $specname<br>";
					$specvals = array();
					//$values = simplexml_import_dom($specsTree->ValueRecommendation);
					foreach($specsTree->ValueRecommendation as $v) {
						$valueList = simplexml_import_dom($v);
						/*print "SPECVALS:  <pre>";
						print_r($v);*/
						$specvals[] = (string)$valueList->Value;
					}
					$specifics[$specname] = $specvals;
				}
				/*print "<pre>";
				print_r($specifics);exit;
				exit;*/
			}
			
		}
		return $specifics;
	}
	
	
	/**
	 * Returns the latest category hierarchy for the eBay site specified in the CategorySiteID
	 * @return array of categories
	 */
	public function getSubCategories($parentCat) {
		$cats = array();
		$this->_httpclient->setHeaders(array('X-EBAY-API-CALL-NAME:'.$this->_apiCallGetCategories));
		$getitmlistxml = $this->_xpable . $this->_getCategoriesReqRoot . $this->_reqAuth . $this->_buildGetCategoriesReqXml($parentCat); // XML POST for getting ebay categories
		$this->_httpclient->setRawData($getitmlistxml, 'text/xml');
		$res = $this->_httpclient->request(Zend_Http_Client::POST);
		if($res->isSuccessful()) {
			$resb = $res->getBody();
			$catDom = new DOMDocument();                
			$catDom->loadXML( $resb);
			if($this->_isAckSuccess($catDom)) {
				$catNodeList = $catDom->getElementsByTagName(/*$this->_namespace,*/ 'CategoryArray');
				$catTree = simplexml_import_dom($catNodeList->item(0)); 
				foreach($catTree as $c) {
					if($c->CategoryID != $parentCat) {
						$cats[] = array('categoryID' => (string)$c->CategoryID, 'categoryName' => (string)$c->CategoryName);
					}
				}
			}
		}
		return $cats;
		
	}
	
	/**
	 * returns item details for each item listed by the seller
	 * @return unknown_type
	 */
	public function getItemDetails($listinIDs=null) {
		// first grab all itemIDs listed by seller
		// following importing could take a while. increase max_request_timeout to 5 minutes
        set_time_limit(300);
		if($listinIDs == null) {
			$itemIds = $this->_fetchList();
		}
		else {
			$itemIds = $listinIDs;
		}
		// start building DOM for output XML
		$dom = new DOMDocument('1.0', 'utf-8');
		
		/*
		$elmSeller = $dom->createElement('seller');
		$rootNode = $dom->appendChild($elmSeller);
		
		$elmSellerID = $dom->createElement('sellerID', $this->_seller);
		$rootNode->appendChild($elmSellerID);
		
		$elmPlatform = $dom->createElement('platform', 'ebay');
		$rootNode->appendChild($elmPlatform);
		
		$elmItems = $dom->createElement('Items');
		$itemsNode = $rootNode->appendChild($elmItems);
		*/
		$arrayIDs = array();
		$i=0;		
		// grab details for each itemID now
		foreach($itemIds as $itemID) {
			if($i++ > 29) break;
			// keep building DOM
			
			/*
			$elmItemDetails = $dom->createElement('Item');
			$itmdetailsNode = $itemsNode->appendChild($elmItemDetails);
			$elmItemID = $dom->createElement('ItemID', $itemID);
			$itmdetailsNode->appendChild($elmItemID);
			*/
			
			$getItemDetailsReqDetails = '<ItemID>'.$itemID.'</ItemID><IncludeItemSpecifics>true</IncludeItemSpecifics><DetailLevel>ReturnAll</DetailLevel></GetItemRequest>';
			$getitmdtlxml = $this->_xpable . $this->_getItemDetailsReqRoot . $this->_reqAuth . $getItemDetailsReqDetails; // XML POST for getting item details for a given itemID
			
			$this->_httpclient->resetParameters();
			$this->_httpclient->setHeaders(array('X-EBAY-API-CALL-NAME:'.$this->_apiCallGetItem));
			$this->_httpclient->setRawData($getitmdtlxml, 'text/xml');
			$res = $this->_httpclient->request(Zend_Http_Client::POST);
			if($res->isSuccessful()) {
				//got HTTP 200. Check the response body further
				//print "<pre>"; print_r($res->getBody());exit;
				$body = $res->getBody();
				$itemDom = new DOMDocument();                
				$itemDom->loadXML( $res->getBody() );    
				$ack = $this->_isAckSuccess($itemDom);
				
				if($ack == false) {
					print "\n**** ERROR in GetItem API Call *****";
					//TODO: Error logging?
					exit;
				}
				
				/*
				$data = simplexml_load_string($res->getBody());
				
				print "<pre>";
				print_r($data);*/
				
				$itemdetailsDom = new DOMDocument();
				$itemdetailsDom->loadXML($res->getBody());
				$itmcallItmDetNodeList = $itemdetailsDom->getElementsByTagNameNS($this->_namespace, 'Item');
				
				$itmcallItmDetNode = simplexml_import_dom($itmcallItmDetNodeList->item(0)); 
				/*print "<pre>";
				print_r($itmcallItmDetNode);exit;*/
				$itmDescription = $itmcallItmDetNode->Description;
				//$arrayIDs[$itemID]['Description'] = preg_replace ( "'<[^>]+>'U", "",(string)$itmDescription);
				$arrayIDs[$itemID]['Description'] = htmlentities((string)$itmDescription);
				//$arrayIDs[$itemID]['Description'] = (string)$itmDescription;
				$itmTitle = $itmcallItmDetNode->Title;
				$arrayIDs[$itemID]['Title'] = (string)$itmTitle;
				$listingtype = $itmcallItmDetNode->ListingType;
				$arrayIDs[$itemID]['ListingType'] = (string)$listingtype;
				
				// picture URLs
				$picurl = $itmcallItmDetNode->PictureDetails->PictureURL;
				foreach($picurl as $p) {
					$pics[] = (string)$p;
				}
				$arrayIDs[$itemID]['Pictures'] = $pics;
				unset($pics);
				// entire picture details
				$arrayIDs[$itemID]['PictureDetails'] = $itmcallItmDetNode->PictureDetails;
				
				
				/*print "<pre>";
				print_r($arrayIDs);exit;*/
				
				
				//Start/End dates for the listing
				$listd = $itmcallItmDetNode->ListingDetails->StartTime;
				$starttime = $listd[0];
				$arrayIDs[$itemID]['From'] = (string)$starttime;
				$listd = $itmcallItmDetNode->ListingDetails->EndTime;
				$endtime = $listd[0];
				$arrayIDs[$itemID]['To'] = (string)$endtime;
				
				$listd = $itmcallItmDetNode->PrimaryCategory->CategoryName;
				$CatName = $listd[0];
				$arrayIDs[$itemID]['Category'] = (string)$CatName;
				
				// Different prices
				$BuyItNowPrice = $itmcallItmDetNode->BuyItNowPrice;
				$arrayIDs[$itemID]['BINPrice'] = (string)$BuyItNowPrice;
				$listd = $itmcallItmDetNode->ListingDetails->ConvertedStartPrice;
				$ConvertedStartPrice = $listd[0];
				$arrayIDs[$itemID]['StartPrice'] = (string)$ConvertedStartPrice;
				$selling = $itmcallItmDetNode->SellingStatus->CurrentPrice;
				$CurrentPrice = $selling[0];
				$arrayIDs[$itemID]['CurrentPrice'] = (string)$CurrentPrice;
				
				// Start building item details in dom
				// ... on second thought, we dont need the DOM. everything is built into $arrayIDs  arrau
				//     so commenting out DOM part below
				/*
				$itmTitleElm = $dom->createElement('Title', $itmTitle);
				$itmdetailsNode->appendChild($itmTitleElm);
				
				$itmDescriptionElm = $dom->createElement('Description', $itmDescription);
				$itmdetailsNode->appendChild($itmDescriptionElm);
				
				$itmCat = $dom->createElement('Category', htmlentities($CatName));
				$itmdetailsNode->appendChild($itmCat);
				
				
				$itmListingType = $dom->createElement('ListingType', $listingtype);
				$itmdetailsNode->appendChild($itmListingType);
				
				$itmStartTimeElm = $dom->createElement('StartTime', $starttime);
				$itmdetailsNode->appendChild($itmStartTimeElm);
				
				$itmEndTimeElm = $dom->createElement('EndTime', $endtime);
				$itmdetailsNode->appendChild($itmEndTimeElm);
				
				$itmBINPrice = $dom->createElement('BuyItNowPrice', $BuyItNowPrice);
				$itmdetailsNode->appendChild($itmBINPrice);
				
				$itmStartPrice = $dom->createElement('StartPrice', $ConvertedStartPrice);
				$itmdetailsNode->appendChild($itmStartPrice);
				
				$itmCurrentPrice = $dom->createElement('CurrentPrice', $CurrentPrice);
				$itmdetailsNode->appendChild($itmCurrentPrice);
				*/
				// Item Specifics
				
				/*$itmSpecs = $dom->createElement('ItemSpecs');
				$itemSpecsNode = $itmdetailsNode->appendChild($itmSpecs);*/
				foreach($itmcallItmDetNode->ItemSpecifics->NameValueList as $specific) {
					//print "<br> Name: " . $specific->Name . "  VAL: " . $specific->Value;
					$arrayIDs[$itemID]['itemSpecifics'][(string)$specific->Name] = (string)$specific->Value;
					//$ispec = $dom->createElement(preg_replace( '/\s+/', '', $specific->Name), $specific->Value);
					//$itemSpecsNode->appendChild($ispec);
				}
				// Condition code: pre-owned etc
				$arrayIDs[$itemID]['itemSpecifics']['Condition'] = (string)$itmcallItmDetNode->ConditionDisplayName;
				//exit;		
			}
			else {
				print "<br>ERROR in GetItem call";
			}
		}
		
		/*print '<pre>';
		print_r($arrayIDs);exit;*/
		
		// spit out DOM
		/*$dom->formatOutput = true;
		$x = $dom->saveXML();*/
		/*print "<pre>";
		print htmlentities($x);
		exit;*/
		return $arrayIDs;
	}
	
	/**
	 * Returns a list of items listed by seller
	 * @return array(ItemIDs)
	 */
	private function _fetchList() {
		$this->_getAlreadyImported();
		$itemIDs = array();
		$this->_httpclient->setHeaders(array('X-EBAY-API-CALL-NAME:'.$this->_apiCallSellerList));
		$getitmlistxml = $this->_xpable . $this->_getListReqRoot . $this->_reqAuth . $this->_buildSellerListReqXml(); // XML POST for getting seller's list
		$this->_httpclient->setRawData($getitmlistxml, 'text/xml');
		$res = $this->_httpclient->request(Zend_Http_Client::POST);
		if($res->isSuccessful()) {
			
			// Got HTTP 200. Parse the response to see if things went really ok
			$listDom = new DOMDocument();                
			$listDom->loadXML( $res->getBody());
			if($this->_isAckSuccess($listDom)) {
				// go thru Item nodes to grab all ItemIDs
				$itemsListedNode=$listDom->getElementsByTagNameNS($this->_namespace,'Item');
				$itemCount = $itemsListedNode->length;
				
				for($i=0;$i<$itemCount;$i++) {
					$itm = simplexml_import_dom($itemsListedNode->item($i));
					if(!in_array((string)$itm->ItemID, $this->_alreadyImported)) {
						$itemIDs[] = (string)$itm->ItemID;;
					}
				}
			}
		}
		return $itemIDs;
	}
	
	/**
	 * 
	 * Builds a list of listing IDs that already have been imported
	 */
	private function _getAlreadyImported() {
		$file = APPLICATION_PATH . '/application/settings/database.php';
	    $options = include $file;
	    $db = Zend_Db::factory($options['adapter'], $options['params']);
		$select = $db->select()
			->from(array('m' => $options['params']['dbname'].'.engine4_marketplace_marketplaces'),array('entry_source_ref'))
			->where('m.owner_id='.$this->_upheelsUserId . ' and entry_source_ref is not null');
		$listings = $db->fetchAll($select);
		foreach($listings as  $listingid) {
			$this->_alreadyImported[] = $listingid['entry_source_ref'];
		}
	}
	
	private function _buildSellerListReqXml() {
		return '<StartTimeFrom>' . $this->_starttime . '</StartTimeFrom><StartTimeTo>' . $this->_endtime . '</StartTimeTo><UserID>'.$this->_seller.'</UserID></GetSellerListRequest>';
	}
	
	private function _buildGetCategoriesReqXml($categoryID,$siteID=0) {
		//return '<CategorySiteID>'.$siteID.'</CategorySiteID><DetailLevel>ReturnAll</DetailLevel></GetCategoriesRequest>';
		return '<LevelLimit>10</LevelLimit><ViewAllNodes>false</ViewAllNodes><CategorySiteID>'.$siteID.'</CategorySiteID><DetailLevel>ReturnAll</DetailLevel><CategoryParent>'.$categoryID.'</CategoryParent></GetCategoriesRequest>';
	}
	
	private function _buildGetCategorySpecificsReqXml($catID) {
		return '<CategorySpecific><CategoryID>'.$catID.'</CategoryID></CategorySpecific></GetCategorySpecificsRequest>';
	}
	
	private function _setXHttpHeaders() {
		$this->_httpclient->setHeaders(array(
							    'X-EBAY-API-COMPATIBILITY-LEVEL:'.$this->_apiVersion,
								'X-EBAY-API-DEV-NAME:'.$this->_apiDevName,
								'X-EBAY-API-APP-NAME:'.$this->_apiAppName,
								'X-EBAY-API-CERT-NAME:'.$this->_apiCertName,
								'X-EBAY-API-SITEID:'.$this->_apiSiteID));
								//'X-EBAY-API-CALL-NAME:'.$this->_apiCallSellerList));
	}
	
	public function getSeller() {
		return $this->_seller;
	}
	public function setSeller($seller) {
		$this->_seller = $seller;
	}
	public function getSandbox()
	{
		return $this->_sandbox;
	}
	public function setSandbox($sandbox) {
		$this->_sandbox = $sandbox;
	}
	public function setListingFrom($starttime) {
		$this->_starttime = $starttime;
	}
	public function setListinTo($endtime) {
		$this->_endtime = $endtime;
	}
	public function getListingFrom() {
		return $this->_starttime;
	}
	public function getListinTo() {
		return $this->_endtime;
	}
	public function setUpheelsSellerId($id) {
		$this->_upheelsUserId = $id;
	}
	
	private function _isAckSuccess($dom) {
		
		$ackNode=$dom->getElementsByTagNameNS ($this->_namespace,'Ack');
		simplexml_import_dom($ackNode->item(0))=='Success'?$ack=true:$ack=false;
		return $ack;
	}
}
?>
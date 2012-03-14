<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2006-2010
 * * 
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2006-2010
 * * 
 */
class Marketplaceups_Api_Core extends Core_Api_Abstract
{
  
  public function ups($dest_zip,$service,$weight,$length,$width,$height, $marketplace_zip_code = 0) {
	$AccessLicenseNumber = Engine_Api::_()->getApi('settings', 'core')->getSetting('marketplace.ups.license', '6C873DD53E7776B0'); // Your license number
	$UserId = 'username'; // Username
	$Password = 'password'; // Password
	$PostalCode = $marketplace_zip_code; // Zipcode you are shipping FROM
	$ShipperNumber = '98765'; // Your UPS shipper number

    	$data ="<?xml version=\"1.0\"?>
    	<AccessRequest xml:lang=\"en-US\">
    		<AccessLicenseNumber>$AccessLicenseNumber</AccessLicenseNumber>
    		<UserId>$UserId</UserId>
    		<Password>$Password</Password>
    	</AccessRequest>
    	<?xml version=\"1.0\"?>
    	<RatingServiceSelectionRequest xml:lang=\"en-US\">
    		<Request>
    			<TransactionReference>
    				<CustomerContext>Bare Bones Rate Request</CustomerContext>
    				<XpciVersion>1.0001</XpciVersion>
    			</TransactionReference>
    			<RequestAction>Rate</RequestAction>
    			<RequestOption>Rate</RequestOption>
    		</Request>
    	<PickupType>
    		<Code>01</Code>
    	</PickupType>
    	<Shipment>
    		<Shipper>
    			<Address>
    				<PostalCode>$PostalCode</PostalCode>
    				<CountryCode>US</CountryCode>
    			</Address>
			<ShipperNumber>$ShipperNumber</ShipperNumber>
    		</Shipper>
    		<ShipTo>
    			<Address>
    				<PostalCode>$dest_zip</PostalCode>
    				<CountryCode>US</CountryCode>
				<ResidentialAddressIndicator/>
    			</Address>
    		</ShipTo>
    		<ShipFrom>
    			<Address>
    				<PostalCode>$PostalCode</PostalCode>
    				<CountryCode>US</CountryCode>
    			</Address>
    		</ShipFrom>
    		<Service>
    			<Code>$service</Code>
    		</Service>
    		<Package>
    			<PackagingType>
    				<Code>02</Code>
    			</PackagingType>
    			<Dimensions>
    				<UnitOfMeasurement>
    					<Code>IN</Code>
    				</UnitOfMeasurement>
    				<Length>$length</Length>
    				<Width>$width</Width>
    				<Height>$height</Height>
    			</Dimensions>
    			<PackageWeight>
    				<UnitOfMeasurement>
    					<Code>LBS</Code>
    				</UnitOfMeasurement>
    				<Weight>$weight</Weight>
    			</PackageWeight>
    		</Package>
    	</Shipment>
    	</RatingServiceSelectionRequest>";
    	$ch = curl_init("https://www.ups.com/ups.app/xml/Rate");
    	curl_setopt($ch, CURLOPT_HEADER, 1);
    	curl_setopt($ch,CURLOPT_POST,1);
    	curl_setopt($ch,CURLOPT_TIMEOUT, 60);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    	$result=curl_exec ($ch);
		//echo '<!-- '. $result. ' -->'; // THIS LINE IS FOR DEBUG PURPOSES ONLY-IT WILL SHOW IN HTML COMMENTS
    	$data = strstr($result, '<?');
    	$xml_parser = xml_parser_create();
    	xml_parse_into_struct($xml_parser, $data, $vals, $index);
    	xml_parser_free($xml_parser);
    	$params = array();
    	$level = array();
    	foreach ($vals as $xml_elem) {
    	 if ($xml_elem['type'] == 'open') {
			if (array_key_exists('attributes',$xml_elem)) {
				 list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
			} else {
				 $level[$xml_elem['level']] = $xml_elem['tag'];
			}
    	 }
    	 if ($xml_elem['type'] == 'complete') {
			$start_level = 1;
			$php_stmt = '$params';
			while($start_level < $xml_elem['level']) {
				 $php_stmt .= '[$level['.$start_level.']]';
				 $start_level++;
			}
			$php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
			if(!empty($xml_elem['value']))
				eval($php_stmt);
    	 }
    	}
    	curl_close($ch);
    	return $params['RATINGSERVICESELECTIONRESPONSE']['RATEDSHIPMENT']['TOTALCHARGES']['MONETARYVALUE'];
  }
}
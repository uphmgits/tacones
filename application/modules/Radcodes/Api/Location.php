<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Radcodes_Api_Location extends Core_Api_Abstract
{
  
  public function getSelectProximity($type, $location, $distance, $select = null)
  {
    //$itemClass = Engine_Api::_()->getItemClass($type);
    
    $table = Engine_Api::_()->getItemTable($type);
    $rName = $table->info('name');
    $rPrimary = $table->info('primary');
    reset($rPrimary);
    $cPrimary = current($rPrimary);
    

    if ($select === null)
    {
      $select = $table->select();
    }
    
		$google_map = new Radcodes_Lib_Google_Map();
		$geocoded_address = $google_map->geocode($location);
		
		if ($geocoded_address)
		{
			$locName = Engine_Api::_()->getDbtable('locations', 'radcodes')->info('name');
			
			$select = $select
			  ->setIntegrityCheck(false)
			  ->from($rName)
			  ->joinLeft($locName, "$locName.parent_id = $rName.$cPrimary")
			  ->where($locName.'.parent_type = ?', $type);
			
			if ($geocoded_address->hasType(Radcodes_Lib_Google_Map_GeocodedAddress::TYPE_COUNTRY))
			{
			  $select->where($locName.'.country IN (?)', array($geocoded_address->getCountry(), $geocoded_address->getCountryCode()));
			}
			else if ($geocoded_address->hasType(Radcodes_Lib_Google_Map_GeocodedAddress::TYPE_PROVINCE))
			{
			  $select->where($locName.'.state IN (?)', array($geocoded_address->getProvince(), $geocoded_address->getProvinceCode()));
			}
			else
			{
			  $coord = $geocoded_address->getCoord();
			  $select = $coord->getCriteriaInRadius($locName.'.lat', $locName.'.lng', $distance, $select);
			}
		}
		
		return $select;
  }
  
	public function getSingletonLocation($parent)
	{
		$location = $this->getMappableLocation($parent);
		if (!$location)
		{
			$location = $parent->updateLocation();
		}
		return $location;
	}
	
  public function getMappableLocation($parent)
  {
    $table = Engine_Api::_()->getDbtable('locations', 'radcodes');
    $select = $table->select()
      ->where('parent_type = ?', $parent->getType())
      ->where('parent_id = ?', $parent->getIdentity())
      ->order('location_id ASC')
      ->limit(1);
  
    $location = $table->fetchRow($select);
    
    return $location;
  }
  
  public function deleteMappableLocation($parent)
  {
    $location = $this->getMappableLocation($parent);
    if ($location)
    {
      $location->delete();
    }
  }
  
  public function updateMappableLocation($parent, $data)
  {
    if (is_string($data))
    {
      $google_map = new Radcodes_Lib_Google_Map();
      $data = $google_map->geocode($data);
    }
    
    if ($data instanceof Radcodes_Lib_Google_Map_GeocodedAddress)
    {
      $data = array(
				'formatted_address' => $data->getFormattedAddress(),
				'street_address' => $data->getStreetAddress(),      
				'city' => $data->getCity(),
				'state' => $data->getProvince(),
				'country' => $data->getCountryCode(),
				'zip' => $data->getPostalCode(),
				'lat' => $data->getLat(),
				'lng' => $data->getLng(),
      );
    }
    
    if (empty($data))
    {
      $this->deleteMappableLocation($parent);
    }
    else
    {
      $location = $this->getMappableLocation($parent);
      
      if ($location === null)
      {
	      $table = Engine_Api::_()->getDbtable('locations', 'radcodes');
	      if (empty($data['parent_type']) || empty($data['parent_id']))
	      {
	        $data['parent_type'] = $parent->getType();
	        $data['parent_id'] = $parent->getIdentity();
	      }
	      $location = $table->createRow();
      }

	    $location->setFromArray($data);
	    $location->save();
	    
	    return $location;
	  }
  }

  
  
}

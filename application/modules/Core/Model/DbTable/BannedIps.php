<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: BannedIps.php 9099 2011-07-22 21:56:22Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_BannedIps extends Engine_Db_Table
{
  public function addAddress($address)
  {
    $addressObject = new Engine_IP($address);
    if( !$addressObject->isValid() ) {
      throw new Engine_Exception('Invalid IP address');
    }

    $addressBinary = $addressObject->toBinary();
    
    try {
      $this->insert(array(
        'start' => $addressBinary,
        'stop' => $addressBinary,
      ));
    } catch( Exception $e ) {
      // Silence
    }

    return $this;
  }

  public function addAddressRange($startAddress, $stopAddress)
  {
    $startAddressObject = new Engine_IP($startAddress);
    $stopAddressObject = new Engine_IP($stopAddress);

    if( !$startAddressObject->isValid() ) {
      throw new Engine_Exception('Invalid start IP address');
    }
    if( !$stopAddressObject->isValid() ) {
      throw new Engine_Exception('Invalid stop IP address');
    }

    $startAddressBinary = $startAddressObject->toBinary();
    $stopAddressBinary = $stopAddressObject->toBinary();

    try {
      $this->insert(array(
        'start' => $addressBinary,
        'stop' => $addressBinary,
      ));
    } catch( Exception $e ) {
      // Silence
    }

    return $this;
  }

  public function getAddresses()
  {
    $data = $this->select()
        ->from($this, array('start', 'stop'))
        ->order('start ASC')
        ->query()
        ->fetchAll();

    $addresses = array();
    foreach( $data as $datum ) {
      if( $datum['start'] == $datum['stop'] ) {
        $addresses[] = Engine_IP::normalizeAddress($datum['start']);
      } else {
        $startStr = Engine_IP::normalizeAddress($datum['start']);
        $stopStr = Engine_IP::normalizeAddress($datum['stop']);
        //$addresses[] = $startStr . ' - ' . $stopStr;
        if( $startStr && $stopStr ) {
          $addresses[] = array($startStr, $stopStr);
        }
      }
    }

    return array_filter($addresses);
  }

  public function isAddressBanned($address, $spec = null)
  {
    $addressObject = new Engine_IP($address);
    $addressBinary = $addressObject->toBinary();

    // Load banned IPs
    if( null === $spec ) {
      $bannedIps = $this->select()
          ->from($this)
          ->query()
          ->fetchAll();
    } else {
      $bannedIps = $spec;
    }

    $isBanned = false;
    foreach( $bannedIps as $bannedIp ) {
      // @todo ipv4->ipv6 transformations
      if( strlen($addressBinary) == strlen($bannedIp['start']) ) {
        if( strcmp($addressBinary, $bannedIp['start']) >= 0 &&
            strcmp($addressBinary, $bannedIp['stop']) <= 0 ) {
          $isBanned = true;
          break;
        }
      }
    }

    return (bool) $isBanned;
  }

  public function setAddresses($addresses)
  {
    // Build assoc for existing addresses
    $data = $this->select()
        ->from($this)
        ->query()
        ->fetchAll();

    $currentAddresses = array();
    foreach( $data as $datum ) {
      $currentAddresses[bin2hex($datum['start']) . '-' . bin2hex($datum['stop'])] = $datum['bannedip_id'];
    }

    // Build assoc array for new addresses
    $newAddresses = $this->normalizeAddressArray($addresses);

    // Get added addresses and removed addresses
    $addedAddresses = array_diff_key($newAddresses, $currentAddresses);
    $removedAddresses = array_diff_key($currentAddresses, $newAddresses);

    // Do added addresses
    foreach( $addedAddresses as $addedAddress ) {
      $this->insert(array(
        'start' => $addedAddress['start'],
        'stop' => $addedAddress['stop'],
      ));
    }
    
    // Do removed addresses
    foreach( $removedAddresses as $removedAddress ) {
      $this->delete(array(
        'start = ?' => $removedAddress['start'],
        'stop = ?' => $removedAddress['stop'],
      ));
    }

    return $this;
  }

  public function removeAddress($address)
  {
    $addressObject = new Engine_IP($address);
    if( !$addressObject->isValid() ) {
      throw new Engine_Exception('Invalid IP address');
    }

    $addressBinary = $addressObject->toBinary();

    // Delete
    $this->delete(array(
      'start' => $addressBinary,
      'stop' => $addressBinary,
    ));

    return $this;
  }

  public function removeAddressRange($startAddress, $stopAddress)
  {
    $startAddressObject = new Engine_IP($startAddress);
    $stopAddressObject = new Engine_IP($stopAddress);

    if( !$startAddressObject->isValid() ) {
      throw new Engine_Exception('Invalid start IP address');
    }
    if( !$stopAddressObject->isValid() ) {
      throw new Engine_Exception('Invalid stop IP address');
    }

    $startAddressBinary = $startAddressObject->toBinary();
    $stopAddressBinary = $stopAddressObject->toBinary();

    // Delete
    $this->delete(array(
      'start' => $startAddressBinary,
      'stop' => $stopAddressBinary,
    ));

    return $this;
  }
  
  public function normalizeAddressArray($addresses)
  {
    $data = array();
    foreach( $addresses as $address ) {
      if( is_string($address) ) {
        $start = Engine_IP::normalizeAddressToBinary($address);
        $stop = Engine_IP::normalizeAddressToBinary($address);
      } else if( is_array($address) ) {
        $start = Engine_IP::normalizeAddressToBinary($address[0]);
        $stop = Engine_IP::normalizeAddressToBinary($address[1]);
      } else {
        continue;
      }
      $data[bin2hex($start) . '-' . bin2hex($stop)] = array(
        'start' => $start, 
        'stop' => $stop
      );
    }
    return $data;
  }
}

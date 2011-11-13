<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: BannedUsernames.php 9382 2011-10-14 00:41:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_BannedUsernames extends Engine_Db_Table
{
  public function addUsername($username)
  {
    $exists = (bool) $this->select()
        ->from($this, new Zend_Db_Expr('TRUE'))
        ->where('username = ?', $username)
        ->query()
        ->fetch();

    if( !$exists ) {
      $this->insert(array(
        'username = ?' => strtolower($username),
      ));
    }

    return $this;
  }

  public function addUsernames($usernames)
  {
    if( empty($usernames) || !is_array($usernames) ) {
      return $this;
    }
    
    $usernames = array_map('strtolower', array_values($usernames));

    $data = $this->select()
        ->from($this, 'username')
        ->where('username IN(?)', $usernames)
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    // New emails
    $newEmails = array_diff($usernames, $data);

    foreach( $newUsernames as $newUsername ) {
      $this->insert(array(
        'username' => $newUsername,
      ));
    }

    return $this;
  }

  public function getUsernames()
  {
    return $this->select()
        ->from($this, 'username')
        ->order('username ASC')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  public function isUsernameBanned($username)
  {
    $data = $this->select()
        ->from($this, 'username')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $isBanned = false;

    foreach( $data as $test ) {
      if( false === strpos($test, '*') ) {
        if( strtolower($username) == $test ) {
          $isBanned = true;
          break;
        }
      } else {
        $pregExpr = preg_quote($test, '/');
        $pregExpr = str_replace('*', '.*?', $pregExpr);
        $pregExpr = '/' . $pregExpr . '/i';
        if( preg_match($pregExpr, $username) ) {
          $isBanned = true;
          break;
        }
      }
    }

    return $isBanned;
  }

  public function setUsernames($usernames)
  {
    $usernames = array_map('strtolower', array_values($usernames));

    $data = $this->select()
        ->from($this, 'username')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    // New emails
    $newUsernames = array_diff($usernames, $data);
    foreach( $newUsernames as $newUsername ) {
      $this->insert(array(
        'username' => $newUsername,
      ));
    }

    // Removed emails
    $removedUsernames = array_diff($data, $usernames);
    if( !empty($removedUsernames) ) {
      $this->delete(array(
        'username IN(?)' => $removedUsernames,
      ));
    }

    return $this;
  }

  public function removeUsername($username)
  {
    $this->delete(array(
      'username = ?' => strtolower($username),
    ));

    return $this;
  }

  public function removeUsernames($usernames)
  {
    if( empty($usernames) || !is_array($usernames) ) {
      return $this;
    }
    
    $usernames = array_map('strtolower', array_values($usernames));

    $this->delete(array(
      'username IN(?)' => $usernames,
    ));

    return $this;
  }
}

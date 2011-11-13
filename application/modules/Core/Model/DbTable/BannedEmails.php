<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: BannedEmails.php 9235 2011-09-02 22:32:26Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_BannedEmails extends Engine_Db_Table
{
  public function addEmail($email)
  {
    $exists = (bool) $this->select()
        ->from($this, new Zend_Db_Expr('TRUE'))
        ->where('email = ?', $email)
        ->query()
        ->fetch();

    if( !$exists ) {
      $this->insert(array(
        'email = ?' => strtolower($email),
      ));
    }

    return $this;
  }

  public function addEmails($emails)
  {
    if( empty($emails) || !is_array($emails) ) {
      return $this;
    }
    
    $emails = array_map('strtolower', array_values($emails));
    
    $data = $this->select()
        ->from($this, 'email')
        ->where('email IN(?)', $emails)
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    // New emails
    $newEmails = array_diff($emails, $data);

    foreach( $newEmails as $newEmail ) {
      $this->insert(array(
        'email' => $newEmail,
      ));
    }

    return $this;
  }

  public function getEmails()
  {
    return $this->select()
        ->from($this, 'email')
        ->order('email ASC')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  public function isEmailBanned($email)
  {
    $email = trim($email);
    
    $data = $this->select()
        ->from($this, 'email')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $isBanned = false;

    foreach( $data as $test ) {
      if( false === strpos($test, '*') ) {
        if( strtolower($email) == $test ) {
          $isBanned = true;
          break;
        }
      } else if( $test[0] == '/' ) {
        if( @preg_match($test, $email) ) {
          $isBanned = true;
          break;
        }
      } else {
        $pregExpr = preg_quote($test, '/');
        $pregExpr = str_replace('\\*', '.*', $pregExpr);
        $pregExpr = '/^' . $pregExpr . '$/i';
        if( preg_match($pregExpr, $email) ) {
          $isBanned = true;
          break;
        }
      }
    }

    return $isBanned;
  }

  public function setEmails($emails)
  {
    $emails = array_unique(array_map('strtolower', array_values($emails)));

    $data = $this->select()
        ->from($this, 'email')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);
    
    // New emails
    $newEmails = array_diff($emails, $data);
    foreach( $newEmails as $newEmail ) {
      $this->insert(array(
        'email' => $newEmail,
      ));
    }

    // Removed emails
    $removedEmails = array_diff($data, $emails);
    if( !empty($removedEmails) ) {
      $this->delete(array(
        'email IN(?)' => $removedEmails,
      ));
    }

    return $this;
  }

  public function removeEmail($email)
  {
    $this->delete(array(
      'email = ?' => strtolower($email),
    ));

    return $this;
  }

  public function removeEmails($emails)
  {
    if( empty($emails) || !is_array($emails) ) {
      return $this;
    }
    
    $emails = array_map('strtolower', array_values($emails));
    
    $this->delete(array(
      'email IN(?)' => $emails,
    ));

    return $this;
  }
}

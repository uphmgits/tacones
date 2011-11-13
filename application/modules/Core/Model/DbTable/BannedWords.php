<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: BannedWords.php 9036 2011-06-29 21:06:30Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_BannedWords extends Engine_Db_Table
{
  public function addWord($word)
  {
    $exists = (bool) $this->select()
        ->from($this, new Zend_Db_Expr('TRUE'))
        ->where('word = ?', $word)
        ->query()
        ->fetch();

    if( !$exists ) {
      $this->insert(array(
        'word = ?' => strtolower($word),
      ));
    }

    return $this;
  }

  public function addWords($words)
  {
    if( empty($words) || !is_array($words) ) {
      return $this;
    }

    $words = array_map('strtolower', array_values($words));

    $data = $this->select()
        ->from($this, 'word')
        ->where('word IN(?)', $words)
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    // New emails
    $newEmails = array_diff($words, $data);

    foreach( $newWords as $newWord ) {
      $this->insert(array(
        'word' => $newWord,
      ));
    }

    return $this;
  }

  public function getWords()
  {
    return $this->select()
        ->from($this, 'word')
        ->order('word ASC')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);
  }

  public function isWordBanned($word)
  {
    $data = $this->select()
        ->from($this, 'word')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $isBanned = false;

    foreach( $data as $test ) {
      if( false === strpos($test, '*') ) {
        if( strtolower($word) == $test ) {
          $isBanned = true;
          break;
        }
      } else {
        $pregExpr = preg_quote($test, '/');
        $pregExpr = str_replace('*', '.*?', $pregExpr);
        $pregExpr = '/' . $pregExpr . '/i';
        if( preg_match($pregExpr, $word) ) {
          $isBanned = true;
          break;
        }
      }
    }

    return $isBanned;
  }

  public function setWords($words)
  {
    $words = array_map('strtolower', array_filter(array_values($words)));

    $data = $this->select()
        ->from($this, 'word')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    // New emails
    $newWords = array_diff($words, $data);
    foreach( $newWords as $newWord ) {
      $this->insert(array(
        'word' => $newWord,
      ));
    }

    // Removed emails
    $removedWords = array_diff($data, $words);
    if( !empty($removedWords) ) {
      $this->delete(array(
        'word IN(?)' => $removedWords,
      ));
    }

    return $this;
  }

  public function removeWord($word)
  {
    $this->delete(array(
      'word = ?' => strtolower($word),
    ));

    return $this;
  }

  public function removeWords($words)
  {
    if( empty($words) || !is_array($words) ) {
      return $this;
    }

    $words = array_map('strtolower', array_values($words));

    $this->delete(array(
      'word IN(?)' => $words,
    ));

    return $this;
  }
}

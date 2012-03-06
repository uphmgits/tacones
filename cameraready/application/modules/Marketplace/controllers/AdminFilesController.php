<?php
/**
 * SocialEngineMarket
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
 */

class Marketplace_AdminFilesController extends Core_Controller_Action_Admin
{
  protected $_basePath;

  public function init()
  {
    // Check if folder exists and is writable
    if( !file_exists(APPLICATION_PATH . '/public/masspay') ) {
      return $this->_forward('error', null, null, array(
        'message' => 'The public/masspay folder does not exist. Please create this folder.',
      ));
    }
    if( !file_exists(APPLICATION_PATH . '/public/masspay_return') ) {
      return $this->_forward('error', null, null, array(
        'message' => 'The public/masspay folder does not exist. Please create this folder.',
      ));
    }
    
    // Set base path
    $this->_basePath = realpath(APPLICATION_PATH . '/public/masspay');

    $this->view->file_filter_list = $file_filter_list = $this->_getParam('file_filter_list', 0);
    if( $file_filter_list ) $this->_basePath = realpath(APPLICATION_PATH . '/public/masspay_return');

  }

  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main', array(), 'marketplace_admin_main_ordermanagement');
    $this->view->subNavigation = $subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('marketplace_admin_main_ordermanagement', array(), 'marketplace_admin_main_ordermanagement_files');

    // Get path
    $this->view->path = $path = $this->_getPath();
    $this->view->relPath = $relPath = $this->_getRelPath($path);

    // List files
    $files = array();
    $dirs = array();
    $contents = array();
    $it = new DirectoryIterator($path);
    foreach( $it as $key => $file ) {
      $filename = $file->getFilename();
      if( ($it->isDot() && $this->_basePath == $path) || $filename == '.' || ($filename != '..' && $filename[0] == '.') ) {
        continue;
      }
      $relPath = trim(str_replace($this->_basePath, '', realpath($file->getPathname())), '/\\');
      $ext = strtolower(ltrim(strrchr($file->getFilename(), '.'), '.'));
      if( $file->isDir() ) $ext = null;
      $type = 'generic';
      switch( true ) {
        case in_array($ext, array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'tif', 'svg')):
          $type = 'image';
          break;
        case in_array($ext, array('txt', 'log', 'js')):
          $type = 'text';
          break;
        case in_array($ext, array('html', 'htm'/*, 'php'*/)):
          $type = 'markup';
          break;
      }
      $dat = array(
        'name' => $file->getFilename(),
        'path' => $file->getPathname(),
        'info' => $file->getPathInfo(),
        'rel' => $relPath,
        'ext' => $ext,
        'type' => $type,
        'is_dir' =>  $file->isDir(),
        'is_file' => $file->isFile(),
        'is_image' => ( $type == 'image' ),
        'is_text' => ( $type == 'text' ),
        'is_markup' => ( $type == 'markup' ),
      );
      if( $it->isDir() ) {
        $dirs[$relPath] = $dat;
      } else if( $it->isFile() ) {
        $files[$relPath] = $dat;
      }
      $contents[$relPath] = $dat;
    }
    ksort($contents);

    $this->view->paginator = $paginator = Zend_Paginator::factory($contents);
    $paginator->setItemCountPerPage(20);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->files = $files;
    $this->view->dirs = $dirs;
    $this->view->contents = $contents;
  }

  public function renameAction()
  {
    $path = $this->_getPath();

    $this->view->fileIndex = $this->_getParam('index');

    if( !is_file($path) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    @list($fileName, $fileExt) = explode('.', basename($path), 2);
    
    $this->view->form = $form = new Core_Form_Admin_File_Rename();
    $form->name->setValue($fileName);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $filename = $form->getValue('name');
    $newPath = dirname($path) . DIRECTORY_SEPARATOR . $filename . '.' . $fileExt;

    if( !rename($path, $newPath) ) {
      $form->addError('Unable to rename');
      return;
    }

    $this->view->fileName = basename($newPath);
    $this->view->status = true;

    // Redirect
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    } else if( 'smoothbox' === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
      ));
    }
  }

  public function deleteAction()
  {
    $path = $this->_getPath();

    $this->view->fileIndex = $this->_getParam('index');
    
    if( !file_exists($path) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
    
    $this->view->form = $form = new Core_Form_Admin_File_Delete();
    $form->setAction($_SERVER['REQUEST_URI']);
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $vals = $this->getRequest()->getPost();
    if( !empty($vals['actions']) && is_array($vals['actions']) ) {
      $vals['actions'] = join(PATH_SEPARATOR, $vals['actions']);
    }
    $form->isValid($vals);    
    if( is_dir($path) ) {
      $actions = $vals['actions'];
      if( is_string($actions) ) {
        $actions = explode(PATH_SEPARATOR, $actions);
      } else if( !is_array($actions) ) {
        $actions = array(); // blegh
      }

      $it = new DirectoryIterator($path);
      foreach( $it as $file ) {
        if( !$file->isFile() ) continue;
        if( in_array($file->getFilename(), $actions) ) {
          if( !unlink($file->getPathname()) ) {
            // Blegh
          }
        }
      }
      
    } else if( is_file($path) ) {

      if( !@unlink($path) ) {
        return $form->addError('Unable to delete');
      }
      
    }

    $this->view->status = true;

    // Redirect
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    } else if( 'smoothbox' === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
      ));
    }
  }

  public function previewAction()
  {
    // Get path
    $path = $this->_getRelPath('path');

    if( file_exists($path) && is_file($path) ) {
      echo file_get_contents($path);
    }
    exit(); // ugly
  }

  public function downloadAction()
  {
    // Get path
    $path = $this->_getPath();

    if( file_exists($path) && is_file($path) ) {
      // Kill zend's ob
      while( ob_get_level() > 0 ) {
        ob_end_clean();
      }

      header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      header("Content-Length: " . filesize($path), true);
      flush();

      $fp = fopen($path, "r");
      while( !feof($fp) )
      {
        echo fread($fp, 65536);
        flush();
      }
      fclose($fp);
    }
    
    exit(); // Hm....
  }

  public function errorAction()
  {
    $this->getResponse()->setBody($this->view->translate($this->_getParam('message', 'error')));
    $this->_helper->viewRenderer->setNoRender(true);
  }



  protected function _getPath($key = 'path')
  {
    return $this->_checkPath(urldecode($this->_getParam($key, '')), $this->_basePath);
  }

  protected function _getRelPath($path, $basePath = null)
  {
    if( null === $basePath ) $basePath = $this->_basePath;
    $path = realpath($path);
    $basePath = realpath($basePath);
    $relPath = trim(str_replace($basePath, '', $path), '/\\');
    return $relPath;
  }
  
  protected function _checkPath($path, $basePath)
  {
    // Sanitize
    //$path = preg_replace('/^[a-z0-9_.-]/', '', $path);
    $path = preg_replace('/\.{2,}/', '.', $path);
    $path = preg_replace('/[\/\\\\]+/', '/', $path);
    $path = trim($path, './\\');
    $path = $basePath . '/' . $path;

    // Resolve
    $basePath = realpath($basePath);
    $path = realpath($path);
    
    // Check if this is a parent of the base path
    if( $basePath != $path && strpos($basePath, $path) !== false ) {
      return $this->_helper->redirector->gotoRoute(array());
    }

    return $path;
  }
}

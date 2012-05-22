<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: CompareController.php 7432 2010-09-20 23:30:18Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class ToolsController extends Zend_Controller_Action
{
  /**
   * @var Engine_Package_Manager
   */
  protected $_packageManager;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Zend_Cache_Core
   */
  protected $_cache;

  protected $_compareCountIndex;

  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Get manager
    $this->_packageManager = Zend_Registry::get('Engine_Package_Manager');

    // Get session
    $this->_session = new Zend_Session_Namespace('InstallToolsController');

    // Get cache
    if( !Zend_Registry::isRegistered('Cache') ) {
      throw new Engine_Exception('Cache could not be initialized. Please try setting full permissions on temporary/cache');
    }
    $this->_cache = Zend_Registry::get('Cache');
  }

  public function __call($method, $args = array())
  {
    // Proxy externals for
    if( 'externalsAction' == $method ) {
      $path = APPLICATION_PATH . '/application/libraries/Adminer';
      list($base, $static) = explode('externals', $_SERVER['REQUEST_URI']);
      $this->_outputFile($path . '/externals' . $static);
      exit();
    }
    
    parent::__call($methodName, $args);
  }

  public function indexAction()
  {
    $this->view->hasAdminer = file_exists(APPLICATION_PATH . '/application/libraries/Adminer');
  }




  public function adminerAction()
  {
    // Get config
    $path = APPLICATION_PATH . '/application/libraries/Adminer';
    $adminerPath = $path . '/adminer';
    $configFile = APPLICATION_PATH . '/install/config/adminer.php';

    $config = array();
    if( file_exists($configFile) ) {
      $config = include $configFile;
    }

    // Adminer missing
    if( !file_exists($adminerPath . '/index.php') ) {
      throw new Engine_Exception('Adminer is missing');
    }

    // Proxy static resources
    else if( '' != $this->_getParam('static') ) {
      list($base, $static) = explode('static', $_SERVER['REQUEST_URI']);
      $this->_outputFile($adminerPath . '/static' . $static);
      exit();
    }

    // Adminer main
    else {
      // Check request uri
      list($request_filename) = explode('?', $_SERVER['REQUEST_URI']);
      if( substr($request_filename, -1) != '/' && false === strpos($request_filename, 'adminer/adminer.php') ) {
        header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
        exit();
      }

      // Kill output buffering?
      while( ob_get_level() > 0 ) {
        ob_end_clean();
      }

      // Change directory
      chdir($adminerPath);

      // Add autologin
      if( !empty($config['autologin']) &&
          $_SERVER['REQUEST_METHOD'] == 'GET' &&
          empty($_SESSION["usernames"]) ) {
        $db = Zend_Registry::get('Zend_Db');
        $dbConfig = $db->getConfig();
        $_POST['server'] = $dbConfig['host'];
        $_POST['username'] = $dbConfig['username'];
        $_POST['password'] = $dbConfig['password'];
      }
      
      // Globals in: adminer.inc.php
      global $VERSION, $connection;
      // Globals in: auth.inc.php
      global $connection, $adminer;
      // Globals in: connect.inc.php
      global $connection, $VERSION, $token, $error;
      // Globals in: design.inc.php
      global $LANG, $VERSION, $adminer, $connection;
      // Globals in: editing.inc.php
      global $structured_types, $unsigned, $inout, $enum_length, $connection, $types;
      // Globals in: export.inc.php
      global $connection;
      // Globals in: functions.inc.php
      global $connection, $error, $adminer, $types;
      // Globals in: lang.inc.php
      global $LANG, $translations, $langs;
      // Globals in: mysql.inc.php
      global $adminer, $connection, $on_actions;

      define('_ENGINE_ADMINER', true);
      
      include $adminerPath . '/index.php';
      
      exit();
    }
  }
  
  public function apcAction()
  {
    
  }
  
  public function compareAction()
  {
    // Get packages
    $packages = $this->_packageManager->listInstalledPackages();

    // Get cached diffs
    if( isset($this->_session->diffs) ) {
      $diffs = $this->_session->diffs;
    } else {
      $this->_session->diffs = $diffs = new Engine_Cache_ArrayContainer(array(), $this->_cache);
    }

    // Flush diffs
    if( $this->_getParam('flush') ) {
      $diffs->clean();
      unset($diffs);
      unset($this->_session->diffs);
      return $this->_helper->redirector->gotoRoute(array('flush' => null));
    }

    // Check for skip identical
    $showAll = (bool) $this->_getParam('all', false);

    // Build diffs
    $indexedCount = array();
    if( $diffs->count() <= 0 ) {
      foreach( $packages as $key => $package ) {
        $operation = new Engine_Package_Manager_Operation_Install($this->_packageManager, $package);
        $fileOperations = $operation->getFileOperations(!$showAll);
        $fileOperations = $fileOperations['operations'];

        $currentCount = 0;
        $indexedOperations = array();
        if( !empty($fileOperations) ) {

          // Re-index file operations
          do {
            // Get key/val and remove
            $path = key($fileOperations);
            $info = $fileOperations[$path];
            $code = $info['key'];
            unset($fileOperations[$path]);

            if( !$showAll ) {
              if( $code == 'identical' ) {
                continue;
              }
            }

            // Save to index
            $indexedOperations[$code][$path] = $info;

            // Count
            $currentCount++;

            // Clear
            unset($path);
            unset($info);
            unset($code);
          } while( count($fileOperations) > 0 );
        }
        
        unset($operation);
        unset($fileOperations);

        // Save cache
        //if( !empty($indexedOperations) ) {
          $diffs->offsetSet($package->getKey(), $indexedOperations);
          $indexedCount[$package->getKey()] = $currentCount;
        //}
        unset($indexedOperations);
      }
      $this->_compareCountIndex = $indexedCount;

      // Sort
      $diffs->uksort(array($this, 'compareSort'));
    }
    
    $this->view->diffs = $diffs;
    
    // Get extracted packages
    $oldPackages = array();
    $it = new DirectoryIterator($this->_packageManager->getTemporaryPath(Engine_Package_Manager::PATH_PACKAGES));
    foreach( $it as $child ) {
      if( $it->isDot() || $it->isFile() || !$it->isDir() ) {
        continue;
      }
      $oldPackages[] = basename($child->getPathname());
    }
    
    $this->view->oldPackages = $oldPackages;
  }

  public function conflictAction()
  {
    // Get packages
    $packages = $this->_packageManager->listInstalledPackages();

    $index = array();
    $conflicts = array();

    foreach( $packages as $package ) {
      foreach( $package->getFileStructure() as $file ) {
        if( !isset($index[$file]) ) {
          $index[$file] = $package->getKey();
        } else {
          $conflicts[$file][] = $index[$file];
          $conflicts[$file][] = $package->getKey();
        }
      }
    }
    $this->view->conflicts = $conflicts;
  }

  public function diffAction()
  {
    if( $this->_getParam('hideIdentifiers') ) {
      $this->view->layout()->hideIdentifiers = true;
    }
    
    $left = $this->_getParam('left');
    $right = $this->_getParam('right');
    $file = $this->_getParam('file');
    $packageKey = $this->_getParam('package');
    $type = $this->_getParam('type', 'inline');
    $show = $this->_getParam('show', 0);

    // Left/right mode
    if( $left && $right ) {
      // Calculate base file?
      $file = '';
      for( $il = strlen($left) - 1, $ir = strlen($right) - 1; $il >= 0 && $ir >= 0; $il--, $ir-- ) {
        if( $left[$il] === $right[$ir] ) {
          $file = $left[$il] . $file;
        } else {
          break;
        }
      }
      $file = trim($file, '/\\');
      // Add base path
      if( $left[0] != '/' && $left[0] != '\\' ) {
        $left = APPLICATION_PATH . DIRECTORY_SEPARATOR . $left;
      }
      if( $right[0] != '/' && $right[0] != '\\' ) {
        $right = APPLICATION_PATH . DIRECTORY_SEPARATOR . $right;
      }
      // Make sure it's within the installation
      if( 0 !== strpos($left, APPLICATION_PATH) || 0 !== strpos($right, APPLICATION_PATH) ) {
        throw new Engine_Exception('Not within the installation');
      }
    }

    // File/Package mode
    else if( $file && $packageKey ) {
      $package = $this->_packageManager->listExtractedPackages()->offsetGet($packageKey);
      if( !$package ) {
        throw new Engine_Exception('Package does not exist.');
      }
      $left = $package->getBasePath() . DIRECTORY_SEPARATOR . ltrim($file, '/\\');
      $right = APPLICATION_PATH . DIRECTORY_SEPARATOR . ltrim($file, '/\\');
    }

    // Whoops
    else {
      return;
    }

    // Must have at least left or right
    if( !$left && !$right ) {
      return;
    } else if( !file_exists($left) && !file_exists($right) ) {
      return;
    }

    // Assign
    $this->view->file = $file;
    $this->view->left = $left;
    $this->view->right = $right;

    // Options
    $this->view->type = $type;
    $this->view->showEverything = $show;
    $arr = array();
    parse_str($_SERVER['QUERY_STRING'], $arr);
    $this->view->parts = $arr;

    // Diff
    include_once 'Text/Diff.php';
    include_once 'Text/Diff/Renderer.php';
    include_once 'Text/Diff/Renderer/context.php';
    include_once 'Text/Diff/Renderer/inline.php';
    include_once 'Text/Diff/Renderer/unified.php';
    
    $this->view->textDiff = $textDiff = new Text_Diff(
      'native',//'auto',
      array(
        file_exists($left)  ? file($left,  FILE_IGNORE_NEW_LINES) : array(),
        file_exists($right) ? file($right, FILE_IGNORE_NEW_LINES) : array(),
      )
    );
  }

  public function phpAction()
  {
    ob_start();
    phpinfo();
    $source = ob_get_clean();

    preg_match('~<style.+?>(.+?)</style>.+?(<table.+\/table>)~ims', $source, $matches);
    $css = $matches[1];
    $source = $matches[2];

    $css = preg_replace('/[\r\n](.+?{)/iu', "\n#phpinfo \$1", $css);

    //$regex = '/'.preg_quote('<a href="http://www.php.net/">', '/').'.+?'.preg_quote('</a>', '/').'/ims';
    //$source = preg_replace($regex, '', $source);

    // strip images from phpinfo()
    $regex = '/<img .+?>/ims';
    $source = preg_replace($regex, '', $source);

    $regex = '/'.preg_quote('<h2>PHP License</h2>', '/').'.+$/ims';
    $source = preg_replace($regex, '', $source);

    $source = str_replace("module_Zend Optimizer", "module_Zend_Optimizer", $source);

    $this->view->style = $css;
    $this->view->content = $source;
  }

  public function sanityAction()
  {
    // Get db
    if( Zend_Registry::isRegistered('Zend_Db') && ($db = Zend_Registry::get('Zend_Db')) instanceof Zend_Db_Adapter_Abstract ) {
      Engine_Sanity::setDefaultDbAdapter($db);
    }

    // Get packages
    $packages = $this->_packageManager->listInstalledPackages();

    // Get dependencies
    $this->view->dependencies = $dependencies = $this->_packageManager->depend();

    // Get tests
    $this->view->tests = $tests = new Engine_Sanity();

    $packageIndex = array();
    foreach( $packages as $package ) {
      $packageTests = $package->getTests();

      // No tests
      if( empty($packageTests) ) {
        continue;
      }

      $packageIndex[$package->getKey()] = $package;

      // Make battery
      $battery = new Engine_Sanity(array(
        'name' => $package->getKey(),
      ));
      foreach( $packageTests as $test ) {
        $battery->addTest($test->toArray());
      }

      $tests->addTest($battery);
    }

    $this->view->packageIndex = $packageIndex;

    $tests->run();
  }

  public function logAction()
  {
    $logPath = APPLICATION_PATH . '/temporary/log';

    // Get all existing log files
    $logFiles = array();
    foreach( scandir($logPath) as $file ) {
      if( strtolower(substr($file, -4)) == '.log' ) {
        $logFiles[] = $file;
      }
    }

    // No files
    $this->view->logFiles = $logFiles;
    if( empty($logFiles) ) {
      $this->view->error = 'There are no log files to view.';
      return;
    }

    // Make form
    $labels = array(
      'main.log' => 'Error log',
      'tasks.log' => 'Task scheduler log',
      'translate.log' => 'Language log',
      'video.log' => 'Video encoding log',
    );
    $multiOptions = array_combine($logFiles, $logFiles);
    $labels = array_intersect_key($labels, $multiOptions);
    $multiOptions = array_diff_key($multiOptions, $labels);
    $multiOptions = array_merge($labels, $multiOptions);

    $this->view->formFilter = $formFilter = new Install_Form_Tools_LogFilter();
    $formFilter->getElement('file')->addMultiOptions($multiOptions);
    //$formFilter->getElement('file')->setValue(key($logFiles));

    $values = array_merge(array(
      //'file' => current($logFiles),
      'length' => 50,
    ), $this->_getAllParams());

    if( $formFilter->isValid($values) ) {
      $values = $formFilter->getValues();
    } else {
      $values = array('length' => 50);
    }

    /*
    if( empty($values['file']) ) {
      $values = array(
        'file' => current($logFiles),
        'length' => 50,
      );
    }
     */

    // Make sure param is in existing log files
    $logFile = $values['file'];
    if( empty($logFile) ||
        !in_array($logFile, $logFiles) ||
        !file_exists($logPath . DIRECTORY_SEPARATOR . $logFile) ) {
      $logFile = null;
    }

    // Exit if no valid log file
    if( !$logFile ) {
      $this->view->error = 'Please select a file to view.';
      return;
    }

    // Clear log if requested
    if( $this->getRequest()->isPost() && $this->_getParam('clear', false) ) {
      if( ($fh = fopen($logPath . DIRECTORY_SEPARATOR . $logFile, 'w')) ){
        ftruncate($fh, 0);
        fclose($fh);
      }
      return $this->_helper->redirector->gotoRoute(array());
    }

    // Get log length
    $this->view->logFile = $logFile;
    $this->view->logSize = $logSize = filesize($logPath . DIRECTORY_SEPARATOR . $logFile);
    $this->view->logLength = $logLength = $values['length'];
    $this->view->logOffset = $logOffset = $this->_getParam('offset', $logSize);


    // Tail the file
    $endOffset = 0;
    try {
      $lines = $this->_tail($logPath . DIRECTORY_SEPARATOR . $logFile, $logLength, $logOffset, true, $endOffset);
    } catch( Exception $e ) {
      $this->view->error = $e->getMessage();
      return;
    }

    $this->view->logText = $lines;
    $this->view->logEndOffset = $endOffset;
  }

  public function logDownloadAction()
  {
    $logPath = APPLICATION_PATH . '/temporary/log';

    // Get all existing log files
    $logFiles = array();
    foreach( scandir($logPath) as $file ) {
      if( strtolower(substr($file, -4)) == '.log' ) {
        $logFiles[] = $file;
      }
    }

    $logFile = $this->_getParam('file');
    if( empty($logFile) ||
        !in_array($logFile, $logFiles) ||
        !file_exists($logPath . DIRECTORY_SEPARATOR . $logFile) ) {
      exit();
    }

    // kill output buffering
    while( ob_get_level() > 0 ) {
      ob_end_clean();
    }

    // Send headers
    header('content-disposition: attachment, filename=' . urlencode($logFile));
    header('content-length: ' . filesize($logPath . DIRECTORY_SEPARATOR . $logFile));

    // Open file
    $handle = fopen($logPath . DIRECTORY_SEPARATOR . $logFile, 'r');
    while( '' !== ($str = fread($handle, 1024)) ) {
      echo $str;
    }
    exit();
  }

  protected function _outputFile($file, $exit = true)
  {
    $ext = trim(substr($file, strrpos($file, '.')), '.');
    switch( $ext ) {
      case 'css':
        header('Content-Type: text/css');
        break;
      case 'js':
        header('Content-Type: text/javascript');
        break;
      case 'jpg': case 'jpeg':
        header('Content-Type: image/jpeg');
        break;
      case 'png':
        header('Content-Type: image/png');
        break;
      case 'gif':
        header('Content-Type: image/gif');
        break;
      case 'log':
      case 'txt':
        header('Content-Type: text/plain');
        break;
      default:
        header('Content-Type: text/html');
        break;
    }
    echo file_get_contents($file);
    if( $exit ) {
      exit();
    }
  }

  // Static utility

  public function compareSort($a, $b)
  {
    if( !isset($this->_compareCountIndex[$a]) ||
        !isset($this->_compareCountIndex[$b]) ||
        $this->_compareCountIndex[$a] == $this->_compareCountIndex[$b]) {
      return 0;
    }

    return ( $this->_compareCountIndex[$a] < $this->_compareCountIndex[$b] ? 1 : -1 );
  }

  protected function _tail($file, $length = 10, $offset = 0, $whence = true, &$endOffset = null)
  {
    // Check stuff
    if( !file_exists($file) ) {
      throw new Exception('File does not exist.');
    }
    if( 0 === ($size = filesize($file)) ) {
      throw new Exception('File is empty.');
    }
    if( !($fh = fopen($file, 'r')) ) {
      throw new Exception('Unable to open file.');
    }

    // Process args
    if( abs($offset) > $size ) {
      throw new Exception('Reached end of file.');
    }
    if( !in_array($whence, array(SEEK_SET, SEEK_END)) ) {
      throw new Exception('Unknown whence.');
    }

    // Seek to requested position
    fseek($fh, $offset, SEEK_SET);

    // Read in chunks of 512 bytes
    $position = $offset;
    $break = false;
    $lines = array();
    $chunkSize = 512;
    $buffer = '';

    do {

      // Get next position
      $position += ( $whence ? -$chunkSize : $chunkSize );
      fseek($fh, $position, SEEK_SET);

      // Whoops we ran out of stuff
      if( $position < 0 || $position > $size ) {
        $break = true;
        break;
      }

      // Read a chunk
      $chunk = fread($fh, $chunkSize);
      if( $whence ) {
        $buffer = $chunk . $buffer;
      } else {
        $buffer .= $chunk;
      }

      // Parse chunk into lines
      $bufferLines = preg_split('/\r\n?|\n/', $buffer);

      // Put the last (probably incomplete) one back
      if( $whence ) {
        $buffer = array_shift($bufferLines);
      } else {
        $buffer = array_pop($bufferLines);
      }

      // Add to existing lines
      if( $whence ) {
        $lines = array_merge($bufferLines, $lines);
      } else {
        $lines = array_merge($lines, $bufferLines);
      }

      // Are we done?
      if( count($lines) >= $length ) {
        $break = true;
      }

    } while( !$break );

    $endOffset = $position;

    // Add remaining length in buffer
    $endOffset += strlen($buffer);

    return trim(join(PHP_EOL, $lines), "\n\r");
  }
}
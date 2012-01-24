<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: S3.php 9006 2011-06-21 00:22:28Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Storage_Form_Admin_Service_S3 extends Storage_Form_Admin_Service_Generic
{
  public function init()
  {
    // Element: accessKey
    $this->addElement('Text', 'accessKey', array(
      'label' => 'Access Key',
      'required' => true,
      'allowEmpty' => false,
    ));

    // Element: secretKey
    $this->addElement('Text', 'secretKey', array(
      'label' => 'Secret Key',
      'required' => true,
      'allowEmpty' => false,
    ));

    // Element: region
    $this->addElement('Select', 'region', array(
      'label' => 'Region',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        'us-west-1' => 'United States (West)',
        'us-east-1' => 'United States (East)',
        'eu-west-1' => 'Europe (Ireland)',
        'ap-southeast-1' => 'Asia Pacific (Singapore)',
        'ap-northeast-1' => 'Asia Pacific (Japan)',
      ),
    ));

    // Element: bucket
    $this->addElement('Text', 'bucket', array(
      'label' => 'Bucket',
      'description' => 'If the bucket does not exist, we will attempt to create it.',
      'required' => true,
      'allowEmpty' => false,
    ));

    // Element: path
    $this->addElement('Text', 'path', array(
      'label' => 'Path Prefix',
      'description' => 'This is prepended to the file path. Defaults to "public".',
    ));

    // Element: baseUrl
    $this->addElement('Text', 'baseUrl', array(
      'label' => 'CloudFront Domain',
      'description' => 'If you are using Amazon CloudFront for this bucket, ' .
          'enter the domain here.',
    ));

    parent::init();
  }

  public function isValid($data)
  {
    $valid = parent::isValid($data);

    // Custom valid
    if( $valid ) {
      // Check auth
      try {
        $testService = new Zend_Service_Amazon_S3($data['accessKey'], $data['secretKey'], $data['region']);
        $buckets = $testService->getBuckets();
      } catch( Exception $e ) {
        $this->addError('Please double check your access keys.');
        return false;
      }
      // Check bucket
      try {
        if( !in_array($data['bucket'], $buckets) ) {
          if( !$testService->createBucket($data['bucket'], $data['region']) ) {
            throw new Exception('Could not create or find bucket');
          }
        }
      } catch( Exception $e ) {
        $this->addError('Bucket name is already taken and could not be created.');
        return false;
      }
    }

    return $valid;
  }
}
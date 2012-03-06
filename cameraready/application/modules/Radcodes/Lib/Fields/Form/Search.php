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
 
class Radcodes_Lib_Fields_Form_Search extends Fields_Form_Search
{


  public function generate()
  {
    // get the search structure
    $structure = Engine_Api::_()->getApi('core', 'fields')->getFieldsStructureSearch($this->_fieldType);

    // Start firing away
    $index = 0;
    foreach( $structure as $map )
    {
      $field = $map->getChild();
      $index++;

      // Ignore fields not searchable (even though getFieldsStructureSearch should have skipped it already)
      if( !$field->search )
      {
        continue;
      }

      // Get search key
      $key = null;
      if( !empty( $field->alias) ) {
        $key =  $field->alias;
      } else {
        $key = sprintf('field_%d', $field->field_id);
      }

      // Get params
      $params = $field->getElementParams($this->_fieldType, array('required' => false));

      if( !@is_array($params['options']['attribs']) ) {
        $params['options']['attribs'] = array();
      }

      // Remove some stuff
      unset($params['options']['required']);
      unset($params['options']['allowEmpty']);
      unset($params['options']['validators']);

      // Change decorators
      $params['options']['decorators'] = array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      );
      $params['options']['order'] = $index;

      // Get generic type
      $info = Engine_Api::_()->fields()->getFieldInfo($field->type);
      $genericType = null;
      if( !empty($info['base']) ) {
        $genericType = $info['base'];
      } else {
        $genericType = $field->type;
      }
      $params['type'] = $genericType; // For now

      // Hack birthdate->age
      if( $field->type == 'birthdate' ) {
        $params['type'] = 'Select';
        $params['options']['label'] = 'Age';
        $multiOptions = array('' => ' ');
        for( $i = 13; $i <= 100; $i++ ) {
          $multiOptions[$i] = $i;
        }
        $params['options']['multiOptions'] = $multiOptions;
      }

      // Ignored fields (these are hard-coded)
      if( in_array($field->type, array('profile_type', 'first_name', 'last_name')) ) {
        continue;
      }
      
      // Hacks
      switch( $genericType ) {
        // Ranges
        case 'date':
        case 'int':
        case 'integer':
        case 'float':
          // Use subform
          $subform = new Zend_Form_SubForm(array(
            'description' => $params['options']['label'],
            'order' => $params['options']['order'],
            'decorators' => array(
              'FormElements',
              array('Description', array('placement' => 'PREPEND', 'tag' => 'span')),
              array('HtmlTag', array('tag' => 'li', 'class' => 'browse-range-wrapper'))
            )
          ));
         // Fields_Form_Standard::enableForm($subform);
          Engine_Form::enableForm($subform);
          unset($params['options']['label']);
          unset($params['options']['order']);
          $params['options']['decorators'] = array('ViewHelper');
          $subform->addElement($params['type'], 'min', $params['options']);
          $subform->addElement($params['type'], 'max', $params['options']);
          $this->addSubForm($subform, $key);
          
          break;

        // Select types
        case 'select':
        case 'radio':
        case 'multiselect':
        case 'multi_checkbox':
          // Ignore if there is only one option
          if( count(@$params['options']['multiOptions']) <= 1 ) {
            continue;
          }
          if( count(@$params['options']['multiOptions']) <= 2 && isset($params['options']['multiOptions']['']) ) {
            continue;
          }
          $this->addElement(Engine_Api::_()->fields()->inflectFieldType($params['type']), $key, $params['options']);
          break;

        case 'checkbox':
        	
        	$params['options']['filters'] = array(
		        new Radcodes_Lib_Filter_Null()
		      );
        	$this->addElement($params['type'], $key, $params['options']);
        	
        	break;
          
        // Normal
        default:
          $this->addElement($params['type'], $key, $params['options']);
          break;
      }

      $element = $this->$key;
      //$element = $this->getElement($key);
      $this->_fieldElements[$key] = $element;
    }
  }
}
<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Review
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Review_Form_Filter extends Engine_Form
{
  
  public function init()
  {
    $this->clearDecorators();
    /*
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
		*/
    $this
      ->setAttribs(array(
       // 'id' => 'filter_form',
        'class' => 'review_filter_form',
      ));
    $this->addElement('Select', 'order', array(
      'label' => 'Sort By',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'helpfulnessdesc' => 'Helpfulness',
        'ratingdesc' => 'Rating Descending',
        'ratingasc' => 'Rating Ascending',
        'mosthelpful' => 'Most Helpful',
        'mostcommented' => 'Most Commented',
        'mostliked' => 'Most Liked',
      ),
    ));
    
 
    
    $this->addElement('Select', 'rating', array(
      'label' => 'Rating',
      'multiOptions' => array(
        '' => '',
        '5' => '5 stars',
        '4' => '4 stars',
        '3' => '3 stars',
        '2' => '2 stars',
        '1' => '1 star',
      ),
    )); 
    
    $this->addElement('Select', 'recommend', array(
      'label' => 'Recommend',
      'multiOptions' => array(
        '' => '',
        '1' => 'Yes',
        '0' => 'No',
      ),      
    )); 
    
    $this->addElement('Text', 'keyword', array(
      'label' => 'Keyword',
     // 'attribs' => array('size' => 10),
    )); 
/*
    foreach( $this->getElements() as $fel ) {
      if( $fel instanceof Zend_Form_Element ) {
        
        $fel->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
          ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fel->getName() . '-search-wrapper', 'class' => 'form-search-wrapper'));
        
      }
    }  
  */  

    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      //'ignore' => true,
      'decorators' => array('ViewHelper')
    )); 
    
  }
  
}
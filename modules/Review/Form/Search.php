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
 
 
 
class Review_Form_Search extends Fields_Form_Search // Radcodes_Lib_Fields_Form_Search
{
  protected $_fieldType = 'review';
  
  public function init()
  {
    parent::init();

    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'reviews_browse_filters field_search_criteria',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(),'review_browse',true))
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browsereviews_criteria reviews_browse_filters');
    

    // Add custom elements
    $this->getAdditionalOptionsElement();
    
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;
    
    $this->addElement('Hidden', 'page', array(
      'order' => $i++,
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => $i++,
    ));

    $this->addElement('Text', 'search', array(
      'label' => 'Search Reviews',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
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
      'onchange' => 'this.form.submit();',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
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
      'onchange' => 'this.form.submit();',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
    /*
    $seperator1 = $this->getElement('separator1');
    $this->removeElement('separator1');
    $seperator1->setOrder($i++);
    $this->addElement($seperator1);
		*/
    if (count($this->_fieldElements)) {
      $this->_order['separator1'] = $i++;
    }
    else {
      $this->removeElement('separator1');
    }
    
    $j = 10000000;
    
    $this->addElement('Checkbox', 'recommend', array(
      'label' => 'Only Reviews With Recommend',
      'order' => $j++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));    
    
    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'order' => $j++,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
  }
}
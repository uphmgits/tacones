<?php
/**
 * 
 *
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 * @version    $Id: WidgetController.php
 * 
 */

/**
 * @category   Application_Extensions
 * @package    Marketplace
 * @copyright  Copyright 2010 
 * * 
 */


class Marketplace_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'marketplace';

  public function init()
  {
    //parent::init();

    $this->loadDefaultDecorators();

    $this->getDecorator('HtmlTag')->setOption('class', 'browsemarketplaces_criteria marketplaces_browse_filters')->setOption('id', 'filter_form');

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box marketplaces_browse_filters',
      ))
      ->setAction($_SERVER['REQUEST_URI'])
      ;

    // Generate
    $this->generate();
	$this->removeElement('separator1');
	$this->removeElement('separator2');

    foreach( $this->getFieldElements() as $fel ) {
      if( $fel instanceof Zend_Form_Element ) {
        $fel->clearDecorators();
        $fel->addDecorator('ViewHelper');
        Engine_Form::addDefaultDecorators($fel);
      } else if( $fel instanceof Zend_Form_SubForm ) {
        $fel->clearDecorators();
        $fel->setDescription('<label>' . $fel->getDescription() . '</label>');
        $fel->addDecorator('FormElements')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fel->getName() . '-element', 'class' => 'form-element'))
            ->addDecorator('Description', array('tag' => 'div', 'class' => 'form-label', 'placement' => 'PREPEND', 'escape' => false))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'id'  => $fel->getName() . '-wrapper', 'class' => 'form-wrapper browse-range-wrapper'));
      }
    }

    // Add custom elements
    $this->getAdditionalOptionsElement();
  }

  public function getAdditionalOptionsElement()
  {
    $i = -1000;

    $this->addElement('Hidden', 'page', array(
      'order' => 9999,
    ));

    $this->addElement('Hidden', 'tag', array(
      'order' => 9999,
    ));

    $this->addElement('Hidden', 'start_date', array(
      'order' => 9999,
    ));

    $this->addElement('Hidden', 'end_date', array(
      'order' => 9999,
    ));

    $this->addElement('Text', 'search', array(
      'label' => 'Search Marketplace',
      'order' => $i--,
    ));
           $this->addElement('Text', 'price_end', array(
      'label' => 'Price end:',
      'order' => $i--,
      'size' => 10,
    ));

        $this->addElement('Text', 'price_start', array(
      'label' => 'Price start:',
      'order' => $i--,
            'size' => 10,
    ));
    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
      ),
      'onchange' => 'this.form.submit();',
      'order' => $i--,
    ));

    $this->addElement('Select', 'show', array(
      'label' => 'Show',
      'multiOptions' => array(
        '1' => 'Everyone\'s Posts',
        '2' => 'Only My Friends\' Posts',
      ),
      'onchange' => 'this.form.submit();',
      'order' => $i--,
    ));

    $this->addElement('Select', 'closed', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => 'All Listings',
        '0' => 'Only Open Listings',
        '1' => 'Only Closed Listings',
      ),
      'onchange' => 'this.form.submit();',
      'order' => $i--,
    ));

    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => array(
        '0' => 'All Categories',
      ),
      'onchange' => 'this.form.submit();',
      'order' => $i--,
    ));

    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'order' => 1000,
      'onclick' => 'this.form.submit();',
    ));
  }
}
<?php
class User_Widget_ProfileFieldsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
	  $subject_id = $subject->getIdentity();
	  //
	
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    $this->view->level_id = $viewer->level_id;

    if( $viewer->level_id <= 2 )  {
        $notBanned = ( $subject->enabled and $subject->approved );
        $request = Zend_Controller_Front::getInstance()->getRequest()->getPost();

        if( isset($request['ban_option']) )  {
            if( $notBanned ) {
                $subject->enabled = 0;
                $subject->approved = 0;
                $notBanned = false;
            } else {
                $subject->enabled = 1;
                $subject->approved = 1;
                $notBanned = true;
            }
            $subject->save();
        }
        $this->view->notBanned = $notBanned;
    }

    $this->view->aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($subject); 
    $this->view->average_rating = Engine_Api::_()->review()->getUserAverageRating($subject);
    $this->view->total_review = Engine_Api::_()->review()->getUserReviewCount($subject);
	
///////////////////////////////////////////////////////////////////////
//Count Comments
	$commentsTable = Engine_Api::_()->getDbtable('comments', 'activity');
	$select = $commentsTable->select()
	->where('poster_id =?', $subject_id);
	
	$countComents = $commentsTable->fetchAll($select);
	$this->view->countComents =  count($countComents);
///////////////////////////////////////////////////////////////////////
//Count Followers
    $selectMembership = Engine_Api::_()->getDbtable('membership', 'user') ;
    $select = $selectMembership->select()->where('resource_id=?',$subject_id);
	$followers = $selectMembership->fetchAll($select);

    $this->view->countFollowers =  count($followers);
///////////////////////////////////////////////////////////////////////
//Count Following
    $selectMembership2 = Engine_Api::_()->getDbtable('membership', 'user') ;
    $select2 = $selectMembership2->select()->where('user_id=?',$subject_id);
	$followings = $selectMembership2->fetchAll($select2);

	$this->view->countFollowing =  count($followings);
////////////////////////////////////////////////////////////////////////		

    // Load fields view helpers
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // Values
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
    if( count($fieldStructure) <= 1 ) { // @todo figure out right logic
      return $this->setNoRender();
    }
    return;

    $valuesStructure = array();
    $valueCount = 0;
    foreach( $fieldStructure as $index => $field )
    {
      $value = $field->getValue($subject);
      if( !$field->display )
      {
        continue;
      }

      if( $field->isHeading() )
      {
        $valuesStructure[] = array(
          'alias' => null,
          'label' => $field->label,
          'value' => $field->label,
          'heading' => true,
          'type' => $field->type,
        );
      }

      else if( $value && !empty($value->value) )
      {
        $valueCount++;

        $label = Engine_Api::_()->fields()
                 ->getFieldsOptions($subject)
                 ->getRowMatching('option_id', $value->value);
        $label = $label
                 ? $label->label
                 : $value->value;

        $valuesStructure[] = array(
          'alias' => $field->alias,
          'label' => $field->label,
          'value' => $label,
          'heading' => false,
          'type' => $field->type,
        );
      }
    }
    $this->view->user   = $subject;
    $this->view->fields = $valuesStructure;
    $this->view->valueCount = $valueCount;


    // Do not render if nothing to show
    if( $valueCount <= 0 ) {
     // return $this->setNoRender();
    }		
  }
}

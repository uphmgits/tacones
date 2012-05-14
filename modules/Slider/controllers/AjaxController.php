<?php

class Slider_AjaxController extends Core_Controller_Action_Standard
{
  public function getslideAction() {
      $slide_id = (int) $this->_getParam('slide_id', 0);
      try {
        if (!$slide_id) throw new Engine_Exception('Incorrect Slide Id.');
        $slide = Engine_Api::_()->getItem('slider_slide', $slide_id);
        if ($slide == null) throw new Engine_Exception('Slide is NULL.');
        $data = $slide->toArray();
        $data['description'] = str_replace("\r\n", '<br/>',htmlspecialchars_decode($data['description']));
        $data['result'] = 'success';
      }
      catch (Exception $e){
          $data = array('result' => 'error',
                        'message' => $e->getMessage() );
      }
    return $this->_helper->json($data);
  }
}
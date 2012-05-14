<?php

class Slider_Widget_ShowSlidesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $table = Engine_Api::_()->getItemTable('slider_slide');
    $select = $table->select()
                    ->order('order ASC')
                    ->where("enable_slide = '1'");

    $paginator = $table->fetchAll($select);

    if( $paginator->count() <= 0 ) {
      return $this->setNoRender();
    }    
    $this->view->paginator = $paginator;
    $slides_str = '';
    $slides_links = '';
    $i = 1;
    foreach ($paginator as $slide) {
        $slides_str .= $slide->slide_id . ',';
        $link_on = ($i != 1) ? 'off' : 'on';
        $slides_links .= "<a class=\"slider_$link_on\" id=\"slider_link_$i\" href=\"javascript:void(0);\" onclick=\"javascript: myslider.change_img($i);\"></a>";
		//$slides_links .= "<a class=\"slider_$link_on\" id=\"slider_link_$i\" href=\"javascript:void(0);\" onclick=\"javascript: myslider.change_img($i);\">$i</a>";
        $i++;
    }
    $this->view->slides_links = $slides_links;
    $this->view->slides_str = trim($slides_str, ',');
    $this->view->link_display = (trim($paginator[0]->link)) ? 'block' : 'none';
  }
}
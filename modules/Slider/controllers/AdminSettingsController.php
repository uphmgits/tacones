<?php
class Slider_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                           ->getNavigation('slider_admin_main', array(), 'slider_admin_main_settings');

    $this->view->form = $form = new Slider_Form_Admin_Global();

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
      foreach ($values as $key => $value){
        $setting_tmp->setSetting($key, $value);
      }
    }
  }
  public function slidesAction() {
      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                                                             ->getNavigation('slider_admin_main', array(), 'slider_admin_main_slides');
      $this->view->slides = Engine_Api::_()->getDbtable('slides', 'slider')->fetchAll();
  }

  public function addSlideAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Slider_Form_Admin_Slide();
    if ($this->_hasParam('slide_id')) {
        $slideRow = Engine_Api::_()->getItem('slider_slide', $this->_getParam('slide_id'));
        $form->populate($slideRow->toArray())
             ->setTitle('Edit Slide');
        $form->submit->setLabel('Save');
        $form->image->setRequired(false)
                    ->setAllowEmpty(true);
        $edit = true;
    }
    else {
        $form->setTitle('Add Slide');
        $edit = false;
    }
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) && (($img_isUploaded = $form->image->isUploaded()) or $edit)) {
      $slideTable = Engine_Api::_()->getItemTable('slider_slide');
        // Begin database transaction
      $db = $slideTable->getAdapter();
      $db->beginTransaction();

      try
      {

        $values = $form->getValues();
        if (!$edit) $slideRow = $slideTable->createRow();
        $slideRow->setFromArray($values);
        $slideRow->save();
        $db->commit();
        if ($img_isUploaded) {
            $slide_id = $slideRow->getIdentity();
            $destName = APPLICATION_PATH . '/application/modules/Slider/externals/images/slides/slide_' . $slide_id . '.jpg';
            $settings = Engine_Api::_()->getApi('settings', 'core');
            $slide_width = $settings->getSetting('slide_width', 640);
            $slide_height = $settings->getSetting('slide_height', 480);

            file_exists($destName) && unlink($destName);
            $image = new Slider_Library_Gd();
            $filename = $form->image->getFileName();
            $image->open($filename);
            if ($image->width == $slide_width and $image->height == $slide_height) {
                $image->destroy();
                rename($filename, $destName);
            }
            else {
                $image->set_quality($settings->getSetting('quality', 100));
                $ratio_orig = $image->width/$image->height;
                if ($slide_width/$slide_height < $ratio_orig) {
                    $slide_height_res = $slide_height;
                    $slide_width_res = $slide_height*$ratio_orig;
                } else {
                    $slide_width_res = $slide_width;
                    $slide_height_res = $slide_width_res/$ratio_orig;
                }
                $image->resize($slide_width_res, $slide_height_res)
                      ->crop(0,0, $slide_width, $slide_height)
                      ->write($destName)
                      ->destroy();
                unlink($filename);
            }
        }
        $this->_forward('success', 'utility', 'core', array(
          'parentRefresh'=> 1500,
          'messages' => ($edit) ? array('Slide successfully edited.') : array('Slide successfully added.')
      ));
      }

      catch( Exception $e )
      {
        unlink($form->image->getFileName());
        $db->rollBack();
        throw $e;
      }
      
    }
    else $this->renderScript('admin-settings/form.tpl');
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->delete_title = 'Delete Slide?';
    $this->view->delete_description = 'Are you sure that you want to delete this slide? It will not be recoverable after being deleted.';
    $id = $this->_getParam('slide_id');
    $this->view->slide_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $slide = Engine_Api::_()->getItem('slider_slide', $id);
        $slide->delete();

        $db->commit();
        unlink(APPLICATION_PATH . '/application/modules/Slider/externals/images/slides/slide_' . $id . '.jpg');
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('etc/delete.tpl');
  }

  public function sortAction() {

    $this->_helper->layout->setLayout('admin-simple');
    if( $this->getRequest()->isPost() and $this->getRequest()->getPost('sort_task') == 'save') {
        $sort_data = explode(',', $this->getRequest()->getPost('sort_slides'));
        try {
            Engine_Api::_()->getDbtable('slides', 'slider')->save_order($sort_data);
            $this->_forward('success', 'utility', 'core', array(
                                                                  'parentRefresh'=> 1,
                                                                  'smoothboxClose' => 1,
                                                                ));
        }
        catch (Exception $e) {
           $this->_forward('success', 'utility', 'core', array(
                                                                  'parentRefresh'=> 1500,
                                                                  'messages' => array($e->getmessage())
                                                              ));
        }
   /* if (is_array($sort_data) and count($sort_data) > 0) {
        $i = 1;
        foreach ($sort_data as $slide_id) {
            preg_match ("/slide_(\d+)/", $slide_id, $id);
            $database->database_query("update `wh_slider` set `order` = '$i' where `id` = '{$id[1]}'");
            $i++;
        }
        $message = 'Changes have been saved.';
    }
    else $error_message = 'Incorrect data format. Please try again.';*/
    }
    else {
        $this->view->slides = Engine_Api::_()->getDbtable('slides', 'slider')->fetchAll();
        $this->renderScript('admin-settings/sort.tpl');
    }
  }
}
?>

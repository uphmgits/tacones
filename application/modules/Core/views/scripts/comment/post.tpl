<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: list.tpl 8375 2011-02-02 02:09:25Z john $
 * @author     John
 */
?>

<?php if( isset($this->form) ) : ?>
  <div class='post-comment-container'>
    
    <script type="text/javascript">
      en4.core.runonce.add(function(){
        $($('comment-form-<?=$this->subjectId?>').body).autogrow();
        en4.core.comments.attachCreateComment($('comment-form-<?=$this->subjectId?>'));
      });
    </script>

    <?=$this->form->setAttribs(array('id' => "comment-form-{$this->subjectId}", 
                                     'style' => 'display:none;', 
                                     'onsubmit' => "if( $('comment-lips-{$this->subjectId}') ) 
                                                        $('comment-lips-{$this->subjectId}').set('html', parseInt( $('comment-lips-{$this->subjectId}').get('html') ) + 1);
                                                        $('comment-form-{$this->subjectId}').style.display = 'none';"
                                    ))->render()?>
  </div>
<?php endif; ?>

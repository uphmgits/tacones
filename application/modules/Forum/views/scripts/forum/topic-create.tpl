<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: topic-create.tpl 8918 2011-05-05 20:22:53Z shaun $
 * @author     John
 */
?>

<script type="text/javascript">
function showUploader()
{
  $('photo').style.display = 'block';
  $('photo-label').style.display = 'none';
}
</script>

<h2>
<?php echo $this->htmlLink(array('route'=>'forum_general'), $this->translate("Forums"));?>
  &#187; <?php echo $this->htmlLink(array('route'=>'forum_forum', 'forum_id'=>$this->forum->getIdentity()), $this->translate($this->forum->getTitle()));?>
  &#187 <?php echo $this->translate('Post Topic');?>
</h2>

<?php echo $this->form->render($this) ?>
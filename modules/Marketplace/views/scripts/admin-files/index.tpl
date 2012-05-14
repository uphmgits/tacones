<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7305 2010-09-07 06:49:55Z john $
 * @author     John
 */
?>

<?php
  $baseUrl = $this->baseUrl();
  $this->headTranslate(array(
    'Overall Progress ({total})', 'File Progress', 'Uploading "{name}"',
    'Upload: {bytesLoaded} with {rate}, {timeRemaining} remaining.', '{name}',
    'Remove', 'Click to remove this entry.', 'Upload failed',
    '{name} already added.',
    '{name} ({size}) is too small, the minimal file size is {fileSizeMin}.',
    '{name} ({size}) is too big, the maximal file size is {fileSizeMax}.',
    '{name} could not be added, amount of {fileListMax} files exceeded.',
    '{name} ({size}) is too big, overall filesize of {fileListSizeMax} exceeded.',
    'Server returned HTTP-Status <code>#{code}</code>',
    'Security error occurred ({text})',
    'Error caused a send or load operation to fail ({text})',
  ));
?>

<h2><?php echo $this->translate("File Browser") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate('You can download files'); ?>
</p>

<?php if( count($this->subNavigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
    ?>
  </div>
<?php endif; ?>

<form method='post' action=''>
  <select name='file_filter_list'>
    <option value='0' <?php if( $this->file_filter_list != 1 ) echo "selected"?>><?=$this->translate("Sold Files")?></option>
    <option value='1' <?php if( $this->file_filter_list == 1 ) echo "selected"?>><?=$this->translate("Return Files")?></option>
  </select>  
  <button type='submit' name="submit_button" value="file_filter"><?php echo $this->translate("Filter") ?></button>
</form>

<br />

<?php if(count($this->contents) > 0): $i = 0; ?>
  <div class="admin_files_wrapper">

    <iframe src="about:blank" style="display:none" name="downloadframe"></iframe>
    
    <div class="admin_files_pages">
      <?php $pageInfo = $this->paginator->getPages(); ?>
      <?php echo $this->translate(array('Showing %s-%s of %s file.', 'Showing %s-%s of %s files.', $pageInfo->totalItemCount),
          $pageInfo->firstItemNumber, $pageInfo->lastItemNumber, $pageInfo->totalItemCount) ?>
      <span>
        <?php if( !empty($pageInfo->previous) ): ?>
          <?php echo $this->htmlLink(array('reset' => false, 'APPEND' => '?path=' . urlencode($this->relPath) . '&page=' . $pageInfo->previous), 'Previous Page') ?>
        <?php endif; ?>
        <?php if( !empty($pageInfo->previous) && !empty($pageInfo->next) ): ?>
           |
        <?php endif; ?>
        <?php if( !empty($pageInfo->next) ): ?>
          <?php echo $this->htmlLink(array('reset' => false, 'APPEND' => '?path=' . urlencode($this->relPath) . '&page=' . $pageInfo->next), 'Next Page') ?>
        <?php endif; ?>
      </span>
    </div>

    <form action="<?php echo $this->url(array('action' => 'delete')) ?>?path=<?php echo $this->relPath ?>" method="post">
      <ul class="admin_files">
        <?php foreach( $this->paginator as $content ): $i++; $id = 'admin_file_' . $i; $contentKey = $content['rel']; ?>
          <li class="admin_file admin_file_type_<?php echo $content['type'] ?>" id="<?php echo $id ?>">
            <div class="admin_file_checkbox">
              <?php echo $this->formCheckbox('actions[]', $content['rel']) ?>
            </div>
            <div class="admin_file_options">
              | <a href="<?php echo $this->url(array('action' => 'rename', 'index' => $i)) ?>?path=<?php echo urlencode($content['rel']) ?>&file_filter_list=<?=$this->file_filter_list?>" class="smoothbox"><?php echo $this->translate('rename') ?></a>
              | <a href="<?php echo $this->url(array('action' => 'delete', 'index' => $i)) ?>?path=<?php echo urlencode($content['rel']) ?>&file_filter_list=<?=$this->file_filter_list?>" class="smoothbox"><?php echo $this->translate('delete') ?></a>
              <?php if( $content['is_file'] ): ?>
                | <a href="<?php echo $this->url(array('action' => 'download')) ?><?php echo !empty($content['rel']) ? '?path=' . urlencode($content['rel']) : '' ?>&file_filter_list=<?=$this->file_filter_list?>" target="downloadframe"><?php echo $this->translate('download') ?></a>
              <?php else: ?>
                | <a href="<?php echo $this->url(array('action' => 'index')) ?><?php echo !empty($content['rel']) ? '?path=' . urlencode($content['rel']) : '' ?>"><?php echo $this->translate('open') ?></a>
              <?php endif; ?>
            </div>
            <div class="admin_file_name" title="<?php echo $contentKey ?>">
              <?php if( $content['name'] == '..' ): ?>
                <?php echo $this->translate('(up)') ?>
              <?php else: ?>
                <?php echo $content['name'] ?>
              <?php endif; ?>
            </div>
            <div class="admin_file_preview admin_file_preview_<?php echo $content['type'] ?>" style="display:none">
              <?php if( $content['is_image'] ): ?>
                <?php echo $this->htmlImage($this->baseUrl() . '/public/admin/' . $content['rel'], $content['name']) ?>
              <?php elseif( $content['is_markup'] ): ?>
                <iframe style="background-color: #fff;" src="<?php echo $this->url(array('action' => 'preview')) ?>?path=<?php echo urlencode($content['rel']) ?>"></iframe>
              <?php elseif( $content['is_text'] ): ?>
                <div>
                  <?php echo nl2br($this->escape(file_get_contents($content['path']))) ?>
                </div>
              <?php endif; ?>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="admin_files_submit">
        <button type="submit"><?php echo $this->translate('Delete Selected') ?></button>
      </div>
      <?php echo $this->formHidden('path', $this->relPath) ?>
    </form>
  </div>
<?php endif; ?>

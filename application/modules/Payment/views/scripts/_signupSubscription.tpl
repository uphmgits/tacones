<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _signupSubscription.tpl 9191 2011-08-20 00:00:03Z john $
 * @author     John Boehr <j@webligo.com>
 */
?>

<?php //echo $this->form->render($this) ?>

<form method="post" action="<?php echo $this->escape($this->form->getAction()) ?>"
      class="global_form payment_form_signup" enctype="application/x-www-form-urlencoded">
  <div>
    <div>
      <h3>
        <?php echo $this->translate($this->form->getTitle()) ?>
      </h3>
      <p class="form-description">
        <?php echo $this->translate($this->form->getDescription()) ?>
      </p>
      <?php if( $this->form->isErrors() ): ?>
        <ul class="form-errors"><li>Choose Plan:<ul class="errors"><li>
          <?php echo $this->translate('Please provide a valid answer for this field.') ?>
        </li></ul></li></ul>
      <?php endif ?>
      <div class="form-elements">
        <div id="package_id-wrapper" class="form-wrapper">
          <div id="package_id-label" class="form-label">
            <label for="package_id" class="required">Choose Plan:</label>
          </div>
          <div id="package_id-element" class="form-element">
            <ul class="form-options-wrapper">
            <?php foreach( $this->form->getPackages() as $package ):
              $id = $package->package_id;
              $attribs = array('id' => 'package-' . $id, 'class' => 'package-select');
              if( $id == $this->currentPackage->package_id ) {
                continue;
                //$attribs['disabled'] = 'disabled';
              }
              $count++;
              ?>
              <li>
                <input type="radio" name="package_id" id="package_id-<?php echo $id ?>" value="1" />
                <label for="package_id-<?php echo $id ?>" class="package-label">
                  <?php echo $this->translate($package->title) ?>
                  <?php echo $this->translate('(%1$s)', $package->getPackageDescription()) ?>
                </label>
                <p class="package-description">
                  <?php echo $this->translate($package->description) ?>
                </p>
              </li>
            <?php endforeach; ?>
            </ul>
          </div>
        </div>
        
        
        <?php /*
        <?php $count = 0; ?>
        <?php foreach( $this->form->getPackages() as $package ):
          $id = $package->package_id;
          $attribs = array('id' => 'package-' . $id, 'class' => 'package-select');
          if( $id == $this->currentPackage->package_id ) {
            continue;
            //$attribs['disabled'] = 'disabled';
          }
          $count++;
          ?>
          <div id="package-<?php echo $id ?>-wrapper" class="form-wrapper">
            <div id="package-<?php echo $id ?>-element" class="form-element">
              <?php echo $this->formSingleRadio('package_id', $package->package_id, $attribs) ?>
              <div class="package-container">
                <label class="package-label" for="package-<?php echo $id ?>">
                  <?php echo $this->translate($package->title) ?>
                  <?php echo $this->translate('(%1$s)', $package->getPackageDescription()) ?>
                </label>
                <p class="package-description">
                  <?php echo $this->translate($package->description) ?>
                </p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
         * 
         */ ?>
        
        <div id="submit-wrapper" class="form-wrapper">
          <div id="submit-label" class="form-label">&nbsp;</div>
          <div id="submit-element" class="form-element">
            <button name="submit" id="submit" type="submit">Continue</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</form>
  

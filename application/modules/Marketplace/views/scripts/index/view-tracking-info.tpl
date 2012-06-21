<div style="padding:20px">
  <?php if( $this->fedex_xml ) : ?>
  <?php elseif( $this->ups_xml ) : ?>
          <?php echo "<pre>"; print_r($this->ups_xml); echo "</pre>"; ?>
  <?php endif;?>
</div>

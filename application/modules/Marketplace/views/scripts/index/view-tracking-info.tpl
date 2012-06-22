<div style="padding:20px">
  <?php if( $this->fedex_xml and $this->fedex_xml instanceof SimpleXMLElement ) : ?>
      <?php echo "<pre>"; print_r($this->fedex_xml); echo "</pre>"; ?>
  <?php elseif( $this->ups_xml and $this->ups_xml instanceof SimpleXMLElement ) : ?>
      <?php echo "<pre>"; print_r($this->ups_xml); echo "</pre>"; ?>
  <?php endif;?>
</div>

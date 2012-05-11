
<script type="text/javascript">
  var searchPolls = function() {
    $('filter_form').submit();
  }
</script>

<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif ?>

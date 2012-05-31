<div class="top-banner-container">
  <div></div>
  <?=$this->htmlLink( $this->prevUrl,
                      "<img src='{$this->baseUrl()}/public/header-imgs/{$this->prevBanner}' /><div></div>", 
                      array('class' => 'top-banner-menu top-banner-prev'))?>

  <?=$this->htmlLink( $this->currentUrl, "<img src='{$this->baseUrl()}/public/header-imgs/{$this->currentBanner}' />")?>

  <?=$this->htmlLink( $this->nextUrl,
                      "<img src='{$this->baseUrl()}/public/header-imgs/{$this->nextBanner}' /><div></div>", 
                      array('class' => 'top-banner-menu top-banner-next'))?>
  <div></div>
</div>

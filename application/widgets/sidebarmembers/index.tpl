<div id="sidebar_members_wrapper">
  <div class="sidebar_members quicklinks">
    <ul class="sidebar_members_list">
      <li><?=$this->htmlLink( array('route' => 'user_general', 'is_online' => 1), $this->translate('Online Now') )?></li>
      <li><?=$this->htmlLink( array('route' => 'user_general', 'newest' => 1), $this->translate('New Members') )?></li>
      <li><?=$this->htmlLink( "javascript:void(0)", $this->translate('V.I.P Members') )?></li>
      <li><?=$this->htmlLink( array('route' => 'user_general', 'most_followed' => 1), $this->translate('Most Followed') )?></li>
      <li><?=$this->htmlLink( $this->baseUrl() . "/pages/announcement", $this->translate('Announcements') )?></li>
      <li><?=$this->htmlLink( array('route' => 'forum_general'), $this->translate('Forum') )?></li>
    </ul>
  </div>
</div>

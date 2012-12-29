<script type="tmpl/underscore" id="photo-meta">
  <div class="photo-meta">
    <h4 class="title"><a href="/p/<%= id %>"><%= title || filenameOriginal %><a/></h4>
    <ul class="info">
      <li><a href="#"><i class="tb-icon-small-comment tb-icon-dark"></i> <span class="number">24</span></a></li>
      <li><a href="#"><i class="tb-icon-small-heart tb-icon-dark"></i> <span class="number">24</span></a></li>
      <li><a href="#"><i class="tb-icon-small-maximize tb-icon-dark"></i> <span class="number">Share</span></a></li>
      <li><a href="#" title="Toggle the privacy setting"><i class="tb-icon-small-<%= permission == 0 ? 'locked' : 'unlocked' %> tb-icon-dark permission" data-id="<%= id %>"></i></a></li>
      <li><a href="#" title="Set as your profile photo"><i class="tb-icon-small-profile tb-icon-dark profile" data-id="<%= id %>"></i></a></li>
    </ul>
  </div>
</script>
<script type="tmpl/underscore" id="profile-photo-meta">
  <img class="profile-pic profile-photo" src="<%= photoUrl %>" />
</script>
<script type="tmpl/underscore" id="profile-photo-header-meta">
  <a href="#" class="profile-link" data-toggle="dropdown"><img class="profile-pic profile-photo" src="<%= photoUrl %>" /></a>
  <ul class="dropdown-menu" role="menu">
    <li><a href="#">Child Item 1</a></li>
    <li><a href="#">Child Item 2</a></li>
  </ul>
</script>
<!--
  <img class="profile-pic profile-photo" src="<%= photoUrl %>" />
-->
<script type="tmpl/underscore" id="profile-name-meta">
  <span class="name"><%= name %></span>
</script>


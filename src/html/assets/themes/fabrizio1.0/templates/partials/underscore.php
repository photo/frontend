<?php $isAdmin = $this->user->isAdmin(); ?>
<script type="tmpl/underscore" id="photo-meta">
  <div class="photo-meta">
    <?php if($isAdmin) { ?>
      <h4 class="title edit"><a href="/p/<%= id %>" title="Update the title"><%= title || filenameOriginal %></a></h4>
      <ul class="info">
        <li><a href="#"><i class="icon-comments tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#"><i class="icon-heart tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#"><i class="icon-share-alt tb-icon-dark"></i> <span class="number">Share</span></li>
        <li><a href="#" title="Toggle the privacy setting"><i class="icon-<%= permission == 0 ? 'lock' : 'unlock' %> tb-icon-dark permission edit" data-id="<%= id %>"></i></li>
        <li><a href="#" title="Set as your profile photo"><i class="icon-user tb-icon-dark profile edit" data-id="<%= id %>"></i></li>
        <li><a href="#" title="Select for batch editing"><i class="icon-pushpin tb-icon-dark pin edit" data-id="<%= id %>"></i></li>
      </ul>
    <?php } else { ?>
      <h4 class="title"><%= title || filenameOriginal %></h4>
      <ul class="info">
        <li><a href="#"><i class="icon-comments tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#"><i class="icon-heart tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#"><i class="icon-share-alt tb-icon-dark"></i> <span class="number">Share</span></li>
      </ul>
    <?php } ?>
  </div>
</script>
<script type="tmpl/underscore" id="profile-photo-meta">
  <img class="profile-pic profile-photo" src="<%= photoUrl %>" />
</script>
<!--
  <img class="profile-pic profile-photo" src="<%= photoUrl %>" />
-->

<script type="tmpl/underscore" id="user-badge-meta">
  <h4 class="profile-name-meta username"><span class="name <?php if($isAdmin) { ?> edit <?php } ?>"><%= name %></span></h4>
  <div class="tray-wrap">
    <span class="avatar"><img class="avatar profile-pic profile-photo" src="<%= photoUrl %>" /></span>
    <div class="tray">
      <div class="details">
        <ul>
          <li>
            <a href="/photos/list">
              <i class="icon-picture"></i>
              <span class="number"><%= counts.photos %></span>
              <span class="title">photos</span>
            </a>
          </li>
          <li>
            <a href="/albums/list">
              <i class="icon-th-large"></i>
              <span class="number"><%= counts.albums %></span>
              <span class="title">albums</span>
            </a>
          </li>
          <li>
            <a href="/tags/list">
              <i class="icon-tags"></i>
              <span class="number"><%= counts.tags %></span>
              <span class="title">tags</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</script>

<script type="tmpl/underscore" id="profile-name-meta">
  <span class="name <?php if($isAdmin) { ?> edit <?php } ?>" <?php if($isAdmin) { ?>title="Change display name"<?php } ?>><%= name %></span>
</script>

<script type="tmpl/underscore" id="op-lightbox">
  <div class="op-lightbox">
    <div class="header">
      <div class="container">
        <div class="logo"></div>
        <a class="detail-link" href="#">Full Details</a>
      </div>
    </div>
    <div class="bd">
      <div class="photo">
        <div class="nav">
          <a href="#" class="prev">&lt;</a>
          <a href="#" class="next">&gt;</a>
        </div>
      </div>
      <div class="details">
        <div class="toggle">
          <span class="special-key">D</span>
          <span class="hide-details">Hide Details</span>
          <span class="show-details">Show Details</span>
        </div>
        <div class="container">
        
        </div>
      </div>
    </div>
  </div>
</script>
<script type="tmpl/underscore" id="op-lightbox-details">
  <div class="detail-block">
    <div class="title">
      <span class="text"><%= title || filenameOriginal %></span>
      <span class="actions">
        <a href="#"><i class="tb-icon-heart tb-icon-dark"></i></a>
        <a href="#"><i class="tb-icon-comment tb-icon-dark"></i></a>
        <a href="#"><i class="tb-icon-maximize tb-icon-dark"></i></a>
      </span>
    </div>
    <div class="description">
      <span class="text"><%= description %></span>
    </div>
  </div>
  <div class="action-block">
  
  </div>
</script>

<script type="tmpl/underscore" id="album-meta">
  <li>
    <a href="/photos/album-<%= id %>/list">
      <img src="<%= cover.path200x200xCR %>">
    </a>
    <?php if($isAdmin) { ?>
      <h5 class=" name edit"><%= name %></h5>
    <?php } else { ?>
      <h5 class="name"><%= name %></h5>
    <?php } ?>
  </li>
</script>

<script type="tmpl/underscore" id="photo-detail-meta">
  <img src="<%= path870x550 %>">
  <br>
  <?php if($isAdmin) { ?>
    <a href="#" class="title edit"><%= title %></a>
    <br>
    <a href="#" class="description edit"><%= description %></a>
  <?php } else { ?>
   <%= title %>
   <br>
   <%= description %>
  <?php } ?>

   <hr>

  <% if (!_.isNull(previous)) { %>
    <% if (previous[0]) { %>
      <a class="paginate" data-id="<%= previous[0].id %>">
        <img src="<%= previous[0].path90x90xCR %>">
      </a>
    <% } %>
    <% if (previous[1]) { %>
      <a class="paginate" data-id="<%= previous[1].id %>">
        <img src="<%= previous[1].path90x90xCR %>">
      </a>
    <% } %>
  <% } %>
  --
  <% if (!_.isNull(next)) { %>
    <% if (next[0]) { %>
      <a class="paginate" data-id="<%= next[0].id %>">
        <img src="<%= next[0].path90x90xCR %>">
      </a>
    <% } %>
    <% if (next[1]) { %>
      <a class="paginate" data-id="<%= next[1].id %>">
        <img src="<%= next[1].path90x90xCR %>">
      </a>
    <% } %>
  <% } %>
  <ul>
    <% for(var tag in tags) { %>
      <li><%= tags[tag] %></li>
    <% } %>
  </ul>
</script>

<script type="tmpl/underscore" id="batch-meta">
  <?php if($isAdmin) { ?>
    <a data-toggle="dropdown" href="#"><i class="tb-icon-light icon-cogs"></i> Batch Edit <% if (count > 0) { %><span class="badge badge-important"><%= count %></span><% } %></a>
    <ul class="dropdown-menu">
      <% if (count > 0) { %>
        <li><a>Batch edit your photos</a></li>
        <li class="divider"></li>
        <li><a href="#" class="tags">&nbsp;&middot;&nbsp;Add or Remove Tags</a></li>
        <li><a href="#">&nbsp;&middot;&nbsp;Organize Into Albums</a></li>
        <li><a href="#">&nbsp;&middot;&nbsp;Manage Privacy</a></li>
        <li><a href="#">&nbsp;&middot;&nbsp;Edit Date and Location</a></li>
        <li><a href="#">&nbsp;&middot;&nbsp;Rotate 90&deg; CW</a></li>
        <li class="divider"></li>
        <li><a href="#" class="clear">Clear pinned photos</a></li>
      <% } else { %>
        <li><a>Select photos by clicking <i class="tb-icon-light tb-icon-pin"></i></a></li>
      <% } %>
    </ul>
  <?php } ?>
</script>

<script type="tmpl/underscore" id="notification-meta">
  <div class="alert alert-<% if(mode=='confirm') { %>confirm<% } else { %>error<% } %> trovebox-message">
    <div class="container">
      <% if(type!='flash') { %>
        <button type="button" class="close <% if(type=='static') { %> notificationDelete<% } %>" data-dismiss="alert" data-target=".trovebox-message">×</button>
      <% } %>
      <%= msg %>
    </div>
  </div>
</script>

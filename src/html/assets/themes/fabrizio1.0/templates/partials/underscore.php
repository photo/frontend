<?php $isAdmin = $this->user->isAdmin(); ?>
<script type="tmpl/underscore" id="photo-meta">
  <div class="photo-meta">
    <?php if($isAdmin) { ?>
      <h4 class="title edit"><a href="/p/<%= id %>" title="Update the title"><i class="icon-pencil"></i> <%= title || filenameOriginal %></a></h4>
      <ul class="info">
        <li><a href="#" title="Comments"><i class="icon-comments tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#" title="Favorites"><i class="icon-heart tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#" title="Share via Facebook, Twitter or Email"><i class="icon-share-alt tb-icon-dark"></i></li>
        <li><a href="#" title="Toggle the privacy setting"><i class="icon-<%= permission == 0 ? 'lock' : 'unlock' %> tb-icon-dark permission edit" data-id="<%= id %>"></i></li>
        <li><a href="#" title="Set as your profile photo"><i class="icon-user tb-icon-dark profile edit" data-id="<%= id %>"></i></li>
        <li><a href="#" title="Select for batch editing"><i class="icon-pushpin tb-icon-dark pin edit" data-id="<%= id %>"></i></li>
        <li><a href="#" title="Delete this photo"><i class="icon-trash tb-icon-dark delete edit" data-id="<%= id %>"></i></li>
      </ul>
    <?php } else { ?>
      <h4 class="title"><%= title || filenameOriginal %></h4>
      <ul class="info">
        <li><a href="#"><i class="icon-comments tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#"><i class="icon-heart tb-icon-dark"></i> <span class="number">24</span></li>
        <li><a href="#"><i class="icon-share-alt tb-icon-dark"></i></li>
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
  <?php if($isAdmin) { ?>
    <h4 class="profile-name-meta username"><span class="name edit"><i class="icon-pencil"></i> <%= name %></span></h4>
  <?php } else { ?>
    <h4 class="profile-name-meta username"><span class="name"><%= name %></span></h4>
  <?php } ?>
  <div class="tray-wrap">
    <span class="avatar"><img class="avatar profile-pic profile-photo" src="<%= photoUrl %>" /></span>
    <div class="tray">
      <div class="details">
        <h5 class="username"><%= name %></h5>
        <ul>
          <li>
            <a href="/photos/list">
              <i class="icon-picture" rel="tooltip" title="View Photos" data-placement="bottom"></i>
              <span class="number"><%= counts.photos %></span>
              <span class="title">photos</span>
            </a>
          </li>
          <li>
            <a href="/albums/list">
              <i class="icon-th-large" rel="tooltip" title="View Albums" data-placement="bottom"></i>
              <span class="number"><%= counts.albums %></span>
              <span class="title">albums</span>
            </a>
          </li>
          <li>
            <a href="/tags/list">
              <i class="icon-tags" rel="tooltip" title="View Tags" data-placement="bottom"></i>
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
  <?php if($isAdmin) { ?>

    <span class="name edit" title="Change display name"><i class="icon-pencil"></i> <%= name %></span>
  <?php } else { ?>
    <span class="name" ><%= name %></span>
  <?php } ?>

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
          <a href="" class="prev"><i class="icon-angle-left"></i></a>
          <a href="" class="next"><i class="icon-angle-right"></i></a>
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
  <div class="action-block">
    <ul>
      <li><i class="icon-eye-open"></i> 110 Views</i></li>
      <li><i class="icon-comment"></i> 7 Comments</i></li>
      <?php if($this->config->site->allowOriginalDownload == 1) { ?>
        <li><a href="<%= pathDownload %>"><i class="icon-download"></i> Download</i></a></li>
      <?php } ?>
      <li><i class="icon-heart"></i> 16 Favorites</i></li>
      <li><a href="#"><i class="icon-share"></i> Share</i></a></li>
      <li><a class="permission<?php if($isAdmin) { ?> edit<?php } ?>" href="#"><i class="icon-<%= permission == 0 ? 'lock' : 'unlock' %>"></i> Private</i></a></li>
    </ul>
  </div>
  <div class="detail-block">
    <div class="title">
      <span class="text"><%= title || filenameOriginal %></span>
      <span class="actions">
        <!--<a href="#"><i class="icon-heart"></i></a>
        <a href="#"><i class="icon-comments"></i></a>
        <a href="#"><i class="icon-share"></i></a>-->
      </span>
    </div>
    <div class="description">
      <span class="text"><%= description %></span>
    </div>
  </div>
</script>

<script type="tmpl/underscore" id="album-meta">
  <li>
    <a href="/photos/album-<%= id %>/list">
      <% if (!_.isNull(cover)) { %>
        <img src="<%= cover.path200x200xCR %>">
      <% } %>
      <?php if($isAdmin) { ?>
        <h5 class=" name edit"><%= name %></h5>
      <?php } else { ?>
        <h5 class="name"><%= name %></h5>
      <?php } ?>
    </a>
  </li>
</script>

<script type="tmpl/underscore" id="photo-detail-meta">
  <h1 class="photo-title"></h1>
  
  <div class="row">
    <div class="span9">
      <div class="photo">
        <img src="<%= path870x870 %>" />
        <span class="mag photo-view-modal-click" data-id="<%= id %>"><i class="icon-search"></i></span>
      </div>
      <div class="description"></div>
      <div class="comments"></div>
      
    </div>
    <div class="span3">
      
      <div class="userbadge userbadge-light user-badge-meta owner"></div>
      
      <div class="pagination">
        <div class="slider">
          <div class="arrow arrow-prev"><i class="icon-angle-left"></i></div>

          <div class="photos">
            <div class="scroller">
              <div class="thumbs"></div>
            </div>
          </div>
          <div class="arrow arrow-next"><i class="icon-angle-right"></i></div>
        </div>
      </div>
      
      <div class="photo-meta"></div>
      
      <ul class="tags">
        <% for(var tag in tags) { %>
          <li><a href="#"><%= tags[tag] %></a></li>
        <% } %>
      </ul>
    </div>
  </div>
</script>

<script type="tmpl/underscore" id="photo-detail-title-tmpl">
  <span class="title<?php if($isAdmin) { ?> edit<?php } ?>"><%= title || filenameOriginal %></span>
  <span class="actions">
    <a href="#"><i class="icon-heart"></i></a>
    <a href="#"><i class="icon-comment"></i></a>
    <a href="#"><i class="icon-share"></i></a>
  </span>
</script>

<script type="tmpl/underscore" id="photo-detail-description-tmpl">
  <span class="text<?php if($isAdmin) { ?> edit<?php } ?>"><%= description %></span>
</script>

<script type="tmpl/underscore" id="photo-comments-tmpl">
  <h3><span class="comment-count"><%= this.actions ? actions.length : 0 %></span> Comments...</h3>
  
  <ul class="comment-list hide"></ul>
  
  <form class="comment-form" action="/action/<%= id %>/photo/create" method="post">
    <textarea rows="4" name="value"></textarea>
    <input type="hidden" name="type" value="comment" />
    <input type="hidden" name="targetUrl" value="<%= window.location %>" />
    <input type="hidden" name="crumb" value="" />
    <div class="form-buttons">
      <button class="btn btn-primary" type="submit">Leave a Comment</button>
    </div>
  </form>
</script>

<script type="tmpl/underscore" id="photo-comment-tmpl">
  <li>
    <img src="<%= avatar %>" class="avatar" />
    <div class="comment">
      <div class="title">
        <span class="date"><%= date %></span>
        <span class="name"><%= name %></span>
      </div>
      <div class="value">
        <%= value %>
      </div>
    </div>
  </li>
</script>

<script type="tmpl/underscore" id="photo-detail-meta-tmpl">
  <ul>
    <li><i class="icon-heart"></i> 16 Favorites</i></li>
    <li><i class="icon-calendar"></i> <%= phpjs.date('M d, Y', dateTaken) %></i></li>
    <li><i class="icon-comment"></i> 7 Comments</i></li>
    <li><a class="permission<?php if($isAdmin) { ?> edit<?php } ?>" href="#"><i class="icon-<%= permission == 0 ? 'lock' : 'unlock' %>"></i> <%= permission == 0 ? 'Private' : 'Public' %></i></a></li>
    <li><i class="icon-eye-open"></i> 110 Views</i></li>
    
    <?php if($this->config->site->allowOriginalDownload == 1) { ?>
      <li><a href="<%= pathDownload %>"><i class="icon-download"></i> Download</i></a></li>
    <?php } ?>
    
  </ul>
</script>

<script type="tmpl/underscore" id="batch-meta">
  <?php if($isAdmin) { ?>
    <a data-toggle="dropdown" href="#"><i class="tb-icon-light <% if(!loading) { %>icon-cogs<% } else { %>icon-spinner icon-spin<% } %>"></i> Batch Edit <% if (count > 0) { %><span class="badge badge-important"><%= count %></span><% } %></a>
    <ul class="dropdown-menu">
      <% if (count > 0) { %>
        <li><a>Batch edit your photos</a></li>
        <li class="divider"></li>
        <li><a href="#" class="tags">&nbsp;&middot;&nbsp;Add Tags</a></li>
        <li><a href="#" class="albums">&nbsp;&middot;&nbsp;Add to Album</a></li>
        <li><a href="#">&nbsp;&middot;&nbsp;Manage Privacy</a></li>
        <!--<li><a href="#">&nbsp;&middot;&nbsp;Edit Date and Location</a></li>-->
        <li><a href="#">&nbsp;&middot;&nbsp;Rotate 90&deg; CW</a></li>
        <li class="divider"></li>
        <li><a href="#" class="clear">Clear pinned photos</a></li>
      <% } else { %>
        <li><a><i class="tb-icon-light icon-pushpin"></i> Hover over a photo and click the pushpin</a></li>
      <% } %>
    </ul>
  <?php } ?>
</script>

<script type="tmpl/underscore" id="notification-meta">
  <div class="alert alert-<% if(mode=='confirm') { %>confirm<% } else { %>error<% } %> trovebox-message">
    <div class="container">
      <button type="button" class="close <% if(type=='static') { %> notificationDelete<% } %>" data-dismiss="alert" data-target=".trovebox-message">×</button>
      <%= msg %>
    </div>
  </div>
</script>

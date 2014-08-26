<?php $isAdmin = $this->user->isAdmin(); ?>
<script type="tmpl/underscore" id="photo-meta">
  <div class="photo-meta">
    <?php if($isAdmin) { ?>
      <h4 class="title edit"><a href="/p/<%= id %>" title="Update the title"><i class="icon-pencil"></i><%- title || filenameOriginal %></a></h4>
      <ul class="info">
        <!--<li><a href="#" title="Comments"><i class="icon-comments"></i> <span class="number">24</span></li>
        <li><a href="#" title="Favorites"><i class="icon-heart"></i> <span class="number">24</span></li>-->
        <li><a href="#" class="share" title="Share via Facebook, Twitter or Email" data-id="<%= id %>"><i class="icon-share-alt"></i>Share</a> &nbsp;</li>
        <li class="pull-right"><a href="#" title="Delete this photo"><i class="icon-trash photo delete edit" data-action="delete" data-ids="<%= id %>"></i></a></li>
        <li class="pull-right"><a href="#" title="Select for batch editing"><i class="icon-pushpin pin edit" data-id="<%= id %>"></i></a></li>
        <li class="pull-right album"><a href="#" title="Set as your album cover"><i class="icon-th-large album edit" data-id="<%= id %>"></i></a></li>
        <li class="pull-right"><a href="#" title="Toggle the privacy setting"><i class="icon-<%= permission == 0 ? 'lock' : 'unlock' %> permission edit" data-id="<%= id %>"></i></a></li>
      </ul>
    <?php } else { ?>
      <h4 class="title"><%- title || filenameOriginal %></h4>
      <ul class="info">
        <!--<li><a href="#"><i class="icon-comments"></i> <span class="number">24</span></a></li>
        <li><a href="#"><i class="icon-heart"></i> <span class="number">24</span></a></li>-->
        <!--<li><a href="#" title="Share via Facebook, Twitter or Email"><i class="icon-share-alt"></i> Share</a></li>-->
      </ul>
    <?php } ?>
  </div>
</script>
<script type="tmpl/underscore" id="profile-photo-meta">
  <% if(photoUrl.search('gravatar.com') == -1) { %>
    <img class="avatar profile-pic profile-photo" src="<%= photoUrl %>" <% if(photoUrl.search('gravatar.com') == -1) { %> <% } %> />
  <% } else { %>
    <i class="to icon-user" title="<?php if($isAdmin) { ?>Click the profile icon when mousing over any photo to set it as your profile photo.<?php } else { ?>Trovebox User<?php } ?>"></i>
  <% } %>
</script>

<script type="tmpl/underscore" id="user-badge-meta">
  <?php if($isAdmin) { ?>
    <h4 class="profile-name-meta username"><span class="name edit"><i class="icon-pencil"></i> <span class="value"><%- name %></span></span></h4>
  <?php } else { ?>
    <h4 class="profile-name-meta username"><span class="name"><%- name %></span></h4>
  <?php } ?>
  <div class="tray-wrap">
    <span class="avatar">
      <img class="avatar profile-pic profile-photo" src="<%= photoUrl %>" <% if(photoUrl.search('gravatar.com') == -1) { %>title="Click the profile icon when mousing over any photo to set it as your profile photo." <% } %> />
    </span>
    <div class="tray">
      <div class="details">
        <h5 class="username"><%- name %></h5>
        <ul>
          <li>
            <a href="/photos/list" title="View Photos">
              <i class="icon-picture" rel="tooltip" data-placement="bottom"></i>
              <span class="number"><%= counts.photos %></span>
              <span class="title">photos</span>
            </a>
          </li>
          <li>
            <a href="/albums/list" title="View Albums">
              <i class="icon-th-large" rel="tooltip" data-placement="bottom"></i>
              <span class="number"><%= counts.albums %></span>
              <span class="title">albums</span>
            </a>
          </li>
          <li>
            <a href="/tags/list" title="View Tags">
              <i class="icon-tags" rel="tooltip" data-placement="bottom"></i>
              <span class="number"><%= counts.tags %></span>
              <span class="title">tags</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <% if( showStorage ){ %>
    <div class="tray">
      <div class="details">
        <ul>
          <li>
            <i class="tb-icon-storage"></i>
            <span class="number"><%= TBX.format.bytes_to(counts.storage).size %></span>
            <span class="title"><%= TBX.format.bytes_to(counts.storage).unit %> used</span>
          </li>
        </ul>
      </div>
    </div>
    <% } %>
  </div>
</script>

<script type="tmpl/underscore" id="profile-name-meta">
  <?php if($isAdmin) { ?>
    <span class="name edit" title="Change display name"><i class="icon-pencil"></i> <span class="value"><%- name %></span></span>
  <?php } else { ?>
    <span class="name" ><%- name %></span>
  <?php } ?>

</script>

<script type="tmpl/underscore" id="op-lightbox">
  <div class="op-lightbox">
    <div class="header">
      <div class="container">
        <div class="logo"></div>
        <a class="detail detail-link" href="">Detail View</a> <a href="#" class="detail close-link" title="Pressing ESC also closes this lightbox"><i class="icon-remove"></i></a>
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
          <!--<span class="special-key">D</span>-->
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
      <span class="text">
        <?php if($isAdmin) { ?>
          <a href="#" class="title edit text"><i class="icon-pencil"></i><%- title || filenameOriginal %></a>
        <?php } else { ?>
          <%- title || filenameOriginal %>
        <?php } ?>
      </span>
      <span class="actions hidden-phone">
        <?php if($isAdmin) { ?>
          <a href="#" class="share" data-id="<%= id %>" title="Share this photo via email, Facebook or Twitter"><i class="icon-share-alt"></i></a>
          <a href="<%= pathDownload %>" title="Download the original high resolution photo"><i class="icon-download"></i></a>
        <?php } else { ?>
          <?php if($this->config->site->allowOriginalDownload == 1) { ?>
            <a href="<%= pathDownload %>" title="Download the original high resolution photo"><i class="icon-download"></i></a>
          <?php } ?>
          <!--<i class="icon-<%= permission == 0 ? 'lock' : 'unlock' %>"></i>-->
        <?php } ?>
      </span>
    </div>
    <div class="description">
      <span class="text">
        <?php if($isAdmin) { ?>
          <a href="#" class="description edit text"><i class="icon-pencil"></i> <%- description %></a>
        <?php } else { ?>
          <%- description %>
        <?php } ?>
      </span>
    </div>
  </div>
</script>

<script type="tmpl/underscore" id="album-meta">
  <div class="cover album-<%= id %>">
    <a href="/photos/album-<%= id %>/list">
      <% if (!_.isNull(cover)) { %>
        <img src="<%= cover.path200x200xCR %>">
      <% } else { %>
        <img src="data:image/gif;base64,R0lGODlhAQABAPAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
      <% } %>
      <span class="stack stack1"></span>
      <span class="stack stack2"></span>
    </a>
    <?php if($isAdmin) { ?>
      <h5>
        <span>
          <span class="name edit" title="<%- name %>"><i class="icon-pencil"></i><%- name %></span>
          <div class="icons">
            <a href="#" class="share" data-id="<%= id %>" title="Share this album"><i class="icon-share"></i>Share</a>
            <a href="#" class="delete pull-right" data-id="<%= id %>" title="Delete this album"><i class="icon-trash"></i></a>
          </div>
        </span>
      </h5>
    <?php } else { ?>
      <h5><span class="name" title="<%- name %>"><%- name %></span></h5>
    <?php } ?>
  </div>
</script>

<script type="tmpl/underscore" id="photo-detail-meta">
  <h1 class="photo-title"></h1>
  <div class="row">
    <div class="span9">
      <div class="photo">
        <img src="<%= path870x870 %>" class="photo-img-<%= id %> photo-large" />
        <!--<span class="mag photo-view-modal-click" data-id="<%= id %>"><i class="icon-search"></i></span>-->
      </div>
      <div class="description"></div>
    </div>
    <div class="span3">
      <ul class="sidebar">
        <li class="hidden-phone">
          <div class="userbadge userbadge-light user-badge-meta owner"></div>
        </li>
        <li>
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
        </li>
        <li>
          <div class="photo-date"></div>
          <div class="photo-meta"></div>
        </li>
        <li>
          <ul class="collapsibles"></ul>
        </li>
        <li>
          <div class="rights"></div>
        </li>
      </ul>
    </div>
  </div>
</script>

<script type="tmpl/underscore" id="photo-detail-title-tmpl">
  <?php if($isAdmin) { ?>
    <span class="title edit"><i class="icon-pencil"></i><%- title || filenameOriginal %></span>
  <?php } else { ?>
    <span class="title"><%- title || filenameOriginal %></span>
  <?php } ?>
  <span class="actions">
    <!--<a href="#"><i class="icon-heart"></i></a>
    <a href="#"><i class="icon-comment"></i></a>-->
    <?php if($isAdmin) { ?>
      <a href="#" class="triggerShare"><i class="icon-share-alt triggerShare"></i></a>
    <?php } ?>
  </span>
</script>

<script type="tmpl/underscore" id="photo-detail-date-tmpl">
  <?php if($isAdmin) { ?>
    <i class="icon-calendar"></i> <span class="date-view date edit"><%= phpjs.date('l, F jS, Y @ g:ia', dateTaken) %><span class="display-for-edit" data-value="<%= phpjs.date('F j, Y h:i a', dateTaken) %>"></span></span>
  <?php } else { ?>
    <i class="icon-calendar"></i> <%= phpjs.date('M d, Y', dateTaken) %></i>
  <?php } ?>
</script>

<script type="tmpl/underscore" id="photo-detail-description-tmpl">
  <span class="text<?php if($isAdmin) { ?> edit<?php } ?>"><%- description %></span>
</script>

<script type="tmpl/underscore" id="photo-comments-tmpl">
  <h3><span class="comment-count"><%= this.actions ? actions.length : 0 %></span> Comments...</h3>
  
  <ul class="comment-list hide"></ul>
  
  <h3>Your Comment</h3>
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
    <li><a class="permission<?php if($isAdmin) { ?> edit<?php } ?>" href="#"><i class="icon-<%= permission == 0 ? 'lock' : 'unlock' %>"></i> <%= permission == 0 ? 'Private' : 'Public' %></i></a></li>
    <?php if($isAdmin) { ?>
      <li><a href="#" class="lightbox" data-id="<%= id %>"><i class="icon-zoom-in"></i> Lightbox View</i></a></li>
    <?php } ?>
    <?php if($isAdmin) { ?>
      <li><a class="rotate" href="#"><i class="icon-rotate-right"></i> Rotate</a></li>
    <?php } ?>
    <?php if($isAdmin || $this->config->site->allowOriginalDownload == 1) { ?>
      <li><a href="<%= pathDownload %>" class="download trigger"><i class="icon-download"></i> Download</i></a></li>
    <?php } ?>
    <?php if($isAdmin) { ?>
      <li><a class="profile" href="#" data-id="<%= id %>"><i class="icon-user profile"></i> Profile Photo</a></li>
      <li><a href="#" class="share trigger" data-id="<%= id %>"><i class="icon-share-alt"></i> Share</i></a></li>
    <?php } ?>
  </ul>
</script>

<script type="tmpl/underscore" id="photo-detail-rights-tmpl">
  <% if(license.length === 0) { %>
    All Rights Reserved
  <% } else { %>
    <a title="Creative Commons"><i class="tb-icon-small-cc"></i></a>
    <% if(license.search('BY') !== -1) { %>
      <a title="CC BY - Attribution"><i class="tb-icon-small-cc-by"></i></a>
    <% } %>
    <% if(license.search('ND') !== -1) { %>
      <a title="CC ND - No Derivatives"><i class="tb-icon-small-cc-nd"></i></a>
    <% } %>
    <% if(license.search('NC') !== -1) { %>
      <a title="CC NC - Non Commercial"><i class="tb-icon-small-cc-nc"></i></a>
    <% } %>
    <% if(license.search('SA') !== -1) { %>
      <a title="CC SA - Share Alike"><i class="tb-icon-small-cc-sa"></i></a>
    <% } %>
  <% } %>
</script>

<script type="tmpl/underscore" id="photo-detail-collapsibles-tmpl">
  <li>
    <% if( typeof latitude !== 'undefined' && latitude ){ %>
      <h3 class="sidebar-heading">
        <a href="#photo-location" data-toggle="collapse">
          <i class="arrow open icon-angle-down"></i>
          <i class="arrow closed icon-angle-right"></i>
          <i class="icon-globe"></i> Location
        </a>
      </h3>
    <% } %>
    <div id="photo-location" class="collapsible collapse">
      <div class="map"></div>
    </div>
  </li>
  <li>
    <h3 class="sidebar-heading">
      <a href="#photo-tags" data-toggle="collapse">
        <i class="arrow open icon-angle-down"></i>
        <i class="arrow closed icon-angle-right"></i>
        <i class="icon-tags"></i> Tags
      </a>
    </h3>
    <div id="photo-tags" class="collapsible collapse">
      <div class="c">
        <ul class="tags">
          <% for(var tag in tags) { %>
            <li><a href="/photos/tags-<%- tags[tag] %>/list"><%- tags[tag] %></a></li>
          <% } %>
        </ul>
      </div>
    </div>
  </li>
  <li>
    <h3 class="sidebar-heading">
      <a href="#photo-exif" data-toggle="collapse">
        <i class="arrow open icon-angle-down"></i>
        <i class="arrow closed icon-angle-right"></i>
        <i class="icon-camera"></i> Camera Data
      </a>
    </h3>
    <div id="photo-exif" class="collapsible collapse">
      <div class="c">
        <table cellpadding="0" cellspacing="0" border="0">
          <% if( typeof exifCameraMake !== 'undefined' && exifCameraMake ){ %>
            <tr>
              <th>Camera Make</th>
              <td><%- exifCameraMake %></td>
            </tr>
          <% } %>
          <% if( typeof exifCameraModel !== 'undefined' && exifCameraModel ){ %>
            <tr>
              <th>Camera Model</th>
              <td><%- exifCameraModel %></td>
            </tr>
          <% } %>
          <% if( typeof exifExposureTime !== 'undefined' && exifExposureTime ){ %>
            <tr>
              <th>Exposure Time</th>
              <td><%- exifExposureTime %></td>
            </tr>
          <% } %>
          <% if( typeof exifFNumber !== 'undefined' && exifFNumber ){ %>
            <tr>
              <th>Aperture</th>
              <td>f/<%= exifFNumber %></td>
            </tr>
          <% } %>
          <% if( typeof exifFocalLength !== 'undefined' && exifFocalLength ){ %>
            <tr>
              <th>Focal Length</th>
              <td><%= exifFocalLength %>mm</td>
            </tr>
          <% } %>
          <% if( typeof exifISOSpeed !== 'undefined' && exifISOSpeed ){ %>
            <tr>
              <th>ISO</th>
              <td><%= exifISOSpeed %></td>
            </tr>
          <% } %>
        </table>
      </div>
    </div>
  </li>
  
  <!--<li>
    <h3 class="sidebar-heading">
      <a href="#photo-share" data-toggle="collapse">
        <i class="arrow open icon-angle-down"></i>
        <i class="arrow closed icon-angle-right"></i>
        <?php if($isAdmin) { ?>
          <i class="icon-share-alt"></i> Embed &amp; Share
        <?php } ?>
      </a>
    </h3>
    <div id="photo-share" class="collapsible collapse">
      <div class="c">
      Share stuff...
      </div>
    </div>-->
  </li>
</script>

<script type="tmpl/underscore" id="batch-meta">
  <?php if($isAdmin) { ?>
    <?php if($this->utility->isActiveTab('photos') || $this->utility->isActiveTab('upload')) { ?>
      <a data-toggle="dropdown" href="#"><i class="<% if(!loading) { %>icon-cogs<% } else { %>icon-spinner icon-spin<% } %>"></i> Batch Edit <% if (count > 0) { %><span class="badge badge-important"><%= count %></span><% } %></a>
      <ul class="dropdown-menu">
        <% if (count > 0) { %>
          <li><a>Update photo information</a></li>
          <li><a href="#" class="showBatchForm photo" data-action="tags">&nbsp;&middot;&nbsp;Manage Tags</a></li>
          <li><a href="#" class="showBatchForm photo" data-action="albums">&nbsp;&middot;&nbsp;Manage Albums</a></li>
          <li><a href="#" class="showBatchForm photo" data-action="privacy">&nbsp;&middot;&nbsp;Manage Privacy</a></li>
          <li><a href="#" class="showBatchForm photo" data-action="delete">&nbsp;&middot;&nbsp;Delete</a></li>
          <!--<li><a href="#">&nbsp;&middot;&nbsp;Edit Date and Location</a></li>-->
          <!--<li class="divider"></li>
          <li><a>Modify photos</a></li>
          <li><a href="#">&nbsp;&middot;&nbsp;Rotate 90&deg; CW</a></li>-->
          <li class="divider"></li>
          <li><a href="#" class="clear">Clear pinned photos</a></li>
        <% } else { %>
          <li><a><i class="icon-pushpin"></i> Hover over a photo and click the pushpin</a></li>
        <% } %>
      </ul>
    <?php } else if($this->utility->isActiveTab('albums')) { ?>
      <a href="#" class="showBatchForm album"><i class="<% if(!loading) { %>icon-plus<% } else { %>icon-spinner icon-spin<% } %>"></i> Create an album</a>
    <?php } ?>
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
<script type="tmpl/underscore" id="progress-meta">
  <div class="progress <%= striped %>">
    <div class="bar bar-success" style="width:<%= success %>%;"></div>
    <div class="bar bar-warning" style="width:<%= warning %>%;"></div>
    <div class="bar bar-danger" style="width:<%= danger %>%;"></div>
  </div>
</script>
<script type="tmpl/underscore" id="keyboard-shortcuts">
  <h4>Advanced shortcuts</h4>
  <div class="row">
    <div class="span5">
      <h5>Gallery page</h5>
      <ul class="unstyled">
        <li><strong>alt</strong> + <strong>click</strong> &middot; Select a photo.</li>
        <li><strong>shift</strong> + <strong>click</strong> &middot; Select a range of photos.</li>
      </ul>    
    </div>
    <div class="span5">
      <h5>Lightbox</h5>
      <ul class="unstyled">
        <li><strong>t</strong> &middot; Edit the title.</li>
        <li><strong>d</strong> &middot; Edit the description.</li>
        <li><strong>g</strong> &middot; Edit tags.</li>
        <li><strong>p</strong> &middot; Toggle privacy.</li>
        <li><strong>h</strong> or <strong><i class="icon-arrow-left"></i></strong> &middot; Previous photo.</li>
        <li><strong>j</strong> or <strong><i class="icon-arrow-right"></i></strong> &middot; Next photo.</li>
      </ul>    
    </div>
  </div>
  <a href="#" class="batchHide close" title="Close this dialog"><i class="icon-remove batchHide"></i></a>
</script>

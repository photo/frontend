(function($){
  op.ns('data.view').PhotoGallery = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'photo-meta',
    template    :_.template($('#photo-meta').html()),
    editable    : {
      '.title.edit a' : {
        name: 'title',
        title: 'Edit Photo Title',
        placement: 'top',
        on: {
          shown: function(){
            // var view = $(this).data('editable').view;
            $(this).parents('.imageContainer').addClass('editing');
            $(this).data('editable').container.setPosition();
            
            // remove the fade effect because we need to toggle the overflow
            // and it looks crappy when it gets cut off during the transition
            $(this).data('editable').container.tip().removeClass('fade');
          },
          hidden : function(){
            $(this).parents('.imageContainer').removeClass('editing');
          }
        }
      }
    },
    events: {
      'click .album.edit': 'album',
      'click .delete.edit': 'deleteOrRestore',
      'click .restore.edit': 'deleteOrRestore',
      'click .permission.edit': 'permission',
      'click .profile.edit': 'profile',
      'click .pin.edit': 'pin',
      'click .share': 'share'
    },
    album: function(ev) {
      TBX.handlers.click.setAlbumCover(ev);
    },
    deleteOrRestore: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), model = this.model, id = model.get('id'), newActiveStatus = model.get('active') == 0 ? 1 : 0;
      model.set('active', newActiveStatus, {silent:true});
      model.save(null, {
        error: TBX.notification.display.generic.error,
        success: function() {
          if(newActiveStatus === 1) {
            TBX.callbacks.photoRestored(id);
            TBX.notification.show('Your photo was restored.', 'flash', 'confirm');
          } else {
            TBX.callbacks.photoDeleted(id);
            TBX.notification.show('Your photo was deleted. Click the <i class="icon-undo"></i> icon on the photo to restore.', 'flash', 'confirm');
          }
          this.render();
        }
      });
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model, permission = model.get('permission') == 0 ? 1 : 0;
      model.set('permission', permission, {silent: true});
      model.save(null, {error: TBX.notification.display.generic.error});
    },
    pin: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), batch = OP.Batch, count, photo = op.data.store.Photos.get(id).toJSON();
      if(batch.exists(id)) // exists, we need to remove
        batch.remove(id);
      else // let's add it
        batch.add(id, photo);

      count = batch.length();
      TBX.notification.show(TBX.format.sprintf(TBX.strings.batchConfirm, count, count > 1 ? 's' : ''), 'flash', 'confirm');
    },
    profile: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), 
          ownerModel = op.data.store.Profiles.get(TBX.profiles.getOwner()),
          viewerModel = op.data.store.Profiles.get(TBX.profiles.getViewer());
      ownerModel.set('photoId', id, {silent:true});
      ownerModel.save(null, {error: TBX.notification.display.generic.error, success: function(){ TBX.notification.show('Your profile photo was successfully updated.', 'flash', 'confirm'); }});
      if(TBX.profiles.getOwner() !== TBX.profiles.getViewer()) {
        viewerModel.set('photoId', id, {silent:true});
        viewerModel.save();
      }
    },
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/photo/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);

(function($){
  op.ns('data.view').PhotoGallery = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      OP.Util.on('callback:batch-remove', this.batchRemove);
      OP.Util.on('callback:batch-add', this.batchAdd);
    },
    batchAdd: function(photo) {
      var model = TBX.init.pages.photos.batchModel, batch = OP.Batch;
      $('.photo-id-'+photo.id).addClass('pinned');
      model.set('count', batch.length());
      model.trigger('change');
    },
    batchRemove: function(id) {
      var model = TBX.init.pages.photos.batchModel, batch = OP.Batch;
      $('.photo-id-'+id).removeClass('pinned');
      model.set('count', batch.length());
      model.trigger('change');
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
      'click .delete.edit': 'delete',
      'click .permission.edit': 'permission',
      'click .profile.edit': 'profile',
      'click .pin.edit': 'pin',
      'click .share': 'share'
    },
    delete: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model, confirmation;

      confirmation = confirm('Are you sure you want to delete this photo?');
      if(confirmation)
        model.destroy({success: this.modelDestroyed, error: TBX.notification.display.generic.error});
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model, permission = model.get('permission') == 0 ? 1 : 0;
      model.set('permission', permission, {silent: true});
      model.save(null, {error: TBX.notification.display.generic.error});
    },
    pin: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), batch = OP.Batch, photo = op.data.store.Photos.get(id).toJSON();
      if(batch.exists(id)) { // exists, we need to remove
        OP.Batch.remove(id);
        TBX.notification.show('1 photo was <em>removed</em> from your <i class="icon-cogs"></i> batch queue.', 'flash', 'confirm');
      } else { // let's add it
        OP.Batch.add(id, photo);
        TBX.notification.show('1 photo was <em>added</em> to your <i class="icon-cogs"></i> batch queue.', 'flash', 'confirm');
      }
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
      OP.Util.makeRequest('/photos/'+id+'/share.json', {}, TBX.callbacks.share, 'json', 'get');
    },
    modelChanged: function() {
      this.render();
    },
    modelDestroyed: function(model) {
      var id = model.get('id'), $el = $('.imageContainer.photo-id-'+id);
      $el.fadeTo('medium', .25);
    }
  });
})(jQuery);

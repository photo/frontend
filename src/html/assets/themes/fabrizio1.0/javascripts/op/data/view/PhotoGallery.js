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
      'click .permission.edit': 'permission',
      'click .profile.edit': 'profile',
      'click .pin.edit': 'pin'
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:false});
      model.save();
    },
    pin: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = TBX.init.pages.photos.batchModel, batch = OP.Batch;
      if(batch.exists(id)) { // exists, we need to remove
        OP.Batch.remove(id);
      } else { // let's add it
        OP.Batch.add(id);
      }
    },
    profile: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), 
          ownerModel = op.data.store.Profiles.get(TBX.profiles.getOwner()),
          viewerModel = op.data.store.Profiles.get(TBX.profiles.getViewer());
      ownerModel.set('photoId', id, {silent:true});
      ownerModel.save();
      if(TBX.profiles.getOwner() !== TBX.profiles.getViewer()) {
        viewerModel.set('photoId', id, {silent:true});
        viewerModel.save();
      }
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);

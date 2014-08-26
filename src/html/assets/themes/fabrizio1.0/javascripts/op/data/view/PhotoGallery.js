(function($){
  op.ns('data.view').PhotoGallery = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      OP.Util.on('callback:photo-destroy', this.modelDestroyed);
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
      'click .album.edit': 'album',
      'click .delete.edit': 'delete_',
      'click .permission.edit': 'permission',
      'click .pin.edit': 'pin',
      'click .share': 'share'
    },
    album: function(ev) {
      TBX.handlers.click.setAlbumCover(ev);
    },
    delete_: function(ev) {
      TBX.handlers.click.showBatchForm(ev);
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
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/photo/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
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

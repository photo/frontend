(function($){
  op.ns('data.view').BatchIndicator = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      OP.Util.on('callback:batch-clear', this.clearCallback);
      OP.Util.on('batchindicator:add-photos-to-album', this.addPhotosToAlbum);
    },
    addPhotosToAlbum: function(args) {
      var ids = args.ids, albumIds = args.albumIds, photoCount = ids.split(',').length;
      OP.Util.makeRequest('/photos/update.json', {crumb: TBX.crumb(), albumsAdd: albumIds, ids: ids}, function(response) {
        var model = TBX.init.pages.photos.batchModel;
        model.set('loading', false);
        if(response.code === 200) {
          TBX.notification.show('You successfully added ' + photoCount + ' photo' + (photoCount>1?'s':'') + ' to your album.', 'flash', 'confirm');
        } else {
          TBX.notification.show('Sorry, an error occured when trying to add photos to your album.', 'flash', 'error');
        }
      }, 'json', 'post');
    },
    model: this.model,
    className: 'batch-meta',
    template    :_.template($('#batch-meta').html()),
    modelChanged: function() {
      this.render();
    },
    events: {
      'click .clear': 'clear',
      'click .showForm': 'showForm'
    },
    clear: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), batch = OP.Batch, ids = batch.ids();

      // we have to fire this to clear the "pins" in the PhotoGallery view
      for(i in ids)
        OP.Util.fire('callback:batch-remove', ids[i]);

      batch.clear();
    },
    showForm: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), batch = OP.Batch, params = {}, model = TBX.init.pages.photos.batchModel;
      model.set('loading', true);
      params.action = $el.attr('data-id');
      OP.Util.makeRequest('/photos/update.json', params, function(response) {
        var result = response.result;
        model.set('loading', false);
        $('.secondary-flyout').html(result.markup).slideDown('fast');
      }, 'json', 'get');
      return;
    },
    clearCallback: function() {
      var model = TBX.init.pages.photos.batchModel;
      model.set('count', 0);
    },
    render: function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    }
  });
})(jQuery);

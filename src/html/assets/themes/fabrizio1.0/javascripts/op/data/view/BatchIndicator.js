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
      'click .tags': 'tags',
      'click .albums': 'albums'
    },
    albums: function(ev) {
      ev.preventDefault();
      var create, batch = OP.Batch, crumb = TBX.crumb(), ids = batch.ids().join(','), model = TBX.init.pages.photos.batchModel;
      create = confirm("Create a new album?");
      if(!create) {
        // is 1000 albums enough?
        OP.Util.makeRequest('/albums/list.json', {crumb: crumb, pageSize:1000}, function(response) {
          var album, id, msg = '', result = response.result;
          if(response.code !== 200) {
            TBX.notification.show('Sorry, we were unable to get a list of your albums.', 'flash', 'error');
            return;
          }
            
          for(i in result) {
            if(result.hasOwnProperty(i)) {
              album = result[i];
              msg += album.id + ' - ' + album.name + '\n';
            }
          }
          id = prompt(msg, "");
          model.set('loading', true);
          OP.Util.fire('batchindicator:add-photos-to-album', {ids: ids, albumIds: id});
        }, 'json', 'get');
      } else {
        var name = prompt('Provide a name for your album', '');
        model.set('loading', true);
        OP.Util.makeRequest('/album/create.json', {crumb: crumb, name: name}, function(response) {
          var result = response.result;
          if(response.code !== 201) {
            TBX.notification.show('Sorry, we were unable to create your album.', 'flash', 'error');
            model.set('loading', false);
            return;
          }

          OP.Util.fire('batchindicator:add-photos-to-album', {ids: ids, albumIds: result.id});
        }, 'json', 'post');
      }
    },
    clear: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), batch = OP.Batch, ids = batch.ids();

      // we have to fire this to clear the "pins" in the PhotoGallery view
      for(i in ids)
        OP.Util.fire('callback:batch-remove', ids[i]);

      batch.clear();
    },
    tags: function(ev) {
      ev.preventDefault();
      var tags, batch = OP.Batch, params = {ids: batch.ids().join(','), tagsAdd: tags, crumb: TBX.crumb()}, model = TBX.init.pages.photos.batchModel;
      tags = prompt("What tag should be added?");
      model.set('loading', true);
      OP.Util.makeRequest('/photos/update.json', params, function(response) {
        model.set('loading', false);
        if(response.code === 200) {
          var tagCount = tags.split(',').length, photoCount = batch.length();
          TBX.notification.show('You successfully added ' + tagCount + ' tag' + (tagCount>1?'s':'') + ' to ' + photoCount + ' photo' + (photoCount>1?'s':'') + '.', 'flash', 'confirm');
        } else {
          TBX.notification.show('Sorry, an error occured when trying to add tags to your photos.', 'flash', 'confirm');
        }
      }, 'json', 'post');
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

(function($){
  op.ns('data.view').BatchIndicator = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      OP.Util.on('callback:batch-clear', this.clearCallback);
    },
    model: this.model,
    className: 'batch-meta',
    template    :_.template($('#batch-meta').html()),
    modelChanged: function() {
      this.render();
    },
    events: {
      'click .clear': 'clear',
      'click .tags': 'tags'
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
      tags = prompt("What tas should be added?");
      model.set('loading', true);
      OP.Util.makeRequest('/photos/update.json', params, function(response) {
        model.set('loading', false);
        if(response.code === 200) {
          var tagCount = tags.split(',').length, photoCount = batch.length();
          TBX.notification.show('<i class="icon-ok"></i> You successfully added ' + tagCount + ' tag' + (tagCount>1?'s':'') + ' to ' + photoCount + ' photo' + (photoCount>1?'x':'') + '.', 'flash', 'confirm');
        } else {
          TBX.notification.show('<i class="icon-warning-sign"></i> Sorry, an error occured when trying to add tags to your photos.');
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

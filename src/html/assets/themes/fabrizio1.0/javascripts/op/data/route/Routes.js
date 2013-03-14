(function($){
  op.ns('data.route').Routes = Backbone.Router.extend({
    initialize: function(options) {
      this.lightbox = op.Lightbox.getInstance();
      for(i in options) {
        if(options.hasOwnProperty(i))
          this[i] = options[i];
      }
    },
    photoDetail: function(id) {
      var photo = op.data.store.Photos.get(id);
      if(typeof photo === 'object')
        this.render(photo.toJSON());
    },
    photoModal: function(id) {
      this.lightbox.open(id);
    },
    photosList: function(id) {
      this.lightbox.hide();
    }
  });
})(jQuery);

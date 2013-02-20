(function($){
  op.ns('data.view').TagSearch = Backbone.View.extend({
    initialize: function() {
      $(this.el)
        .typeahead({
          source : $.merge(_.pluck(OP.Tag.getTags(), 'id'),  _.pluck(OP.Album.getAlbums(), 'name')),
          updater : _.bind(this.updater, this),
          matcher: this.matcher,
          highlighter: this.highlighter
        });
    },
    
    // http://stackoverflow.com/questions/12662824/twitter-bootstrap-typeahead-multiple-values
    updater : function(item){
      /**
       * TODO - this could just fire the search right away...
       */
      var $el = $(this.el);
      return $el.val().replace(/[^,]*$/,'')+item+',';
    },
    matcher: function (item) {
      var tquery = TBX.util.tagExtractor(this.query);
      if(!tquery)
        return false;
      return item.toLowerCase().search(tquery) === 0;
    },
    highlighter: function (item) {
      var query = TBX.util.tagExtractor(this.query).replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&'), icon,
        isTag = _.where(OP.Tag.getTags(), {'id': item}).length > 0, isAlbum = _.where(OP.Album.getAlbums(), {'name': item}).length > 0;

      if(isTag)
        icon = '<i class="icon-tags"></i> ';
      else if(isAlbum)
        icon = '<i class="icon-th-large"></i> ';

      return icon + item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>'
      })
    }
  });
})(jQuery);

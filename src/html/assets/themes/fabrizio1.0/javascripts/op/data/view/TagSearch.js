(function($){
  op.ns('data.view').TagSearch = Backbone.View.extend({
    initialize: function() {
      var $el = $(this.el), source = [];
      if($el.hasClass('tags') && $el.hasClass('albums')) {
        source = $.merge(_.pluck(OP.Tag.getTags(), 'id'),  _.pluck(OP.Album.getAlbums(), 'name'));
      } else if($el.hasClass('tags')) {
        source = _.pluck(OP.Tag.getTags(), 'id');
      } else if($el.hasClass('albums')) {
        source = _.pluck(OP.Album.getAlbums(), 'name');
      }

      $el
        .typeahead({
          source : source,
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
        tag = _.where(OP.Tag.getTags(), {'id': item}), album = _.where(OP.Album.getAlbums(), {'name': item});

      if(tag.length > 0) {
        icon = '<i class="icon-tags"></i> ';
        count = tag[0].count;
      } else if(album.length > 0) {
        icon = '<i class="icon-th-large"></i> ';
        count = album[0].count;
      }

      return  ' <span class="badge badge-inverse pull-right">'+count+'</span>' + icon + item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>'
      });
    }
  });
})(jQuery);

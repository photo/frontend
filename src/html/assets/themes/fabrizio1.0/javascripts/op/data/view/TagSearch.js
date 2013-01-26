(function($){
  op.ns('data.view').TagSearch = Backbone.View.extend({
    initialize: function() {
      $(this.el)
        .typeahead({
          source : _.bind(this.typeaheadSource, this),
          updater : _.bind(this.typeaheadUpdater, this)
        });
    },
    
    typeaheadSource : function(query, process){
      var self = this;
      if(this.store) {
        return this.store.pluck('id');
      }
      self.store = new op.data.collection.Tag();
      return $.get('/tags/list.json', function(response){
        // jeez i hope this is successful.
        
        self.store.add( response.result );
        return process( self.store.pluck('id') );
      });
    },
    
    typeaheadUpdater : function(item){
      /**
       * TODO - this could just fire the search right away...
       */
      return item;
    }
  });
})(jQuery);
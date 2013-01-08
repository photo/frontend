(function($){
  op.ns('data.model').Batch = Backbone.Model.extend({
    initialize: function() {
      this.set('loading', false);
    },
    sync: function(method, model) {},
  });
})(jQuery);

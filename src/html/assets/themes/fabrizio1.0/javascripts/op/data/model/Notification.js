(function($){
  op.ns('data.model').Notification = Backbone.Model.extend({
    sync: function(method, model) {
      model.trigger('change');
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);


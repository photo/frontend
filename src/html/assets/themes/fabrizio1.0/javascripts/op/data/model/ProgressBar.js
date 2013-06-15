(function($){
  op.ns('data.model').ProgressBar = Backbone.Model.extend({
    defaults: {
      "total": 0,
      "success": 0,
      "warning": 0,
      "danger": 0,
      "striped": "progress-striped"
    },
    completed: function() {
      this.set('striped', '');
    },
    sync: function(method, model) {
      model.trigger('change');
    }
  });
})(jQuery);

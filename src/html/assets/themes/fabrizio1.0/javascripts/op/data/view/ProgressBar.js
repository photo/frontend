(function($){
  op.ns('data.view').ProgressBar = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'progress-meta',
    template    :_.template($('#progress-meta').html()),
    render: function() {
      this.$el.html(this.template(this.model.attributes));
      return this;
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);


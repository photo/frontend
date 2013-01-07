(function($){
  op.ns('data.view').Notification = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'notification-meta',
    template    :_.template($('#notification-meta').html()),
    modelChanged: function() {
      this.render();
    },
    render: function(){
      var that = this, notification = that.model.toJSON();
      $(this.el).hide(function() { $(this).html(that.template(notification)).slideDown('medium');; });
      return this;
    }
  });
})(jQuery);


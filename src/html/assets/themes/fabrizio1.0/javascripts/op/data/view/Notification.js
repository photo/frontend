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
      var that = this, $el = $(this.el), notification = that.model.toJSON(), exists = $('.trovebox-message').length === 1;
      if(exists)
        $el.slideUp('medium', function() { $(this).html(that.template(notification)).slideDown('slow'); });
      else
        $el.css('display', 'none').html(that.template(notification)).slideDown('medium');

      return this;
    }
  });
})(jQuery);


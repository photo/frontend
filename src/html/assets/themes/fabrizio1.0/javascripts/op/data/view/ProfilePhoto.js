(function($){
  op.ns('data.view').ProfilePhoto = Backbone.View.extend({
    initialize: function() {
      if( this.model )
        this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'profile-photo-meta',
    template    :_.template($('#profile-photo-meta').html()),
    render      :function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);
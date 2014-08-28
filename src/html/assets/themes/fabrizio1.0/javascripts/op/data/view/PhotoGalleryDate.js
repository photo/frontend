(function($){
  op.ns('data.view').PhotoGalleryDate = Backbone.View.extend({
    model: this.model,
    className: 'photo-meta-date',
    template    :_.template($('#photo-meta-date').html()),

    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    render : function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },

    modelChanged: function() {
      this.render();
    }

  });
})(jQuery);


(function($){
  op.ns('data.view').BatchIndicator = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      OP.Util.on('callback:batch-clear', this.clearCallback);
    },
    model: this.model,
    className: 'batch-meta',
    template    :_.template($('#batch-meta').html()),
    modelChanged: function() {
      this.render();
    },
    events: {
      'click .clear': 'clear',
      'click .showBatchForm': 'showBatchForm'
    },
    clear: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), batch = OP.Batch, ids = batch.ids();
      batch.clear();
      TBX.notification.show(TBX.format.sprintf(TBX.strings.batchConfirm, 0, 's'), 'flash', 'confirm');
    },
    showBatchForm: function(ev) {
      TBX.handlers.click.showBatchForm(ev);
    },
    clearCallback: function() {
      var model = TBX.init.pages.photos.batchModel;
      model.set('count', 0);
    },
    render: function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    }
  });
})(jQuery);

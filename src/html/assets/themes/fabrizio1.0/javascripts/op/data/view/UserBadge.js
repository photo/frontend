(function($){
  op.ns('data.view').UserBadge = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      this.on('afterrender', this.onAfterRender, this);
    },
    model: this.model,
    className: 'user-badge-meta',
    template    :_.template($('#user-badge-meta').html()),
    editable    : {
      '.name.edit' : {
        name: 'name',
        placement: 'bottom',
        title: 'Edit Display Name',
        validate : function(value){
          if($.trim(value) == ''){
            return 'Please enter a name';
          }
          return null;
        }
      }
    },
    onAfterRender : function(){
      if($(this.el).hasClass('userbadge-light')) $(this.el).find('[rel=tooltip]').tooltip();
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);


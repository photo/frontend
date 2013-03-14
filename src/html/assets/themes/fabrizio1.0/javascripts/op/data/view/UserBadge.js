(function($){
  op.ns('data.view').UserBadge = op.data.view.Editable.extend({
    getViewData : function(){
      return _.extend({}, this.model.toJSON(), {
        showStorage : this.options.showStorage === true
      });
    },
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
      this.on('afterrender', this.onAfterRender, this);
      // pull in the html5 data tags
      this.options = $(this.el).data();
      
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


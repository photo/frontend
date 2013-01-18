(function($){
  op.ns('data.view').AlbumCover = op.data.view.Editable.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    className: 'album-meta',
    template    :_.template($('#album-meta').html()),
    editable    : {
      '.name.edit' : {
        name: 'name',
        placement: 'top',
        title: 'Edit Album Name',
        validate : function(value){
          if($.trim(value) == ''){
            return 'Please enter a name';
          }
          return null;
        }
      }
    },
    modelChanged: function() {
      this.render();
    }
  });
})(jQuery);

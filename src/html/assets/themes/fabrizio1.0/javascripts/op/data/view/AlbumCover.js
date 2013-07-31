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
    events: {
      'click .delete': 'delete_',
      'click .share': 'share'
    },
    modelChanged: function() {
      this.render();
    },
    modelDestroyed: function() {
      var $el = $('.album-'+this.get('id'));
      $el.fadeTo('medium', .25);
    },
    delete_: function(ev) {
      ev.preventDefault();
      var $el = $(ev.target), id = $el.attr('data-id'), model = this.model, ask;
      ask = prompt('Type DELETE if you\'d like to delete the album '+model.get('name') + '.');
      if(ask === 'DELETE')
        model.destroy({success: this.modelDestroyed.bind(model), error: TBX.notification.display.generic.error});
      else
        TBX.notification.show('Your request to delete ' + model.get('name') + ' was cancelled.', 'flash', 'error');
    },
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id');
      OP.Util.makeRequest('/share/album/'+id+'/view.json', {crumb: TBX.crumb()}, TBX.callbacks.share, 'json', 'get');
    }
  });
})(jQuery);

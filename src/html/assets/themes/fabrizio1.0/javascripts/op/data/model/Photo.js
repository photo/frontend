(function($){
  op.ns('data.model').Photo = Backbone.Model.extend({
    sync: function(method, model) {
      var params = {};
      params.crumb = TBX.crumb();
      switch(method) {
        case 'update':
          //params = model.toJSON();
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              params[i] = changedParams[i];
            }
          }
          return $.post('/photo/'+model.get('id')+'/update.json', params, function(response) {
            if(response.code === 200) {
              model.trigger('change');
            } else {
              model.trigger('error');
            }
          }, 'json');
          break;
        case 'delete':
          return $.post('/photo/'+model.get('id')+'/delete.json', params, function(response) {
            if(response.code === 204) {
              model.trigger('change');
            } else {
              model.trigger('error');
            }
          }, 'json');
          break;
      }
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

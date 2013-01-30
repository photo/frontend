(function($){
  op.ns('data.model').Album = Backbone.Model.extend({
    sync: function(method, model, options) {
      options.data = {};
      options.data.crumb = TBX.crumb();
      options.data.httpCodes='*';
      switch(method) {
        case 'read':
          options.url = '/album/'+model.get('id')+'/view.json';
          break;
        case 'update':
          options.url = '/album/'+model.get('id')+'/update.json';
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              options.data[i] = changedParams[i];
            }
          }
          break;
        case 'delete':
          options.url = '/album/'+model.get('id')+'/delete.json';
          break;
      }
      return Backbone.sync(method, model, options);
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

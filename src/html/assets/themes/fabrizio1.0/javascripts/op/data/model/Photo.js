(function($){
  op.ns('data.model').Photo = Backbone.Model.extend({
    sync: function(method, model, options) {
      options.data = {};
      options.data.crumb = TBX.crumb();
      options.data.httpCodes='*';
      switch(method) {
        case 'read':
          options.url = '/photo/'+model.get('id')+'/view.json';
          break;
        case 'update':
          options.url = '/photo/'+model.get('id')+'/update.json';
          var changedParams = model.changedAttributes(), isRestore = false;
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              if(i == 'active')
                isRestore = changedParams[i];
              options.data[i] = changedParams[i];
            }
          }

          // in the case we're setting the active colum we assume it's the only changed value
          //  and call the restore/delete endpoint
          if(isRestore !== false) {
            if(isRestore == 1)
              options.url = '/photo/'+model.get('id')+'/restore.json';
            else
              options.url = '/photo/'+model.get('id')+'/delete.json';
          }
          break;
        case 'delete':
          options.url = '/photo/'+model.get('id')+'/delete.json';
          break;
      }
      return Backbone.sync(method, model, options);
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

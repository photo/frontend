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
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              if(i == 'active') {
                isRestore = changedParams[i];
              } else if(i == 'dateTaken') {
                if(changedParams[i].search(/^([+-])/) === 0) {
                  // add or subtract units. See gh-321
                  options.data[i] = phpjs.strtotime(changedParams[i], model.previous('dateTaken'));
                } else {
                  options.data[i] = phpjs.strtotime(changedParams[i]);
                }
              } else {
                options.data[i] = changedParams[i];
              }
            }
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

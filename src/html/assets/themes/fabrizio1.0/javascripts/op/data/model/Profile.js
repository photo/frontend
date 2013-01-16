(function($){
  /* ------------------------------- Profile ------------------------------- */
  op.ns('data.model').Profile = Backbone.Model.extend({
    sync: function(method, model, options) {
      options.data = {};
      options.data.crumb = TBX.crumb();
      options.data.httpCodes='*';
      switch(method) {
        case 'read':
          options.url = '/user/profile.json';
          break;
        case 'update':
          options.url = '/user/profile.json';
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              options.data[i] = changedParams[i];
            }
          }
          break;
      }
      return Backbone.sync(method, model, options);
    },
    parse: function(response) {
      return response.result;
    }
  });
})(jQuery);

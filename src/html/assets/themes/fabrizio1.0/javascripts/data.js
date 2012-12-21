/**
 * Backbone Data Models and Stores
 */

// create the openphoto namespace if it does not exist.
if( !window.op ) window.op = {};
if( !window.op.data ) window.op.data = {};


(function(exports){
  
  // create the model
  var Photo = Backbone.Model.extend({
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
          $.post('/photo/'+model.get('id')+'/update.json', params, function(response) {
            if(response.code === 200) {
              model.trigger('change');
              //views[model.get('id')].render();
            } else {
              model.trigger('error');
            }
          }, 'json');
          break;
        case 'delete':
          $.post('/photo/'+model.get('id')+'/delete.json', params, function(response) {
            if(response.code === 204) {
              model.trigger('sync');
              views[model.get('id')].remove();
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
  
  var PhotoCollection = Backbone.Collection.extend({
    model         :Photo
  });
  
  var PhotoStore = new PhotoCollection({
    localStorage  :'op-photos'
  });
  
  var PhotoGalleryView = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    tagName: 'div',   
    className: 'photo-meta',
    template    :_.template($('#photo-meta').html()),
    render      :function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    events: {
      'click .permission': 'permission'
    },
    permission: function(ev) {
      console.log('foobar');
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model/*arguments[0].view.Photos.get(id)*/, view = this;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:true});
      model.save(null, {success: function(){console.log('suc'); view.render();}, error:function(){console.log('error');}});
    },
    modelChanged: function() {
      this.render();
    }
  });

  exports.model = {
      Photo: Photo
  };
  exports.collection = {
    Photos: PhotoCollection
  };
  exports.store = {
    Photos: PhotoStore
  };
  exports.view = {
    PhotoGallery: PhotoGalleryView
  };
})(window.op.data);

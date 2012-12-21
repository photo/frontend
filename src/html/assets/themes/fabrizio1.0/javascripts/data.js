/**
 * Backbone Data Models and Stores
 */

// create the openphoto namespace if it does not exist.
Backbone.emulateHTTP = true;
Backbone.emulateJSON = true;
if( !window.op ) window.op = {};
if( !window.op.data ) window.op.data = {};


(function(exports){
  
  /* ------------------------------- Profile ------------------------------- */
  var Profile = Backbone.Model.extend({
    sync: function(method, model) {
      var params = {};
      params.crumb = TBX.crumb();
      switch(method) {
        case 'read':
          $.get('/user/profile.json', params, function(response) {
            if(response.code === 200) {
              model.save(response.result, {silent:true});
            } else {
              model.trigger('error');
            }
          }, 'json');
          break;
        case 'update':
          var changedParams = model.changedAttributes();
          for(i in changedParams) {
            if(changedParams.hasOwnProperty(i)) {
              params[i] = changedParams[i];
            }
          }
          $.post('/user/profile.json', params, function(response) {
            if(response.code === 200) {
              model.set('photoUrl', response.result.photoUrl);
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
  var ProfileNameView = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    tagName: 'h4',
    className: 'profile-name-meta',
    template    :_.template($('#profile-name-meta').html()),
    render      :function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    modelChanged: function() {
      this.render();
    }
  });
  var ProfilePhotoView = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    tagName: 'span',
    className: 'profile-photo-meta',
    template    :_.template($('#profile-photo-meta').html()),
    render      :function(){
      $(this.el).html(this.template(this.model.toJSON()));
      return this;
    },
    modelChanged: function() {
      this.render();
    }
  });
  var ProfileCollection = Backbone.Collection.extend({
    model         :Profile
  });

  /* ------------------------------- Photos ------------------------------- */
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
            } else {
              model.trigger('error');
            }
          }, 'json');
          break;
        case 'delete':
          $.post('/photo/'+model.get('id')+'/delete.json', params, function(response) {
            if(response.code === 204) {
              model.trigger('sync');
              model.remove();
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
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model/*arguments[0].view.Photos.get(id)*/, view = this;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:true});
      model.save();
    },
    modelChanged: function() {
      this.render();
    }
  });

  exports.model = {
      Photo: Photo,
      Profile: Profile
  };
  exports.collection = {
    Photos: PhotoCollection,
    Profiles: ProfileCollection
  };
  exports.store = {
    Photos: PhotoStore
  };
  exports.view = {
    PhotoGallery: PhotoGalleryView,
    ProfilePhoto: ProfilePhotoView,
    ProfileName: ProfileNameView
  };
})(window.op.data);

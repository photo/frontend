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
  
  /**
   * Override the setPosition function to correct the x coordinate
   * if it is tip extends beyond the width of the window on either side
   */
  $.fn.editableContainer.Constructor.prototype.setPosition = function(){
    (function() {    
        var $tip = this.tip()
        , inside
        , pos
        , actualWidth
        , actualHeight
        , placement
        , tp;

        placement = typeof this.options.placement === 'function' ?
        this.options.placement.call(this, $tip[0], this.$element[0]) :
        this.options.placement;

        inside = /in/.test(placement);
       
        $tip
      //  .detach()
      //vitalets: remove any placement class because otherwise they dont influence on re-positioning of visible popover
        .removeClass('top right bottom left')
        .css({ top: 0, left: 0, display: 'block' });
      //  .insertAfter(this.$element);
       
        pos = this.getPosition(inside);

        actualWidth = $tip[0].offsetWidth;
        actualHeight = $tip[0].offsetHeight;

        switch (inside ? placement.split(' ')[1] : placement) {
            case 'bottom':
                tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2};
                break;
            case 'top':
                tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2};
                break;
            case 'left':
                tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth};
                break;
            case 'right':
                tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width};
                break;
        }
        
        // check that the x position is withinin the screen
        if( tp.left < 0 ) {
          var offset = tp.left-15;
          tp.left = 5;
          $tip.find('.arrow').css({'margin-left': offset });
        }
        else if( tp.left + actualWidth > $(window).width() ){
          var offset = (tp.left+actualWidth) - ($(window).scrollLeft() + $(window).width());
          tp.left -= (offset + 5);
          $tip.find('.arrow').css({'margin-left': offset-5 });
        }
        else {
          $tip.find('.arrow').css({'margin-left': -10 });
        }
        

        $tip
        .offset(tp)
        .addClass(placement)
        .addClass('in');
        
    }).call(this.container());
  };
  
  
  var PhotoGalleryView = Backbone.View.extend({
    initialize: function() {
      this.model.on('change', this.modelChanged, this);
    },
    model: this.model,
    tagName: 'div',   
    className: 'photo-meta',
    template    :_.template($('#photo-meta').html()),
    render      :function(){
      var self = this
        , $el = $(this.el);
      
      // We need to capture the lastEditable because
      // after a successful save, render is called to
      // update the view. the $tip will be lost and the
      // "hidden" event will not fire.
      if( this.lastEditable ) {
        this.lastEditable.hide();
      }
        
      $el.html(this.template(this.model.toJSON()));
      
      // initialize the inline editor..
      this.lastEditable = $el.find('.title a ').editable({
        name: 'title',
        placement: 'top',
        title: 'Edit Title',
        url: function(params){
          
          var d = new $.Deferred;
          self.model.set(params.name, params.value, {silent:true});
          self.model.save(null, {
            success : function(){
              d.done();
            },
            error : function(){
              /**
               * TODO: should report the error, but I'm not sure what the
               * arguments / return values are.
               */
              d.reject();
            }
          });
          return d;
        }
      }).on('shown', function(){
        $el.parents('.imageContainer').addClass('editing');
        $(this).data('editable').container.setPosition();
        
        // remove the fade effect because we need to toggle the overflow
        // and it looks crappy when it gets cut off during the transition
        $(this).data('editable').container.tip().removeClass('fade');
      }).on('hidden', function(){
        $el.parents('.imageContainer').removeClass('editing');
      }).data('editable');
      
      
      return this;
    },
    events: {
      'click .permission': 'permission'
      
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model/*arguments[0].view.Photos.get(id)*/, view = this;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:false});
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

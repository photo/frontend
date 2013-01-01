(function($){
  var _instance;
  
  var DetailView = op.data.view.Editable.extend({
    initialize: function(){
      this.boundFunctions = {};
    },
    _bound : function(name){
      if( !this.boundFunctions[name] ){
        if( typeof this[name] !== 'function' ) return null;
        this.boundFunctions[name] = _.bind(this[name], this);
      }
      return this.boundFunctions[name];
    },
    template: _.template($('#op-lightbox-details').html()),
    editable: {
      '.title .text' : {
        name: 'title',
        title: 'Edit Photo Title',
        placement: 'top',
      }
    },
    setModel: function(model){
      if( this.model ){
        // stop listening
        this.model.off('change', this._bound('render'));
      }
      this.model = model;
      this.model.on('change', this._bound('render'));
      this.render();
    },
    
    render : function(){
      DetailView.__super__.render.call(this);
      // capture any editables since the document listener will close
      // everything
    }
  });
  
  var Lightbox = function(){
    
    // we are going to use the global photo store.
    this.boundFunctions = {};
    this.store = op.data.store.Photos;
    this.template = _.template($('#op-lightbox').html());
    this._initView();
    this._initEvents();
    
  };
  
  _.extend( Lightbox.prototype, {
    
    keys : {
      // we may want to rethink some of these key codes...
      // i think down and up may be good to toggle details / thumbs
      next      :[13, 34, 39, 40],
      prev      :[ 8, 33, 37, 38],
      hide      :[27],
      togglePlay:[32]
    },
    
    _indexOf : function(model){
      return _.indexOf(this.store.models, model);
    },
    
    _bound : function(name){
      if( !this.boundFunctions[name] ){
        if( typeof this[name] !== 'function' ) return null;
        this.boundFunctions[name] = _.bind(this[name], this);
      }
      return this.boundFunctions[name];
    },
    
    _initView : function(){
      
      this.$el = $( this.template() )
        .appendTo($(document.body))
        .hide()
        .fadeIn('fast')
        
      this.detailView = new DetailView({el: this.$el.find('.details')} );
    },
    
    _initEvents : function(){
      this.$el.click( this._bound('onContainerClick') );
    },
    
    _captureDocumentEvents : function(){
      $(document).on('keydown.oplightbox', this._bound('keydown'));
    },
    
    _releaseDocumentEvents : function(){
      $(document).off('keydown.oplightbox', this._bound('keydown'));
    },
    
    onContainerClick : function(e){
      if( e.target === this.$el[0] ) this.hide();
    },
    
    keydown : function(e){
      var code = e.which || e.keyCode
        , target = e.target || e.srcElement
        , self = this
        
      // Ignore key combinations and key events within form elements
	  if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey && !(target && (target.type || $(target).is('[contenteditable]')))) {
        $.each(self.keys, function(fn, codes){
          if( ~_.indexOf(codes, code) ){
            self[fn]();
            return false;
          }
          return true;
        });
      }
    },
    
    show : function(item){
      this._captureDocumentEvents();
      this.$el.fadeIn('fast');
      return this;
    },
    
    hide : function(){
      this._releaseDocumentEvents();
      this.$el.fadeOut('fast');
      return this;
    },
    
    update : function(model){
      this.detailView.setModel( model );
      // we need to remove our listeners and add then add them
      // so the editable document events can be captured first
      this._releaseDocumentEvents();
      this._captureDocumentEvents();
      return this;
    },
    
    togglePlay : function(){
      // TODO - implement slideshow playing state.
      return this;
    }
  });
  
  Lightbox.getInstance = function(){
    if( _instance === undefined ){
      _instance = new Lightbox();
    }
    return _instance;
  }
  
  Lightbox.open = function(ev){
    ev.preventDefault();
    
    // get the item from the store
    var id = $(ev.currentTarget).attr('data-id')
      , model = op.data.store.Photos.get(id);
      
    if( !model ) return $.error('No image in store with id '+id);
      
    return Lightbox.getInstance().update(model).show();
    
  };
  
  op.Lightbox = Lightbox;
  
  $(document).on('click', '.photo-view-modal-click', Lightbox.open);
  
})(jQuery);
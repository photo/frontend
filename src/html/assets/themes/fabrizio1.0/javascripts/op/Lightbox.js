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
      },
      '.description .text' : {
        name: 'description',
        type: 'textarea',
        title: 'Edit Photo Description',
        placement: 'top',
        emptytext: 'Click to add a description'
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
    events : {
      'click .permission.edit': 'permission'
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:false});
      model.save();
    }
  });
  
  var Lightbox = function(){
    
    // we are going to use the global photo store.
    this.boundFunctions = {};
    this.cache = {};
    this.store = op.data.store.Photos;
    this.template = _.template($('#op-lightbox').html());
    this._initialized = false;
  };
  
  _.extend( Lightbox.prototype, Backbone.Events, {
	
    imagePathKey : 'pathBase',
    //imagePathKey : 'pathOriginal',
    
    _initialize : function(){
      if( this._initialized ) return;
      this._initView();
      this._initEvents();
      this._initialized = true;
    },
    
    keys : {
      // we may want to rethink some of these key codes...
      // i think down and up may be good to toggle details / thumbs
      next      	  :[13, 34, 39, 40],
      prev      	  :[ 8, 33, 37, 38],
      hide      	  :[27],  // escape
      togglePlay	  :[32],  // spacebar 
      toggleDetails :[68]   // D
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
        
      this.detailView = new DetailView({el: this.$el.find('.details .container')} );
    },
    
    _initEvents : function(){
      this.$el.click( this._bound('onContainerClick') );
      this.$el.find('.photo .nav .prev').click( this._bound('prev'));
      this.$el.find('.photo .nav .next').click( this._bound('next'));
      this.$el.find('.details .toggle').click( this._bound('toggleDetails'));
    },
    
    _captureDocumentEvents : function(){
      $(document).on({
        'keydown.oplightbox'	:this._bound('keydown')
      });
      $(window).on({
        'resize.oplightbox'		:this._bound('adjustSize')
      })
    },
    
    _releaseDocumentEvents : function(){
      $(document).off('.oplightbox');
      $(window).off('.oplightbox');
    },
    
    onContainerClick : function(e){
      if( e.target === this.$el[0] ) this.hide();
      if( $(e.target).parent()[0] == this.$el[0] ) this.hide();
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
	
    adjustSize : function(){
      var $photo = this.$el.find('.photo');
      
      // check for the image
      if( (c =this.cache[this.model.get('id')]) && c._loaded ){
      var iw = c.width
        , ih = c.height
        , ir = iw / ih
        , cw = $(window).width()
        , ch = $(window).height() - this.$el.find('.bd').position().top
        , cr = cw / ch
      
      if( iw < cw && ih < ch ){
        $photo.width(iw);
        $photo.height(ih);
      }
      else {
        $photo.css( cr > ir ? {width: iw * ch/ih, height: ch} : {width: cw, height: ih * cw/iw} );
      }
      }
      else {
        $photo.css({'height': ( $(window).height() - this.$el.find('.bd').position().top )+'px'} );
      }
    },
    
    show : function(item){
      this._initialize();
      this._captureDocumentEvents();
      this.$el.fadeIn('fast');
      this.adjustSize();
      return this;
    },
    
    hide : function(){
      this._releaseDocumentEvents();
      this.$el.fadeOut('fast');
      return this;
    },
    
    update : function(model){
      this._initialize();
      this.$el.addClass('loading');
      this.setModel( model );
      this.$el.find('.photo').find('img').remove();
      this.loadImage();
      return this;
    },
	
    setModel : function(model){
      this.model = model;
      this.trigger('updatemodel', model);
      this.detailView.setModel( model );
      this.loadImage();
      this.$el.find('.header .detail-link').attr('href', model.get('url'));
    },
	
    _imageLoaded : function(id){
      var c = this.cache[id];
      c._loaded = true;
      if( this.model.get('id') != id ) return;
      this.$el.removeClass('loading');
      this.$el.find('.photo img').remove();
      $('<img />').attr('src', $(c).attr('src')).hide().appendTo(this.$el.find('.photo')).fadeIn('fast');
      this.adjustSize();
    },
    
    loadImage : function(){
      var c;
      this.$el.find('.photo img').remove();
      this.$el.addClass('loading');
      if( !(c = this.cache[this.model.get('id')]) ){
        var c = this.cache[this.model.get('id')] = new Image();
        c.onload = _.bind(this._imageLoaded, this, this.model.get('id'));
        c.src = this.model.get(this.imagePathKey);
        c._loaded = true;
      }
      else if( c._loaded ){
        this._imageLoaded(this.model.get('id'));
      }
    },
    
    prev : function(){
      var i = _.indexOf( this.store.models, this.model ) - 1;
      if( i < 0 ) i = this.store.models.length-1;
      this.go(i);
    },
    
    next : function(){
      var i = _.indexOf( this.store.models, this.model ) + 1;
      if( i > this.store.models.length-1 ) i = 0;
      this.go(i);
    },
    
    go : function( index ){
      this.setModel( this.store.models[index] );
    },
    
    togglePlay : function(){
      // TODO - implement slideshow playing state.
      return this;
    },
    
    toggleDetails : function(){
      this.$el.toggleClass('details-hidden');
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
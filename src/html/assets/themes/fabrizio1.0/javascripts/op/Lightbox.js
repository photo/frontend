(function($){
  var _instance, addTagSearch;

  addTagSearch = function() {
    var $el = $('.editable-container .tags-inline-input')
    new op.data.view.TagSearch({el: $el});
  };
  
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
      '.title.edit' : {
        name: 'title',
        title: 'Edit Photo Title',
        placement: 'top',
      },
      '.description.edit' : {
        name: 'description',
        type: 'textarea',
        title: 'Edit Photo Description',
        placement: 'top',
        emptytext: 'Click to add a description'
      },
      '.tags.edit' : {
        name: 'tags',
        title: 'Edit Tags',
        placement: 'top',
        emptytext: '',
        inputclass: 'tags-inline-input tags',
        shown: addTagSearch
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
      'click .permission.edit': 'permission',
      'click .rotate': 'rotate',
      'click .share': 'share'
    },
    permission: function(ev) {
      ev.preventDefault();
      var el = $(ev.currentTarget), id = el.attr('data-id'), model = this.model;
      model.set('permission', model.get('permission') == 0 ? 1 : 0, {silent:false});
      model.save();
    },
    rotate: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), model = this.model, id = model.get('id'), size = 'Base', value='90';
      OP.Util.makeRequest('/photo/'+id+'/transform.json', {crumb: TBX.crumb(),rotate:value,generate:'true'}, TBX.callbacks.rotate.bind({model: model, id: id, size: size}), 'json', 'post');
    },
    share: function(ev) {
      ev.preventDefault();
      var $el = $(ev.currentTarget), id = $el.attr('data-id'), router = op.data.store.Router;
      OP.Util.makeRequest('/share/photo/'+id+'/view.json', {crumb: TBX.crumb()}, function(response) {
        router.navigate(op.Lightbox.prototype._path, {trigger: true});
        TBX.callbacks.share(response);
      }, 'json', 'get');
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
    
    _initialize : function(){
      if( this._initialized ) return;
      this._initView();
      this._initEvents();
      this._initialized = true;
    },
    
    keys : {
      // we may want to rethink some of these key codes...
      // i think down and up may be good to toggle details / thumbs
      next      	  :[34, 39, 40, 74], // up, down, j
      prev      	  :[33, 37, 38, 75], // left, up, k
      title         :[84],  // t
      tags          :[71],  // g
      description   :[68],  // d
      privacy    	  :[80],  // p
      hide      	  :[27],  // escape
      togglePlay	  :[32]  // spacebar 
      //toggleDetails :[68]   // d
    },
    
    _path: location.pathname,
    _filter: location.pathname.replace('/p/', '/').replace('/photos/', '/').replace('/list', ''),
    _visible: false,

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
      this.$el.find('.photo').click( this._bound('nextIfImage'));
      this.$el.find('.photo .nav .prev').click( this._bound('prev'));
      this.$el.find('.photo .nav .next').click( this._bound('next'));
      this.$el.find('.details .toggle').click( this._bound('toggleDetails'));
      this.$el.find('.detail-link').click( this._bound('viewDetailPage'));
    },
    
    _captureDocumentEvents : function(){
      $(document).on({
        'keyup.oplightbox'	:this._bound('keyup')
      });
      $(window).on({
        'resize.oplightbox'		:this._bound('adjustSize')
      });
    },
    
    _releaseDocumentEvents : function(){
      $(document).off('.oplightbox');
      $(window).off('.oplightbox');
    },
    
    onContainerClick : function(e){
      if( e.target === this.$el[0] ) this.hide();
      if( $(e.target).parent()[0] == this.$el[0] ) this.hide();
    },
    
    keyup : function(e){
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
        /*
         * iw = image width, ih = image height, ir = image ratio
         * cw = chrome width, ch = chrome height, cr = chrome ratio
         */
        var iw = c.width
          , ih = c.height
          , ir = iw / ih
          , cw = $(window).width()
          , ch = $(window).height() - this.$el.find('.bd').position().top
          , cr = cw / ch;
        
        // if the image is narrower and the height is shorter than the chrome
        //  then set the div's width to the images dimensions
        // else
        //  if the chrome ratio (width) is greater than the image
        //    set the width to the image width * chrome height / image height
        //    set the height to the chrome height
        //  else
        //    set the height to the image height * chrome width / image width
        //    set the width to the chrome width
        if( iw < cw && ih < ch ){
          $photo.width(iw);
          $photo.height(ih);
        } else {
          $photo.css( cr > ir ? {width: iw * ch/ih, height: ch} : {width: cw, height: ih * cw/iw} );
        }
      } else {
        $photo.css({'height': ( $(window).height() - this.$el.find('.bd').position().top )+'px'} );
      }
    },

    isOpen: function() {
      if(this.$el)
        return true;
      return false;
    },
    
    show : function(item){
      this._initialize();
      this._visible = true;
      this._captureDocumentEvents();
      this.$el.fadeIn('fast');
      this.adjustSize();
      return this;
    },
    
    hide : function(){
      var router = op.data.store.Router, $title = $('title');
      this._releaseDocumentEvents();
      this._visible = false;
      this.$el.fadeOut('fast');
      // reset the title back to the "original" title of the page
      $title.html($title.attr('data-original'));
      router.navigate(this._path, {silent:true});
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
      //this.$el.find('.header .detail-link').attr('href', model.get('url'));
      this._preloadNextPrevious(model);
    },
	
    _imageLoaded : function(id){
      var c = this.cache[id];
      c._loaded = true;
      if( this.model.get('id') != id ) return;
      this.$el.removeClass('loading');
      this.$el.find('.photo img').remove();
      $('<img />').attr('class', 'photo-img-'+id).attr('src', $(c).attr('src')).hide().appendTo(this.$el.find('.photo')).fadeIn('fast');
      this.adjustSize();
    },

    _preloadNextPrevious : function(model) {
      var next, previous, photos = [];
      previous = this.store.at(this.store.indexOf(model) - 1);
      next = this.store.at(this.store.indexOf(model) + 1);

      if(typeof(previous) !== 'undefined') {
        photos.push(previous.get(this.imagePathKey));
      }
      if(typeof(next) !== 'undefined') {
        photos.push(next.get(this.imagePathKey));
      }

      if(photos.length > 0) {
        OP.Util.fire('preload:photos', photos);
      }
    },
    
    loadImage : function(){
      var c, $title = $('title');
      this.$el.find('.photo img').remove();
      this.$el.addClass('loading');
      this.$el.find('.photo')
        .width($(window).width())
        .height(($(window).height() - this.$el.find('.bd').position().top )+'px');
        
      // set the title to include the photo's title
      $title.html(TBX.format.sprintf('%s / Photo / %s / Trovebox', TBX.profiles.getOwnerUsername(), this.model.get('title') || this.model.get('filenameOriginal')));

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
    
    prev : function(ev){
      if(ev !== undefined && ev.preventDefault) ev.preventDefault();
      var i = _.indexOf( this.store.models, this.model ) - 1, router = op.data.store.Router, id;
      if( i < 0 ) i = this.store.models.length-1;
      id = this.store.models[i].get('id');
      if( !$('body').hasClass('photo-details') ){
        router.navigate('/p/'+id+this._filter, {trigger: false});
      }
      this.go(i);
    },
    
    next : function(ev){
      if(ev !== undefined && ev.preventDefault) ev.preventDefault();
      var i = _.indexOf( this.store.models, this.model ) + 1, router = op.data.store.Router, id;
      // at the end, load some more synchronously
      if( i > this.store.models.length-1 ) {
        TBX.init.pages.photos.load(false);
      }

      // we check the length again since we append above
      // once we reach the end the appending stops so this works
      if( i < this.store.models.length ) {
        if( !$('body').hasClass('photo-details') ){
          id = this.store.models[i].get('id');
          router.navigate('/p/'+id+this._filter, {trigger: false});
        }
        this.go(i);
      }
    },

    nextIfImage : function(ev) {
      var el = ev.target;
      if(el.tagName === 'IMG') {
        ev.stopPropagation();
        this.next(ev);
      }
    },

    viewDetailPage : function(ev) {
      ev.preventDefault();
      var id = this.model.get('id');
      location.href = '/p/'+id+this._filter;
    },

    tags: function(ev) {
      var $tagsEl = $('a.tags.editable-click', this.$el);
      $tagsEl.trigger('click');
      //new op.data.view.TagSearch({el: $inputEl});
    },

    description: function(ev) {
      $('a.description.editable-click', this.$el).trigger('click');
    },

    title: function(ev) {
      $('a.title.editable-click', this.$el).trigger('click');
    },

    privacy: function(ev) {
      $('.permission', this.$el).trigger('click');
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
    },

    open: function(arg) {
      var id, model;
      if(typeof(arg) === 'event') {
        arg.preventDefault();
        id = $(arg.currentTarget).attr('data-id');
      } else {
        id = arg;
      }
      
      // get the item from the store
      model = op.data.store.Photos.get(id);
        
      if( !model ) return $.error('No image in store with id '+id);
        
      return this.update(model).show();
    }
  });
  
  Lightbox.getInstance = function(){
    if( _instance === undefined ){
      _instance = new Lightbox();
    }
    return _instance;
  }
  
  op.Lightbox = Lightbox;
})(jQuery);

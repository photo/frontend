(function($){
  
  op.ns('data.view').Editable = Backbone.View.extend({
    
    getViewData : function(){
      return this.model.toJSON();
    },
    
    render : function(){
      
      var self = this;
      
      // whenever this is re-rendered, check for any old editables
      // and hide them in case the tip is open.
      if( this.editable ) for(var i in this.editable){
        var e;
        if( this.editable.hasOwnProperty(i) && this.el && (e = $(this.el).find(i).data('editable')) ){
          e.hide();
        }
      }
      
      this.trigger('beforerender', this);
      
      $(this.el).html(this.template(this.getViewData()));
      
      if( !this.editable ){
        this.trigger('afterrender', this);
        return this;
      }
      
      for( var i in this.editable ){
        if( this.editable.hasOwnProperty(i) ){
          
          var $el = $(this.el).find(i);
          
          if( $el.length === 0 ) continue;
          
          var config = _.extend({
            placement: 'top',
            url : function(params){
              var d = new $.Deferred;
              self.model.set(params.name, params.value, {silent:true});
              self.model.save(null, {
                success : function(){
                  d.done();
                },
                error : function( TODO ){
                  /**
                   * TODO: should report the error, but I'm not sure what the
                   * arguments / return values are.
                   */
                  d.reject();
                }
              });
              return d;
            }
          }, this.editable[i]);

          if(typeof(this.editable[i].shown) !== "undefined") {
            $el.on('shown', this.editable[i].shown);
          }
          
          if( $el.find('.value').length ){
            config.value = $el.find('.value').text();
          }
          
          // grab the "on" property
          var on = config.on;
          
          delete config.on;
          $el.editable(config);
          $el.data('editable').view = self;
          
          if( on ) $el.on( on );
        }
      }
      this.trigger('afterrender', this);
      return this;
    }
  });
  
})(jQuery);
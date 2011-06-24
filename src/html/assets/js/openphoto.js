var op = (function(){
  var _this = {
    handlers: {
      photoLink: function(event) {
        var el = this;
        if(event.type == 'click') {
          console.log(el);
        } else if(event.type == 'mouseover') {
          console.log('mousover');
        }
      }
    }
  };
  return {
    init: {
      attach: function() {
        $('.photo-link').live('click mouseover', handlers.photoLink);
      }
    }
  };
})();

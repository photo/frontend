var opTheme = (function() {
  var log = function(msg) {
    if(console !== undefined && console.log !== undefined)
      console.log(msg);
  };
  return{
    init: {
      attach: function(PhotoSwipe) {
        $('div.gallery-page').live('pageshow', function(e){
          var
            currentPage = $(e.target),
            photoSwipeInstanceId = parseInt(Math.random()*10000),
            photoSwipeInstance = PhotoSwipe.getInstance(photoSwipeInstanceId)
            options = {};

          if ($("ul.gallery a").length > 0 && (typeof photoSwipeInstance === "undefined" || photoSwipeInstance === null)) {
            photoSwipeInstance = $("ul.gallery a", e.target).photoSwipe(options, photoSwipeInstanceId);
          }
          return true;
        });
      }
    }
  };
}());

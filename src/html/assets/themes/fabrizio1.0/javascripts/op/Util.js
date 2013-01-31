(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Util() {
    this.fetchAndCache = function(src) {
      $('<img />').attr('src', src).appendTo('body').css('display', 'none').on('load', function(ev) { $(ev.target).remove(); });
    };
  }

  TBX.util = new Util;
})(jQuery);

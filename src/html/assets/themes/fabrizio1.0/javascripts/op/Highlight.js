(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Hightlight() {
    var highlightColor = '#fff7dc', startColor;
    reset = function() {
      $(this).animate({'backgroundColor':startColor}, 'slow');
    }

    this.run = function(el) {
      var $el = $(el);

      startColor = $el.css('backgroundColor');
      $el.animate({backgroundColor: highlightColor}, 'slow', 'swing', reset.bind($el));
    }
  }
  
  TBX.highlight = new Hightlight;
})(jQuery);


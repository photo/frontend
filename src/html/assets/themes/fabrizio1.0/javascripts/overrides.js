// set Backbone defaults
Backbone.emulateHTTP = true;
Backbone.emulateJSON = true;

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
      
      // check to make sure that the y position is not off the top of the screen
      if( tp.top < $(window).scrollTop() ){
        // change the placement to the bottom
        tp.top = pos.top + pos.height;
        placement = 'bottom';
      }
      

      $tip
      .offset(tp)
      .addClass(placement)
      .addClass('in');
      
  }).call(this.container());
};
 
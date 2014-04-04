(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Waypoints() {
    this.add = function($el, func) {
      $el.waypoint(func.bind($el)).addClass('waypoint-added');
      console.log('waypoint added to ' + $el.attr('class'));
    };
  }

  TBX.waypoints = new Waypoints;
})(jQuery);


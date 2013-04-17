(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  var q = [], fired = false, introObj, enabled, enabledState;
  function Tutorial() {
    this.queue = function(step, selector, intro, key, section, width, init) {
      if(!enabled())
        return;

      // step = numeric value always starts at 1
      // selector = css selector for the element
      // intro = text to display to user
      // key = key from the ini file
      // section = section from the ini file
      q.push({step:step, selector: selector, intro:intro, key: key, section: section, width: width});
      if(init && fired === false) {
        OP.Util.fire('tutorial:run');
        fired = true;
      }
    };

    this.run = function() {
      if(!enabled())
        return;

      var qObj, el;
      introObj = introJs();
      for(i=0; i<q.length; i++) {
        qObj = q[i];
        el = $(qObj.selector);
        el.attr('data-intro', qObj.intro).attr('data-step', qObj.step);
        if(typeof(qObj.width) !== "undefined")
          el.attr('data-width', qObj.width);
      }
      introObj.complete(TBX.handlers.custom.tutorialUpdate.bind(qObj));
      introObj.start();
    };
    
    // we only enable this is the info element in the navigation is visible
    //  we hide it using hidden-phone
    enabled = function() {
      if(typeof(enabledState) === "undefined") {
        enabledState = $('.navbar-inner-secondary li.info').is(':visible');
      }
      return enabledState;
    }
  }
  TBX.tutorial = new Tutorial;
})(jQuery);
OP.Util.on('tutorial:run', TBX.tutorial.run);


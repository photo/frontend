(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};
  
  var q = [], i;
  function Tutorial() {
    this.queue = function(step, selector, intro, key, section) {
      // step = numeric value always starts at 1
      // selector = css selector for the element
      // intro = text to display to user
      // key = key from the ini file
      // section = section from the ini file
      q.push({step:step, selector: selector, intro:intro, key: key, section: section});
    };

    this.run = function() {
      var qObj;
      introObj = introJs();
      for(i=0; i<q.length; i++) {
        qObj = q[i];
        $(qObj.selector).attr('data-intro', qObj.intro).attr('data-step', qObj.step);
      }
      introObj.complete(TBX.handlers.custom.tutorialUpdate.bind(qObj));
      introObj.start();
    };
  }
  TBX.tutorial = new Tutorial;
})(jQuery);


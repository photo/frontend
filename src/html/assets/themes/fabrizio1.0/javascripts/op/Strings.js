(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Strings() {
    this.batchConfirm = 'Your batch queue has been updated and now contains %s photo%s.';
  }
  
  TBX.strings = new Strings;
})(jQuery);

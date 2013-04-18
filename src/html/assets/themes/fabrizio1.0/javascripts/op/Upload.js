(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Upload() {
    var dropzone;
    this.init = function() {
      dropzone = new Dropzone('form.dropzone', {
        url: '/photo/upload.json',
        //parallelUploads: 5,
        paramName: 'photo',
        //clickable: false,
        previewsContainer: 'form.dropzone .preview-container',
        enqueueForUpload: false


      });
    };
    this.start = function() {
      dropzone.processQueue();
    }
  }
  
  TBX.upload = new Upload;
})(jQuery);

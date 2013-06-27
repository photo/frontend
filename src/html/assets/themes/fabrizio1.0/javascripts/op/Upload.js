(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Upload() {
    var dropzone, total = 0, duplicateCache = {}, completeObj = {success: 0, duplicate: 0, failed: 0}, confirmationMessage = 0;

    var fileFinishedHandler = function(file, response) {
      var message, previewElement = $(file.previewElement);
      switch(response.code) {
        case 201:
          completeObj.success++;
          $(".dz-details>img", previewElement).attr('src', response.result.path100x100xCR).css('display', 'block');
          break;
        case 409:
          $(".dz-details>img", previewElement).attr('src', response.result.path100x100xCR).css('display', 'block');
          completeObj.duplicate++;
          break;
        default:
          completeObj.failed++;
          break;
      }

      if(completeObj.duplicate === 0)
        message = TBX.format.sprintf('Your upload progress: total -> %s, successful -> %s, failed -> %s', total, completeObj.success, completeObj.failed);
      else
        message = TBX.format.sprintf('Your upload progress: total -> %s, successful -> %s, duplicate -> %s, failed -> %s', total, completeObj.success, completeObj.duplicate, completeObj.failed);

      if(confirmationMessage === 0)
        TBX.notification.show(message);
      else
        
      confirmationMessage = 1;
    };
    this.init = function() {
      dropzone = new Dropzone('form.dropzone', {
        url: '/photo/upload.json',
        parallelUploads: 5,
        paramName: 'photo',
        maxThumbnailFilesize: 1,
        //clickable: true,
        acceptedMimeTypes: 'image/jpg,image/jpeg,image/png,image/gif,image/tiff',
        /*accept: function (file, done) {
          if('image/jpg,image/jpeg,image/png,image/gif,image/tiff'.search(file.type) !== -1) {
            done(); 
          } else { 
            done("Invalid file type."); 
          } 
        },*/
        previewsContainer: 'form.dropzone .preview-container',
        enqueueForUpload: false
      });
      
      dropzone.on("addedfile", function(file) {
        // check if the file is already queued
        if(typeof(duplicateCache[file.name]) !== "undefined") {
          dropzone.removeFile(file);
          return;
        }

        total++;
        duplicateCache[file.name] = 1;
        dropzone.filesQueue.push(file);
        TBX.notification.show(TBX.format.sprintf('Your upload queue has %s %s pending', dropzone.filesQueue.length, TBX.format.plural('photo', dropzone.filesQueue.length)));
      });
      dropzone.on("success", function(file, response) { fileFinishedHandler(file, response); });
      dropzone.on("error", function(file) { fileFinishedHandler(file, {"code":500}); });
    };
    this.start = function() {
      dropzone.processQueue();
    }
  }
  
  TBX.upload = new Upload;
})(jQuery);

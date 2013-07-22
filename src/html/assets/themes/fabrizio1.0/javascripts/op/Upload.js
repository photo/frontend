(function($) {
  if(typeof TBX === 'undefined')
    TBX = {};

  function Upload() {
    var dropzone, total = 0, ids = [], duplicateCache = {}, completeObj = {success: 0, duplicate: 0, failed: 0, completed: 0}, progressModel, $form = $('form.upload');

    var fileFinishedHandler = function(file, response) {
      var message, $previewElement = $(file.previewElement);
      switch(response.code) {
        case 201:
          completeObj.success++;
          $(".dz-details>img", $previewElement).attr('src', response.result.path100x100xCR).css('display', 'block');
          break;
        case 409:
          $(".dz-details>img", $previewElement).attr('src', response.result.path100x100xCR).css('display', 'block');
          completeObj.duplicate++;
          break;
        default:
          completeObj.failed++;
          break;
      }

      completeObj.completed++;
      ids.push(response.result.id);

      if(completeObj.completed === total) {
        progressModel.completed();

        if(completeObj.failed === 0) {
          if(completeObj.duplicate === 0)
            message = 'Your photos have been successfully uploaded. %s';
          else
            message = TBX.format.sprintf('Your photos have been successfully uploaded (%s %s).', completeObj.duplicate, TBX.format.plural('duplicate', completeObj.duplicate)) + ' %s';

          TBX.notification.show(TBX.format.sprintf(message, TBX.format.sprintf('<a href="/photos/ids-%s/list">View or edit your photos</a>.', ids.join(','))));
        } else {
          TBX.notification.show('There was a problem uploading your photos. Please try again.', 'static', 'error');
        }

        $('button i', $form).remove();
      }

      progressModel.set('success', percent(completeObj.success, total));
      progressModel.set('warning', percent(completeObj.duplicate, total));
      progressModel.set('danger', percent(completeObj.failed, total));
    };
    var fileSending = function() {
      if(typeof(progressModel) === "undefined") {
        // insert the progress container
        $el = $('.progress-upload');
        // create the progress model
        progressModel = new op.data.model.ProgressBar();
        // insert the view and render it in place
        (new op.data.view.ProgressBar({model: progressModel, el: $el})).render();
        $('button', $form).prepend('<i class="icon-spinner icon-spin"></i> ');
      }
    };
    var percent = function(numerator, denominator) {
      if(denominator === 0)
        return 0;
      return (numerator / denominator) * 100;
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
      dropzone.on("sending", fileSending);
    };
    this.start = function() {
      dropzone.processQueue();
    }
  }
 
  TBX.upload = new Upload;
})(jQuery);

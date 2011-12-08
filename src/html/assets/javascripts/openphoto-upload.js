/**
* Utility methods for OpenPhoto - this will include the entire framework
* And ability to swap frameworks (YUI or jQuery) in and out.
*/
OPU = (function() {
  return {
      init: function() {
        var uploaderEl = $("#uploader");
        if(uploaderEl.length == 0)
          return;

        uploaderEl.pluploadQueue({
            // General settings
            runtimes : 'html5',
            url : '/photo/upload.json',
            max_file_size : '32mb',
            //chunk_size : '1mb',
            unique_names : true,
     
            // Specify what files to browse for
            filters : [
                {title : "Photos", extensions : "jpg,jpeg,gif,png"}
            ],
     
            // Flash settings
            flash_swf_url : 'plupload.flash.swf',
            multipart_params:{
              crumb: $("form.upload input.crumb").val()
            },
            preinit: {
              BeforeUpload: function() {
                var uploader = $("#uploader").pluploadQueue();
                $(".upload-progress .total").html(uploader.files.length);
                $(".upload-progress .completed").html(uploader.total.uploaded+1);
                $(".upload-progress").slideDown('fast');
              },
              UploadComplete: function() {
                $(".upload-progress").fadeOut('fast', function() { $(".upload-complete").fadeIn('fast'); });
              },
              UploadFile: function() {
                var uploader = $("#uploader").pluploadQueue(), license, permission, tags;
                license = $("form.upload select[name='license'] :selected").val();
                if(license.length == 0)
                  license = $("form.upload input[name='custom']").val();
                tags = $("form.upload input[name='tags']").val();
                permission = $("form.upload input[name='permission']:checked").val();
                
                uploader.settings.multipart_params.license = license;
                uploader.settings.multipart_params.tags = tags;
                uploader.settings.multipart_params.permission = permission;
              }
            }
        });
     
        // Client side form validation
        var uploadForm = $("form.upload");
        uploadForm.submit(function(e) {
          var uploader = $('#uploader').pluploadQueue({});
          // Files in queue upload them first
          if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
              if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                $("form.upload")[0].submit();
              }
            }); 
            uploader.start();
          } else {
            // TODO something that doesn't suck
            alert('Please select at least one photo to upload.');
          }
   
          return false;
        });

        var insufficient = $("#uploader .insufficient");
        if(insufficient.length == 1)
          insufficient.show();
      }
    };
}());

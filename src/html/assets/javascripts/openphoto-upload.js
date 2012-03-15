/**
* Upload utility for OpenPhoto.
* Supports drag/drop with plupload
*/
OPU = (function() {
  var sortByFilename = function(a, b) {
    var aName = a.name;
    var bName = b.name;
    return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
  };
  return {
      init: function() {
        var uploaderEl = $("#uploader");
        if(uploaderEl.length == 0)
          return;

        uploaderEl.pluploadQueue({
            // General settings
            runtimes : 'html5',
            url : '/photo/upload.json?httpCodes=500,403,404', // omit 409 since it's somewhat idempotent
            max_file_size : '32mb',
            file_data_name : 'photo',
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
              FilesAdded: function(uploader, files) {
                var queue = uploader.files.concat(files);
                queue.sort(sortByFilename);
                uploader.files = queue;
              },
              UploadComplete: function(uploader, files) {
                var i, file, failed = 0, total = 0;
                for(i in files) {
                  if(files.hasOwnProperty(i)) {
                    total++;
                    file = files[i];
                    if(file.status !== plupload.DONE)
                      failed++;
                  }
                }
                if(failed === 0) {
                  OP.Util.fire('upload:complete-success');
                } else {
                  OP.Util.fire('upload:complete-error');
                }

              },
              UploadFile: function() {
                var uploader = $("#uploader").pluploadQueue(), license, permission, tags;
                license = $("form.upload select[name='license'] :selected").val();
                tags = $("form.upload input[name='tags']").val();
                permission = $("form.upload input[name='permission']:checked").val();
                
                uploader.settings.multipart_params.license = license;
                uploader.settings.multipart_params.tags = tags;
                uploader.settings.multipart_params.permission = permission;
              }
            }
        });

        OP.Util.on('click:upload-start', function() {
          var uploader = $("#uploader").pluploadQueue();
          uploader.start();
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

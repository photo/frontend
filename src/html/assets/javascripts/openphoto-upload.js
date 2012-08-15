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
  var photosUploaded = {success: [], failure: [], duplicate: [], ids: []};
  var log = function(msg) {
    if(typeof(console) !== 'undefined' && typeof(console.log) !== 'undefined')
      console.log(msg);
  };

  return {
      init: function() {
        var uploaderEl = $("#uploader");
        if(uploaderEl.length == 0)
          return;
     
        if(typeof(uploaderEl.pluploadQueue) == 'undefined') {
          $("#uploader .insufficient").show();
          return;
        }

        uploaderEl.pluploadQueue({
            // General settings
            runtimes : 'html5',
            url : '/photo/upload.json', // omit 409 since it's somewhat idempotent
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
              FileUploaded: function(uploader, file, response) {
                var apiResponse = $.parseJSON(response.response),
                    code = apiResponse.code,
                    result = apiResponse.result;
                if(code === 201) {
                  log('Successfully uploaded ' + file.name + ' at ' + result.url);
                  photosUploaded.success.push(result);
                  photosUploaded.ids.push(result.id);
                } else if(code === 409) {
                  log('Detected a duplicate of ' + file.name);
                  photosUploaded.duplicate.push(result);
                  photosUploaded.ids.push(result.id);
                } else {
                  log('Unable to upload ' + file.name);
                  photosUploaded.failure.push(file.name);
                }
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
                  OP.Util.fire('upload:complete-success', photosUploaded);
                } else {
                  OP.Util.fire('upload:complete-error', photosUploaded);
                }

              },
              UploadFile: function() {
                var uploader = $("#uploader").pluploadQueue(), 
                    form = $('form.upload'),
                    license = $("select[name='license'] :selected", form).val(),
                    permission = $("input[name='permission']:checked", form).val(),
                    albums = $("select[name='albums']", form).val(),
                    tags = $("input[name='tags']", form).val(), 
                    // http://stackoverflow.com/a/6116631
                    // groups = $("input[name='groups[]']:checked", form).map(function () {return this.value;}).get().join(",");
                    groups = $("select[name='groups']", form).val();

                if(typeof(albums) === "undefined")
                  albums = '';
                else if(albums !== null)
                  albums = albums.join(',');

                if(typeof(groups) === "undefined")
                  greoups = '';
                else if(groups !== null)
                  groups = groups.join(',');
                
                uploader.settings.multipart_params.license = license;
                uploader.settings.multipart_params.tags = tags;
                uploader.settings.multipart_params.permission = permission;
                uploader.settings.multipart_params.albums = albums;
                uploader.settings.multipart_params.groups = groups;
              }
            }
        });

        OP.Util.on('submit:photo-upload', function(ev) {
          ev.preventDefault();
          var uploader = $("#uploader").pluploadQueue();
          if (typeof(uploader.files) != 'undefined' && uploader.files.length > 0) {
            uploader.start();
          } else {
            // TODO something that doesn't suck
            opTheme.message.error('Please select at least one photo to upload.');
          }
        });
      }
    };
}());

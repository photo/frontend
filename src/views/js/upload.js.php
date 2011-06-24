$("form#upload-form").fileupload({
  url: '/photo/upload',
  singleFileUploads: true,
  autoUpload: false
})
.bind('fileuploadadd', op.upload.handlers.added)
.bind('fileuploadprogress', op.upload.handlers.progress);

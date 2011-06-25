$("form#upload-form").fileupload({
  url: '/photo/upload.json',
  singleFileUploads: true,
  autoUpload: false
})
.bind('fileuploadadd', op.upload.handlers.added)
.bind('fileuploaddone', op.upload.handlers.done)
.bind('fileuploadprogress', op.upload.handlers.progress);

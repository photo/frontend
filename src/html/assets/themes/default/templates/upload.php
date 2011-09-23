<h1>Upload your photos</h1>
<p>
  Select as many photos as you'd like by clicking the button below.
</p>
<div id="upload">
  <form enctype="multipart/form-data" action="<?php Url::photoUpload(); ?>" method="POST" id="upload-form">
    <input type="hidden" name="returnSizes" value="200x200xCR,100x100">
    <input type="file" name="photo" multiple>
    <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
  </form>

  <div id="upload-progress"></div>
  <ul id="upload-queue">
  </ul>
  <br clear="all">
</div>

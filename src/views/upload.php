<div id="upload">
  <form enctype="multipart/form-data" action="/photo/upload" method="POST" id="upload-form">
    <input type="hidden" name="returnSizes" value="200x200xCR,100x100">
    <label class="filebutton">
      <button type="button"><img src="/assets/img/default/header-navigation-upload.png" align="absmiddle"> Select photos</button>
      <span><input type="file" name="photo" multiple></span>
    </label>
  </form>
  
  <div id="upload-progress"></div>
  <ul id="upload-queue">
  </ul>
  <br clear="all">
</div>

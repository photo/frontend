<div id="upload">
  <form enctype="multipart/form-data" action="/photo/upload" method="POST" id="upload-form">
    <input type="hidden" name="returnSizes" value="200x200">
    <input type="file" name="photo" multiple>
    <br>
    <input type="submit" value="upload">
  </form>
  
  <div id="upload-progress"></div>
  <ul id="upload-queue"></ul>
</div>

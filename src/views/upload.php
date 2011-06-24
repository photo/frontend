<div id="upload">
  <form enctype="multipart/form-data" action="/photo/upload" method="POST" id="upload-form">
    <input type="hidden" name="returnOptions" value="200x200">
    <input type="file" name="photo">
    <br>
    <input type="submit" value="upload">
  </form>

  <ul id="upload-queue"></ul>
</div>

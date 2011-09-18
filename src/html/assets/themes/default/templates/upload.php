<style type="text/css" media="screen">
    #fallback { display: none; }
    #uploader-frame { width: 700px; height: 500px; background-color: gray; padding: 50px 0;}
    #drop-zone { width: 600px; height: 500px; margin: 0px auto; color: white; overflow-x: hidden; overflow-y: auto; }
    /* drop zone has three states */
    #drop-zone.waiting { background-color: black; }
    #drop-zone.hover { background-color: green; }
    #drop-zone.active { background-color: blue; }
    
    /* photo in drop zone */
    #drop-zone .photo { width: 100%; position: relative; }
    #drop-zone .photo .name {}
    #drop-zone .photo .size { float: right; }
    #drop-zone .photo .progress {  bottom: 0px; left: 0px; height: 3px; width: 700px;}
</style>
<div id="uploader-frame">
    <div id="drop-zone" class="waiting">
    </div>
</div>


<div id="fallback">
    <h1>Upload your photos</h1>
    <p>
      Select as many photos as you'd like by clicking the button below.
    </p>
    <div id="upload">
      <form enctype="multipart/form-data" action="/photo/upload" method="POST" id="upload-form">
        <input type="hidden" name="returnSizes" value="200x200xCR,100x100">
        <input type="file" name="photo" multiple>
        <input type="hidden" name="crumb" value="<?php echo $crumb; ?>">
      </form>

      <div id="upload-progress"></div>
      <ul id="upload-queue">
      </ul>
      <br clear="all">
    </div>
</div>
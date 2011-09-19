<style type="text/css" media="screen">
    #fallback { display: none; }
    #uploader-frame { width: 700px; height: 500px; margin: 0 auto; background-color: #EFEFEF; padding: 50px 0; border-bottom: solid 1px #CFCFCF; border-right: solid 1px #CFCFCF;}
    #drop-zone { width: 600px; height: 500px; margin: 0px auto; color: black; overflow-x: hidden; overflow-y: auto; border: 1px solid #54c6fb; }
    /* drop zone has three states */
    #drop-zone.waiting { background-color: #BDBDBD; }
    #drop-zone.hover { background-color: #54c6fb; }
    #drop-zone.active { background-color: #BDBDBD; }
    
    /* photo in drop zone */
    #drop-zone .photo { width: 100%; position: relative; height: 25px; z-index: 5; padding: 7px 0 6px 0;}
    #drop-zone .photo .name { margin-left: 40px; }
    #drop-zone .photo .size { float: right; }
	#drop-zone .photo > span.progress { z-index: -1; display: block; height: 100%; background-color: rgb(43,194,83); background-image: -webkit-gradient(linear, left bottom, left top, color-stop(0, rgb(43,194,83)), color-stop(1, rgb(84,240,84)) ); background-image: -webkit-linear-gradient( center bottom, rgb(43,194,83) 37%, rgb(84,240,84) 69% ); background-image: -moz-linear-gradient( center bottom, rgb(43,194,83) 37%, rgb(84,240,84) 69% ); background-image: -ms-linear-gradient( center bottom, rgb(43,194,83) 37%, rgb(84,240,84) 69% ); background-image: -o-linear-gradient( center bottom, rgb(43,194,83) 37%, rgb(84,240,84) 69% ); -webkit-box-shadow: inset 0 2px 9px  rgba(255,255,255,0.3), inset 0 -2px 6px rgba(0,0,0,0.4); -moz-box-shadow: inset 0 2px 9px  rgba(255,255,255,0.3), inset 0 -2px 6px rgba(0,0,0,0.4); position: absolute; top: 0px; overflow: hidden; }
	#drop-zone .photo .thumb { position: absolute; left: 3px; top: 3px; }
</style>
<div id="uploader-frame" crumb="<?php echo $crumb; ?>">
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
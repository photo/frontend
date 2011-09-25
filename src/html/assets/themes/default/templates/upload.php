<div id="uploader-frame" crumb="<?php echo $crumb; ?>">
    <p class="step one">1.</p>
    <p class="step two">2.</p>
    <div class="options">
        <div>
            <p>*Changes to these options will not effect files already dropped.</p>
        </div>
        <div>
            <label for="license">License</label>
            <select name="license" class="license">
                <option value="">All Rights Reserved</option>
                <option value="CC BY">CC Attribution</option>
                <option value="CC BY-SA">CC Attribution-ShareAlike</option>
                <option value="CC BY-ND">CC Attribution-NoDerivs</option>
                <option value="CC BY-NC">CC Attribution-NonCommercial</option>
                <option value="CC BY-NC-SA">CC Attribution-NonCommercial</option>
                <option value="CC BY-NC-ND">CC Attribution-NonCommercial-ShareAlike</option>
                <option value="_custom_">*Custom*</option>
            </select>
            <span class="custom">&nbsp; -> <input type="text" placeholder="Your custom license goes here"></span>
        </div>
        <div>
            <label for="tags">Tags</label><input type="text" name="tags" class="tags" placeholder="Optional comma separated list">
        </div>
    </div><!-- .options -->
    <div id="drop-zone" class="waiting">
        <p class="instructions">Drop photos here to apply settings and upload</p>
    </div>
>>>>>>> upload
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

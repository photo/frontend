<div id="setup">
  <h1>Set up your OpenPhoto site</h1>
  <ol id="setup-steps">
    <?php for($i=1; $i<=3; $i++) { ?>
      <li <?php if($step == $i){ ?>class="current"<?php } ?>>
        <?php echo $i; ?>
      </li>
    <?php } ?>
  </ol>
  <form method="post">
    <input type="hidden" name="database" value="SimpleDb">
    <input type="hidden" name="fileSystem" value="S3">
    <!--
    <div data-role="fieldcontain">
      <label>Select Database</label>
      <select name="database">
        <option value="simpleDb">Amazon SimpleDb</option>
      </select>
    </div>

    <div data-role="fieldcontain">
      <label for="fileSystem">Select File System</label>
      <select name="fileSystem" id="fileSystem">
        <option value="s3">Amazon S3</option>
        <option value="cloudFiles">Cloudfiles</option>
      </select>
    </div>
    -->

    <div id="form-step-1">
      <h2>Enter your Amazon credentials <em>(<a href="">what's this?</a>)</em></h2>
      <div id="awsCredentials">
        <div data-role="fieldcontain">
          <label for="awsKey">Amazon Access Key ID</label>
          <input type="text" name="awsKey" id="awsKey" size="50" autocomplete="false">
        </div>

        <div data-role="fieldcontain">
          <label for="awsSecret">Amazon Secret Access Key</label>
          <input type="text" name="awsSecret" id="awsSecret" size="50" autocomplete="false">
        </div>
      </div>
      <button type="button" class="button pill big" data-step="1">Continue to Step 2</button>
    </div>
    

    <div id="form-step-2" class="hidden">
      <h2>Create a new bucket</h2>
      <div data-role="fieldcontain">
        <label for="s3Bucket">Amazon S3 Bucket Name <em>Bucket names are globally unique</em></label>
        <input type="text" name="s3Bucket" id="s3Bucket" value="<?php echo $appId; ?>-openphoto">
      </div>

      <div data-role="fieldcontain">
        <label for="simpleDbDomain">Amazon SimpleDb Domain</label>
        <input type="text" name="simpleDbDomain" id="simpleDbDomain" value="openphoto">
      </div>
      <button type="button" class="button pill big" data-step="2">Continue to Step 3</button>
    </div>

    <div id="form-step-3" class="hidden">
      <h2>Specify application values</h2>
      <div data-role="fieldcontain">
        <label for="appId">Application ID <em>Strongly recommended that this appId is globally unique</em></label>
        <input type="text" name="appId" id="appId" value="<?php echo attr($appId); ?>">
      </div>

      <div data-role="fieldcontain">
        <label for="imageLibrary">Available Image Libraries</label>
        <select name="imageLibrary" id="imageLibrary">
          <?php foreach($imageLibs as $key => $val) { ?>
            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
          <?php } ?>
        </select>
      </div>
      <button type="submit" class="button pill big">Complete the Setup</button>
    </div>

  </form>
</div>

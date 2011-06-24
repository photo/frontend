<div id="setup">
  <h1>Set up your OpenPhoto site</h1>
  <form method="post">
    <div data-role="fieldcontain">
      <label for="appId">Application ID <em>Strongly recommended that this appId is globally unique</em></label>
      <input type="text" name="appId" id="appId" value="<?php echo $appId; ?>">
    </div>

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

    <div id="awsCredentials">
      <div data-role="fieldcontain">
        <label for="awsKey">Amazon Access Key ID</label>
        <input type="text" name="awsKey" id="awsKey" size="50" autocomplete="false">
      </div>

      <div data-role="fieldcontain">
        <label for="awsSecret">Amazon Secret Access Key</label>
        <input type="text" name="awsSecret" id="awsSecret" size="50" autocomplete="false">
      </div>

      <div data-role="fieldcontain">
        <label for="s3Bucket">Amazon S3 Bucket Name <em>Bucket names are globally unique</em></label>
        <input type="text" name="s3Bucket" id="s3Bucket" value="<?php echo $appId; ?>-openphoto">
      </div>

      <div data-role="fieldcontain">
        <label for="simpleDbDomain">Amazon SimpleDb Domain</label>
        <input type="text" name="simpleDbDomain" id="simpleDbDomain" value="openphoto">
      </div>
    </div>

    <div data-role="fieldcontain">
      <label for="imageLibrary">Select Image Library</label>
      <select name="imageLibrary" id="imageLibrary">
        <?php foreach($imageLibs as $key => $val) { ?>
          <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
        <?php } ?>
      </select>
    </div>

    <button type="submit" class="button pill big">Complete the Setup</button>
  </form>
</div>

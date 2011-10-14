<div id="setup">
  <h1>Create your OpenPhoto site <?php if(empty($qs)) { ?><em><a href="/setup/restart">(start over)</a></em><?php } ?></h1>
  <ol id="setup-steps">
    <?php for($i = 1; $i <= 3; $i++) { ?>
      <li<?php echo ($step == $i) ? ' class="current"' : ''; ?>><?php echo $i; ?></li>
    <?php } ?>
  </ol>
  <?php if(!empty($errors)) { ?>
    <?php if(is_array($errors)) { ?>
      <ul class="errors">
        <?php foreach($errors as $error) { ?>
          <li><?php echo $error; ?></li>
        <?php } ?>
      </ul>
    <?php } else { ?>
      <p class="error"><?php echo $errors; ?></p>
    <?php } ?>
  <?php } ?>
  <div id="setup-step-1"<?php echo ($step != 1) ? ' class="hidden"' : ''?>>
    <form class="validate" action="/setup<?php echo $qs ?>" method="post">
      <h2>User Settings</h2>
      <label for="email">Email address</label>
      <input type="text" name="email" id="email" placeholder="user@example.com" <?php if(isset($email)) { ?>value="<?php Utility::safe($email); ?>"<?php } ?> data-validation="required email">
      <input type="hidden" name="appId" id="appId" <?php if(isset($appId)) { ?>value="<?php Utility::safe($appId); ?>"<?php } ?>>
      <button type="submit">Continue to Step 2</button>
    </form>
  </div>
  <div id="setup-step-2"<?php echo ($step != 2) ? ' class="hidden"' : ''?>>
    <form action="/setup/2<?php echo $qs; ?>" method="post">
      <h2>Site Settings <em>(the defaults work just fine<!--<a href="">what's this?</a>-->)</em></h2>
      <label for="imageLibrary">Select Image Library</label>
      <?php if(isset($imageLibs)) { ?>
        <select name="imageLibrary" id="imageLibrary">
          <?php foreach($imageLibs as $key => $val) { ?>
            <option value="<?php echo $key; ?>"<?php echo ($imageLibrary == $key) ? ' selected="selected"' : '' ?>><?php echo $val; ?></option>
          <?php } ?>
        </select>
      <?php } ?>
      <label>Select Database</label>
      <select name="database">
        <option value="SimpleDb"<?php echo ($database == 'SimpleDb') ? ' selected="selected"' : '' ?>>Amazon SimpleDb</option>
        <option value="MySql"<?php echo ($database == 'MySql') ? ' selected="selected"' : '' ?>>MySQL</option>
      </select>
      <label for="fileSystem">Select File System</label>
      <select name="fileSystem">
        <option value="S3"<?php echo ($filesystem == 'S3') ? ' selected="selected"' : '' ?>>Amazon S3</option>
        <option value="S3Dropbox"<?php echo ($filesystem == 'S3Dropbox') ? ' selected="selected"' : '' ?>>Amazon S3 + Dropbox</option>
        <option value="Local"<?php echo ($filesystem == 'Local') ? ' selected="selected"' : '' ?>>Local filesystem</option>
        <option value="LocalDropbox"<?php echo ($filesystem == 'LocalDropbox') ? ' selected="selected"' : '' ?>>Local filesystem + Dropbox</option>
      </select>
      <button type="submit">Continue to Step 3</button>
    </form>
  </div>
  <div id="setup-step-3"<?php echo ($step != 3) ? ' class="hidden"' : ''?>>
    <form class="validate" action="/setup/3<?php echo $qs; ?>" method="post">
      <h2>Credentials<!-- <em>(<a href="">what's this?</a>)</em>--></h2>
      <?php if(isset($usesAws) && $usesAws) { ?>
        <h3>Enter your Amazon credentials <em>(<a href="https://aws-portal.amazon.com/gp/aws/developer/account/index.html?action=access-key" target="_blank">what's this?</a>)</em></h3>
        <label for="awsKey">Amazon Access Key ID</label>
        <input type="password" name="awsKey" id="awsKey" placeholder="Your AWS access key" size="50" autocomplete="off" data-validation="required" value="<?php echo $awsKey; ?>">
        <label for="awsSecret">Amazon Secret Access Key</label>
        <input type="password" name="awsSecret" id="awsSecret" placeholder="Your AWS access secret" size="50" autocomplete="off" data-validation="required" value="<?php echo $awsSecret; ?>">
        <?php if(isset($usesS3) && $usesS3) { ?>
          <label for="s3Bucket">Amazon S3 Bucket Name</label>
          <?php if(isset($s3Bucket) && !empty($s3Bucket)) { ?>
            <input type="text" name="s3Bucket" id="s3Bucket" size="50" placeholder="Globally unique bucket name" value="<?php Utility::safe($s3Bucket); ?>" data-validation="required">
          <?php } else { ?>
            <input type="text" name="s3Bucket" id="s3Bucket" size="50" placeholder="Globally unique bucket name" value="<?php Utility::safe($_SERVER['HTTP_HOST']); ?>" data-validation="required">
          <?php } ?>
        <?php } ?>
        <?php if($usesSimpleDb) { ?>
          <label for="simpleDbDomain">Amazon SimpleDb Domain</label>
          <?php if(isset($simpleDbDomain) && !empty($simpleDbDomain)) { ?>
            <input type="text" name="simpleDbDomain" id="simpleDbDomain" size="50" placeholder="SimpleDb domain name (i.e. openphoto)" value="<?php Utility::safe($simpleDbDomain); ?>" data-validation="required">
          <?php } else { ?>
            <input type="text" name="simpleDbDomain" id="simpleDbDomain" size="50" placeholder="SimpleDb domain name (i.e. openphoto)" value="openphoto" data-validation="required">
          <?php } ?>
        <?php } ?>
      <?php } ?>
      <?php if(isset($usesMySql) && !empty($usesMySql)) { ?>
        <h3>Enter your MySQL credentials <!--<em>(<a href="">what's this?</a>)</em>--></h3>
        <label for="mySqlHost">MySQL Host</label>
        <input type="text" name="mySqlHost" id="mySqlHost" placeholder="Your MySql host (i.e. 127.0.0.1)" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlHost; ?>">
        <label for="mySqlUser">MySQL Username</label>
        <input type="text" name="mySqlUser" id="mySqlUser" placeholder="Your MySql username" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlUser; ?>">
        <label for="mySqlPassword">MySQL Password</label>
        <input type="password" name="mySqlPassword" id="mySqlPassword" placeholder="Your MySql password" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlPassword; ?>">
        <label for="mySqlDb">MySQL Database <em>(this needs to already exist)</em></label>
        <input type="text" name="mySqlDb" placeholder="Name of your MySql database" value="openphoto" id="mySqlDb" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlDb; ?>">
        <label for="mySqlTablePrefix">Table prefix <em>(optional)</em></label>
        <input type="text" name="mySqlTablePrefix" placeholder="A prefix for all OpenPhoto tables" value="op_" id="mySqlTablePrefix" size="50" autocomplete="off" value="<?php echo $mySqlTablePrefix; ?>">
      <?php } ?>
      <?php if((isset($usesLocalFs) && !empty($usesLocalFs))) { ?>
        <h3>Enter your local file system credentials <!--<em>(<a href="">what's this?</a>)</em>--></h3>
        <label for="fsRoot">File system root <em>(Must be writable by Apache user)</em></label>
        <input type="text" name="fsRoot" id="fsRoot" size="50" placeholder="/home/username/openphoto/src/html/photos (full path to writable directory)" data-validation="required" value="<?php echo $fsRoot; ?>">
        <label for="fsHost">File system hostname for download URL <em>(Web accessible w/o "http://")</em></label>
        <input type="text" name="fsHost" id="fsHost" size="50" placeholder="example.com/photos (no http:// or trailing slash)" data-validation="required" value="<?php echo $fsHost; ?>">
      <?php } ?>
      <?php if(isset($usesDropbox) && !empty($usesDropbox)) { ?>
        <input type="hidden" name="dropboxKey" value="<?php Utility::safe($dropboxKey); ?>">
        <input type="hidden" name="dropboxSecret" value="<?php Utility::safe($dropboxSecret); ?>">
        <input type="hidden" name="dropboxToken" value="<?php Utility::safe($dropboxToken); ?>">
        <input type="hidden" name="dropboxTokenSecret" value="<?php Utility::safe($dropboxTokenSecret); ?>">
        <input type="hidden" name="dropboxFolder" value="<?php Utility::safe($dropboxFolder); ?>">
      <?php } ?>
      <button type="submit">Complete setup</button>
    </form>
  </div>
</div>

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
      <div class="clearfix">
        <label for="email">Email address</label>
        <div class="input">
          <input type="text" name="email" id="email" placeholder="user@example.com" <?php if(isset($email)) { ?>value="<?php $this->utility->safe($email); ?>"<?php } ?> data-validation="required email">
        </div>
      </div>
      <div class="clearfix">
        <label for="theme">Select a theme</label>
        <div class="input">
          <select name="theme">
            <?php foreach($themes as $thisTheme) { ?>
              <option value="<?php $this->utility->safe($thisTheme); ?>" <?php if($theme == $thisTheme){ ?> selected="selected" <?php } ?>><?php echo ucwords($this->utility->safe($thisTheme, false)); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="actions">
        <?php if(isset($_GET['edit'])) { ?><button type="button" onclick="window.location = '/'">Cancel</button><?php } ?>
        <button type="submit">Continue to Step 2</button>
      </div>
      <input type="hidden" name="appId" id="appId" <?php if(isset($appId)) { ?>value="<?php $this->utility->safe($appId); ?>"<?php } ?>>
    </form>
  </div>
  <div id="setup-step-2"<?php echo ($step != 2) ? ' class="hidden"' : ''?>>
    <form action="/setup/2<?php echo $qs; ?>" method="post">
      <h2>Site Settings <em>(the defaults work just fine<!--<a href="">what's this?</a>-->)</em></h2>
      <div class="clearfix">
        <label for="imageLibrary">Select Image Library</label>
        <div class="input">
          <?php if(isset($imageLibs)) { ?>
            <select name="imageLibrary" id="imageLibrary">
              <?php foreach($imageLibs as $key => $val) { ?>
                <option value="<?php echo $key; ?>"<?php echo ($imageLibrary == $key) ? ' selected="selected"' : '' ?>><?php echo $val; ?></option>
              <?php } ?>
            </select>
          <?php } ?>
        </div>
      </div>
      <div class="clearfix">
        <label>Select Database</label>
        <div class="input">
          <select name="database">
            <option value="SimpleDb"<?php echo ($database == 'SimpleDb') ? ' selected="selected"' : '' ?>>Amazon SimpleDb</option>
            <option value="MySql"<?php echo ($database == 'MySql') ? ' selected="selected"' : '' ?>>MySQL</option>
          </select>
        </div>
      </div>
      <div class="clearfix">
        <label for="fileSystem">Select File System</label>
        <div class="input">
          <select name="fileSystem">
            <option value="S3"<?php echo ($filesystem == 'S3') ? ' selected="selected"' : '' ?>>Amazon S3</option>
            <option value="S3Dropbox"<?php echo ($filesystem == 'S3Dropbox') ? ' selected="selected"' : '' ?>>Amazon S3 + Dropbox</option>
            <option value="Local"<?php echo ($filesystem == 'Local') ? ' selected="selected"' : '' ?>>Local filesystem</option>
            <option value="LocalDropbox"<?php echo ($filesystem == 'LocalDropbox') ? ' selected="selected"' : '' ?>>Local filesystem + Dropbox</option>
          </select>
        </div>
      </div>
      <div class="actions">
        <?php if(isset($_GET['edit'])) { ?><button type="button" onclick="window.location = '/'">Cancel</button><?php } ?>
        <button type="submit">Continue to Step 3</button>
      </div>
    </form>
  </div>
  <div id="setup-step-3"<?php echo ($step != 3) ? ' class="hidden"' : ''?>>
    <form class="validate" action="/setup/3<?php echo $qs; ?>" method="post">
      <h2>Credentials<!-- <em>(<a href="">what's this?</a>)</em>--></h2>
      <?php if(isset($usesAws) && $usesAws) { ?>
        <h3>Enter your Amazon credentials <em>(<a href="https://aws-portal.amazon.com/gp/aws/developer/account/index.html?action=access-key" target="_blank">what's this?</a>)</em></h3>
        <div class="clearfix">
          <label for="awsKey">Amazon Access Key ID</label>
          <div class="input">
            <input type="password" name="awsKey" id="awsKey" placeholder="Your AWS access key" size="50" autocomplete="off" data-validation="required" value="<?php echo $awsKey; ?>">
          </div>
        </div>
        <div class="clearfix">
          <label for="awsSecret">Amazon Secret Access Key</label>
          <div class="input">
            <input type="password" name="awsSecret" id="awsSecret" placeholder="Your AWS access secret" size="50" autocomplete="off" data-validation="required" value="<?php echo $awsSecret; ?>">
          </div>
        </div>
        <div class="clearfix">
          <label for="s3Bucket">Amazon S3 Bucket Name</label>
          <div class="input">
            <?php if(isset($usesS3) && $usesS3) { ?>
              <?php if(isset($s3Bucket) && !empty($s3Bucket)) { ?>
                <input type="text" name="s3Bucket" id="s3Bucket" size="50" placeholder="Globally unique bucket name" value="<?php $this->utility->safe($s3Bucket); ?>" data-validation="required">
              <?php } else { ?>
                <input type="text" name="s3Bucket" id="s3Bucket" size="50" placeholder="Globally unique bucket name" value="<?php $this->utility->safe($_SERVER['HTTP_HOST']); ?>" data-validation="required">
              <?php } ?>
            <?php } ?>
          </div>
        </div>
        <?php if($usesSimpleDb) { ?>
          <div class="clearfix">
            <label for="simpleDbDomain">Amazon SimpleDb Domain</label>
            <div class="input">
              <?php if(isset($simpleDbDomain) && !empty($simpleDbDomain)) { ?>
                <input type="text" name="simpleDbDomain" id="simpleDbDomain" size="50" placeholder="SimpleDb domain name (i.e. openphoto)" value="<?php $this->utility->safe($simpleDbDomain); ?>" data-validation="required">
              <?php } else { ?>
                <input type="text" name="simpleDbDomain" id="simpleDbDomain" size="50" placeholder="SimpleDb domain name (i.e. openphoto)" value="openphoto" data-validation="required">
              <?php } ?>
            </div>
          </div>
        <?php } ?>
      <?php } ?>
      <?php if(isset($usesMySql) && !empty($usesMySql)) { ?>
        <h3>Enter your MySQL credentials <!--<em>(<a href="">what's this?</a>)</em>--></h3>
        <div class="clearfix">
          <label for="mySqlHost">MySQL Host</label>
          <div class="input">
            <input type="text" name="mySqlHost" id="mySqlHost" placeholder="Your MySql host (i.e. 127.0.0.1)" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlHost; ?>">
          </div>
        </div>
        <div class="clearfix">
          <label for="mySqlUser">MySQL Username</label>
          <div class="input">
            <input type="text" name="mySqlUser" id="mySqlUser" placeholder="Your MySql username" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlUser; ?>">
          </div>
        </div>
        <div class="clearfix">
        <label for="mySqlPassword">MySQL Password</label>
          <div class="input">
            <input type="password" name="mySqlPassword" id="mySqlPassword" placeholder="Your MySql password" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlPassword; ?>">
          </div>
        </div>
        <div class="clearfix">
          <label for="mySqlDb">MySQL Database <em>(make sure this database already exists)</em></label>
          <div class="input">
            <input type="text" name="mySqlDb" placeholder="Name of your MySql database" id="mySqlDb" size="50" autocomplete="off" data-validation="required" value="<?php echo $mySqlDb; ?>">
          </div>
        </div>
        <div class="clearfix">
          <label for="mySqlTablePrefix">Table prefix <em>(optional)</em></label>
          <div class="input">
            <input type="text" name="mySqlTablePrefix" placeholder="A prefix for all OpenPhoto tables" id="mySqlTablePrefix" size="50" autocomplete="off" value="<?php echo $mySqlTablePrefix; ?>">
          </div>
        </div>
      <?php } ?>
      <?php if((isset($usesLocalFs) && !empty($usesLocalFs))) { ?>
        <h3>Enter your local file system credentials <!--<em>(<a href="">what's this?</a>)</em>--></h3>
        <div class="clearfix">
          <label for="fsRoot">File system root <em>(Must be writable by Apache user)</em></label>
          <div class="input">
            <input type="text" name="fsRoot" id="fsRoot" size="50" placeholder="/home/username/openphoto/src/html/photos (full path to writable directory)" data-validation="required" value="<?php echo $fsRoot; ?>">
          </div>
        </div>
        <div class="clearfix">
          <label for="fsHost">File system hostname for download URL <em>(Web accessible w/o "http://")</em></label>
          <div class="input">
            <input type="text" name="fsHost" id="fsHost" size="50" placeholder="example.com/photos (no http:// or trailing slash)" data-validation="required" value="<?php echo $fsHost; ?>">
          </div>
        </div>
      <?php } ?>
      <?php if(isset($usesDropbox) && !empty($usesDropbox)) { ?>
        <input type="hidden" name="dropboxKey" value="<?php $this->utility->safe($dropboxKey); ?>">
        <input type="hidden" name="dropboxSecret" value="<?php $this->utility->safe($dropboxSecret); ?>">
        <input type="hidden" name="dropboxToken" value="<?php $this->utility->safe($dropboxToken); ?>">
        <input type="hidden" name="dropboxTokenSecret" value="<?php $this->utility->safe($dropboxTokenSecret); ?>">
        <input type="hidden" name="dropboxFolder" value="<?php $this->utility->safe($dropboxFolder); ?>">
      <?php } ?>
      <div class="actions">
        <?php if(isset($_GET['edit'])) { ?><button type="button" onclick="window.location = '/'">Cancel</button><?php } ?>
        <button type="submit">Complete setup</button>
      </div>
    </form>
  </div>
</div>

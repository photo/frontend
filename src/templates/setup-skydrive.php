<div id="setup">
  <h1>Connect your SkyDrive to Trovebox</h1>
  <p>
    <a href="https://www.dropbox.com/developers/apps" target="_blank">Click here</a> to create a Dropbox app if you haven't already.
  </p>
  <div id="setup-skydrive">
    <form action="/setup/skydrive<?php $this->utility->safe($qs); ?>" method="post" class="validate">
    
      <h2>Enter your SkyDrive App credentials</h2>
      <label for="skyDriveKey">SkyDrive Key <em>(<a href="https://www.dropbox.com/developers/apps" target="_blank">found under options</a>)</em></label>
      <input type="password" name="skyDriveKey" id="skyDriveKey" size="50" autocomplete="off" data-validation="required" placeholder="SkyDrive consumer key or app key" value="<?php echo $skyDriveKey; ?>">

      <label for="SkyDriveSecret">SkyDrive Secret</label>
      <input type="password" name="skyDriveSecret" id="skyDriveSecret" size="50" autocomplete="off" data-validation="required" placeholder="SkyDrive consumer secret or app secret" value="<?php echo $skyDriveSecret; ?>">

      <label for="SkyDriveFolder">SkyDrive Folder Name <em>(a name for the folder we save photos to)</em></label>
      <input type="text" name="skyDriveFolder" id="skyDriveFolder" size="50" autocomplete="off" data-validation="required alphanumeric" placeholder="An alpha numeric folder name" value="<?php echo $skyDriveFolder; ?>">
    
      <h2>Sign Into your SkyDrive Account</h2>
      
      <div id="meName" class="Name"></div>
      <div id="meImg"></div>
      <div id="signin"></div>
      
      <div class="btn-toolbar">
        <?php if(isset($_GET['edit'])) { ?><a class="btn" href="/">Cancel</a><?php } ?>
        <button type="submit" class="btn btn-primary">Continue</button>
      </div>
    </form>
  </div>
</div>

<div>
    <div id="meName" class="Name"></div>
    <div id="meImg"></div>
    <div id="signin"></div>
</div>

<script src="//js.live.net/v5.0/wl.js" type="text/javascript"></script>
<script type="text/javascript">

    // Move these somewhere else 
    // Update the following values
    var client_id = "00000000440F3200",
        scope = ["wl.signin", "wl.basic", "wl.offline_access"],
        redirect_uri = "http://www.garethgreenaway.com/setup/skydrive/callback";

    function id(domId) {
        return document.getElementById(domId);
    }

    function displayMe() {
        var imgHolder = id("meImg"),
            nameHolder = id("meName");

        if (imgHolder.innerHTML != "") return;

        if (WL.getSession() != null) {
            WL.api({ path: "me/picture", method: "get" }).then(
                    function (response) {
                        if (response.location) {
                            imgHolder.innerHTML = "<img src='" + response.location + "' />";
                        }
                    }
                );

            WL.api({ path: "me", method: "get" }).then(
                    function (response) {
                        nameHolder.innerHTML = response.name;
                    }
                );
        }
    }

    function clearMe() {
        id("meImg").innerHTML = "";
        id("meName").innerHTML = "";
    }

    WL.Event.subscribe("auth.sessionChange",
        function (e) {
            if (e.session) {
                displayMe();
            }
            else {
                clearMe();
            }            
        }
    );

    WL.init({ client_id: client_id, redirect_uri: redirect_uri, response_type: "code", scope: scope });

    WL.ui({ name: "signin", element: "signin" });

</script>

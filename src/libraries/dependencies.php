<?php
// for unit tests
if(!isset($pathsObj))
  $pathsObj = getConfig()->get('paths');

// exceptions
require $pathsObj->libraries . '/exceptions.php';

// controllers
require $pathsObj->controllers . '/ApiBaseController.php';
require $pathsObj->controllers . '/BaseController.php';
require $pathsObj->controllers . '/ApiController.php';
require $pathsObj->controllers . '/GeneralController.php';
require $pathsObj->controllers . '/AssetsController.php';
require $pathsObj->controllers . '/ApiActionController.php';
require $pathsObj->controllers . '/ApiActivityController.php';
require $pathsObj->controllers . '/ActionController.php';
require $pathsObj->controllers . '/ApiAlbumController.php';
require $pathsObj->controllers . '/AlbumController.php';
require $pathsObj->controllers . '/ApiGroupController.php';
require $pathsObj->controllers . '/GroupController.php';
require $pathsObj->controllers . '/ApiPhotoController.php';
require $pathsObj->controllers . '/PhotoController.php';
require $pathsObj->controllers . '/ApiPluginController.php';
//require $pathsObj->controllers . '/PluginController.php';
require $pathsObj->controllers . '/ApiTagController.php';
require $pathsObj->controllers . '/TagController.php';
require $pathsObj->controllers . '/ApiUserController.php';
require $pathsObj->controllers . '/UpgradeController.php';
require $pathsObj->controllers . '/UserController.php';
require $pathsObj->controllers . '/ApiOAuthController.php';
require $pathsObj->controllers . '/OAuthController.php';
require $pathsObj->controllers . '/ApiWebhookController.php';
require $pathsObj->controllers . '/WebhookController.php';

// libraries
require $pathsObj->external . '/aws/sdk.class.php';
require $pathsObj->external . '/Dropbox/autoload.php';
require $pathsObj->external . '/Mobile_Detect/Mobile_Detect.php';
require $pathsObj->external . '/JSMin/JSMin.php';
require $pathsObj->external . '/CssMin/CssMin.php';
require $pathsObj->libraries . '/functions.php';

// adapters
require $pathsObj->adapters . '/Database.php';
require $pathsObj->adapters . '/DatabaseSimpleDb.php';
require $pathsObj->adapters . '/DatabaseMySql.php';
require $pathsObj->adapters . '/FileSystem.php';
require $pathsObj->adapters . '/FileSystemS3.php';
require $pathsObj->adapters . '/FileSystemS3Dropbox.php';
require $pathsObj->adapters . '/FileSystemLocal.php';
require $pathsObj->adapters . '/FileSystemLocalDropbox.php';
require $pathsObj->adapters . '/FileSystemDropboxBase.php';
require $pathsObj->adapters . '/Login.php';
require $pathsObj->adapters . '/LoginBrowserId.php';
require $pathsObj->adapters . '/LoginFacebook.php';
require $pathsObj->adapters . '/Image.php';
require $pathsObj->adapters . '/ImageImageMagick.php';
require $pathsObj->adapters . '/ImageGraphicsMagick.php';
require $pathsObj->adapters . '/ImageGD.php';

// models
require $pathsObj->models . '/BaseModel.php';
require $pathsObj->models . '/AssetPipeline.php';
require $pathsObj->models . '/Utility.php';
require $pathsObj->models . '/Url.php';
require $pathsObj->models . '/Authentication.php';
require $pathsObj->models . '/Credential.php';
require $pathsObj->models . '/Action.php';
require $pathsObj->models . '/Activity.php';
require $pathsObj->models . '/Album.php';
require $pathsObj->models . '/Group.php';
require $pathsObj->models . '/Photo.php';
require $pathsObj->models . '/Tag.php';
require $pathsObj->models . '/User.php';
require $pathsObj->models . '/Webhook.php';
require $pathsObj->models . '/Http.php';
require $pathsObj->models . '/Theme.php';
require $pathsObj->models . '/Upgrade.php';
require $pathsObj->models . '/PluginBase.php';
require $pathsObj->models . '/Plugin.php';

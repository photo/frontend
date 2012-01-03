<?php
// exceptions
require getConfig()->get('paths')->libraries . '/exceptions.php';

// controllers
require getConfig()->get('paths')->controllers . '/ApiBaseController.php';
require getConfig()->get('paths')->controllers . '/BaseController.php';
require getConfig()->get('paths')->controllers . '/ApiController.php';
require getConfig()->get('paths')->controllers . '/GeneralController.php';
require getConfig()->get('paths')->controllers . '/AssetsController.php';
require getConfig()->get('paths')->controllers . '/ApiActionController.php';
require getConfig()->get('paths')->controllers . '/ActionController.php';
require getConfig()->get('paths')->controllers . '/ApiGroupController.php';
require getConfig()->get('paths')->controllers . '/GroupController.php';
require getConfig()->get('paths')->controllers . '/ApiPhotoController.php';
require getConfig()->get('paths')->controllers . '/PhotoController.php';
require getConfig()->get('paths')->controllers . '/ApiPluginController.php';
//require getConfig()->get('paths')->controllers . '/PluginController.php';
require getConfig()->get('paths')->controllers . '/ApiTagController.php';
require getConfig()->get('paths')->controllers . '/TagController.php';
require getConfig()->get('paths')->controllers . '/ApiUserController.php';
require getConfig()->get('paths')->controllers . '/UpgradeController.php';
require getConfig()->get('paths')->controllers . '/UserController.php';
require getConfig()->get('paths')->controllers . '/ApiOAuthController.php';
require getConfig()->get('paths')->controllers . '/OAuthController.php';
require getConfig()->get('paths')->controllers . '/ApiWebhookController.php';
require getConfig()->get('paths')->controllers . '/WebhookController.php';

// libraries
require getConfig()->get('paths')->external . '/aws/sdk.class.php';
require getConfig()->get('paths')->external . '/Dropbox/autoload.php';
require getConfig()->get('paths')->external . '/Mobile_Detect/Mobile_Detect.php';
require getConfig()->get('paths')->external . '/JSMin/JSMin.php';
require getConfig()->get('paths')->external . '/CssMin/CssMin.php';
require getConfig()->get('paths')->libraries . '/functions.php';

// adapters
require getConfig()->get('paths')->adapters . '/Database.php';
require getConfig()->get('paths')->adapters . '/DatabaseSimpleDb.php';
require getConfig()->get('paths')->adapters . '/DatabaseMySql.php';
require getConfig()->get('paths')->adapters . '/FileSystem.php';
require getConfig()->get('paths')->adapters . '/FileSystemS3.php';
require getConfig()->get('paths')->adapters . '/FileSystemS3Dropbox.php';
require getConfig()->get('paths')->adapters . '/FileSystemLocal.php';
require getConfig()->get('paths')->adapters . '/FileSystemLocalDropbox.php';
require getConfig()->get('paths')->adapters . '/FileSystemDropboxBase.php';
require getConfig()->get('paths')->adapters . '/Login.php';
require getConfig()->get('paths')->adapters . '/LoginBrowserId.php';
require getConfig()->get('paths')->adapters . '/LoginFacebook.php';

// models
require getConfig()->get('paths')->models . '/BaseModel.php';
require getConfig()->get('paths')->models . '/AssetPipeline.php';
require getConfig()->get('paths')->models . '/Utility.php';
require getConfig()->get('paths')->models . '/Url.php';
require getConfig()->get('paths')->models . '/Authentication.php';
require getConfig()->get('paths')->models . '/Credential.php';
require getConfig()->get('paths')->models . '/Action.php';
require getConfig()->get('paths')->models . '/Group.php';
require getConfig()->get('paths')->models . '/Photo.php';
require getConfig()->get('paths')->models . '/Tag.php';
require getConfig()->get('paths')->models . '/User.php';
require getConfig()->get('paths')->models . '/Webhook.php';
require getConfig()->get('paths')->models . '/Http.php';
require getConfig()->get('paths')->models . '/Theme.php';
require getConfig()->get('paths')->models . '/Upgrade.php';
require getConfig()->get('paths')->models . '/Image.php';
require getConfig()->get('paths')->models . '/ImageImageMagick.php';
require getConfig()->get('paths')->models . '/ImageGraphicsMagick.php';
require getConfig()->get('paths')->models . '/ImageGD.php';
require getConfig()->get('paths')->models . '/PluginBase.php';
require getConfig()->get('paths')->models . '/Plugin.php';

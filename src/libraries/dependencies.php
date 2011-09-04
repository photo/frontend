<?php
// public or api routes
require getConfig()->get('paths')->libraries . '/routes-api.php';
require getConfig()->get('paths')->libraries . '/routes.php';


// controllers
require getConfig()->get('paths')->controllers . '/BaseController.php';
require getConfig()->get('paths')->controllers . '/ApiController.php';
require getConfig()->get('paths')->controllers . '/GeneralController.php';
require getConfig()->get('paths')->controllers . '/ApiActionController.php';
require getConfig()->get('paths')->controllers . '/ActionController.php';
require getConfig()->get('paths')->controllers . '/ApiPhotoController.php';
require getConfig()->get('paths')->controllers . '/PhotoController.php';
require getConfig()->get('paths')->controllers . '/ApiTagController.php';
require getConfig()->get('paths')->controllers . '/TagController.php';
require getConfig()->get('paths')->controllers . '/ApiUserController.php';
require getConfig()->get('paths')->controllers . '/UserController.php';
require getConfig()->get('paths')->controllers . '/OAuthController.php';

// libraries
require getConfig()->get('paths')->external . '/aws/sdk.class.php';
require getConfig()->get('paths')->adapters . '/Database.php';
require getConfig()->get('paths')->adapters . '/DatabaseSimpleDb.php';
require getConfig()->get('paths')->adapters . '/DatabaseMySql.php';
require getConfig()->get('paths')->adapters . '/FileSystem.php';
require getConfig()->get('paths')->adapters . '/FileSystemS3.php';
require getConfig()->get('paths')->adapters . '/FileSystemLocal.php';
require getConfig()->get('paths')->libraries . '/functions.php';

// models
require getConfig()->get('paths')->models . '/Utility.php';
require getConfig()->get('paths')->models . '/Auth.php';
require getConfig()->get('paths')->models . '/Action.php';
require getConfig()->get('paths')->models . '/Photo.php';
require getConfig()->get('paths')->models . '/Tag.php';
require getConfig()->get('paths')->models . '/User.php';
require getConfig()->get('paths')->models . '/Theme.php';
require getConfig()->get('paths')->models . '/Image.php';
require getConfig()->get('paths')->models . '/ImageImageMagick.php';
require getConfig()->get('paths')->models . '/ImageGraphicsMagick.php';
require getConfig()->get('paths')->models . '/ImageGD.php';

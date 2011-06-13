<?php
// public or api routes
require getConfig()->get('paths')->libraries . '/routes.php';
require getConfig()->get('paths')->libraries . '/routes-api.php';


// controllers
require getConfig()->get('paths')->controllers . '/BaseController.php';
require getConfig()->get('paths')->controllers . '/ApiController.php';
require getConfig()->get('paths')->controllers . '/GeneralController.php';
require getConfig()->get('paths')->controllers . '/PhotosController.php';

// libraries
require getConfig()->get('paths')->external . '/aws/sdk.class.php';
require getConfig()->get('paths')->adapters . '/Database.php';
require getConfig()->get('paths')->adapters . '/DatabaseProvider.php';
require getConfig()->get('paths')->adapters . '/DatabaseProviderSimpleDb.php';
require getConfig()->get('paths')->adapters . '/FileSystem.php';
require getConfig()->get('paths')->adapters . '/FileSystemProvider.php';
require getConfig()->get('paths')->adapters . '/FileSystemProviderS3.php';

// models
require getConfig()->get('paths')->models . '/Photo.php';

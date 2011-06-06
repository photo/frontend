<?php
// routes
require getConfig()->get('paths')->libraries . '/routes.php';

// controllers
require getConfig()->get('paths')->controllers . '/BasicController.php';
require getConfig()->get('paths')->controllers . '/GeneralController.php';

// libraries
require getConfig()->get('paths')->external . '/aws/sdk.class.php';
require getConfig()->get('paths')->adapters . '/Database.php';
require getConfig()->get('paths')->adapters . '/DatabaseProvider.php';
require getConfig()->get('paths')->adapters . '/DatabaseProviderSimpleDb.php';

// models
require getConfig()->get('paths')->models . '/Photo.php';

<?php
require $configObj->get('paths')->libraries . '/routes-api.php';

/*
 * Home page, optionally redirects if the theme doesn't have a front.php
 */
getRoute()->get('/', array('GeneralController', 'home'));
if($configObj->get('site')->maintenance == 1)
  getRoute()->get('/maintenance', array('GeneralController', 'maintenance'));


/*
 * General pages like robots.txt
 */
$routeObj->get('/robots.txt', array('GeneralController', 'robots')); // robots.txt

/*
 * Action endpoints
 * All action endpoints follow the same convention.
 * Everything in []'s are optional
 * /action/{id}[/{additional}]
 */
$routeObj->post('/action/([a-zA-Z0-9]+)/(photo)/create', array('ActionController', 'create')); // post an action (/action/{id}/{type}/create)

/*
 * Manage endpoints
 * /manage
 */
$routeObj->get('/manage', array('ManageController', 'home')); // redirect to /manage/photos
$routeObj->get('/manage/photos', array('ManageController', 'photos'));
$routeObj->get('/manage/albums', array('ManageController', 'albums'));
$routeObj->get('/manage/apps', array('ManageController', 'apps'));
$routeObj->get('/manage/apps/callback', array('ManageController', 'appsCallback'));
$routeObj->get('/manage/features', array('ManageController', 'features')); // redirect to /manage/settings
$routeObj->get('/manage/settings', array('ManageController', 'settings'));
$routeObj->get('/manage/groups', array('ManageController', 'groups'));
$routeObj->get('/manage/password/reset/([a-z0-9]{32})', array('ManageController', 'passwordReset'));

/*
 * Album endpoints
 * All album endpoints follow the same convention.
 * Everything in []'s are optional
 * /album[s][/:id]/{action}
 */
getRoute()->get('/albums/list', array('AlbumController', 'list_')); // retrieve activities (/albums/list)

/*
 * Photo endpoints
 * All photo endpoints follow the same convention.
 * Everything in []'s are optional
 * /photo/{id}[/{additional}]
 */
$routeObj->get('/photo/([a-zA-Z0-9]+)/edit', array('PhotoController', 'edit')); // edit form for a photo (/photo/{id}/edit)
$routeObj->get('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).jpg', array('PhotoController', 'create')); // create a version of a photo (/photo/create/{id}/{options}.jpg)
$routeObj->get('/photo/([a-zA-Z0-9]+)/download', array('PhotoController', 'download')); // download a high resolution version of a photo (/photo/create/{id}/{options}.jpg)
$routeObj->get('/photo/([a-zA-Z0-9]+)/?(.+)?/view', array('PhotoController', 'view')); // view a photo (/photo/{id}[/{options}])/view
$routeObj->get('/p/([a-zA-Z0-9]+)/?(.+)?', array('PhotoController', 'view')); // (shortcut for photo/view) view a photo (/p/{id}[/{options}])
$routeObj->post('/photo/([a-zA-Z0-9]+)/update', array('PhotoController', 'update')); // update a photo (/photo/{id}/update
$routeObj->post('/photo/upload', array('PhotoController', 'uploadPost')); // upload a photo
$routeObj->get('/photos/upload', array('PhotoController', 'upload')); // view the upload photo form
$routeObj->get('/photos/?(.+)?/list', array('PhotoController', 'list_')); // view all photos / optionally filter (/photos[/{options})]/list

/*
 * Resource mapping endpoints
 * All shortener endpoints follow the same convention.
 * Everything in []'s are optional
 * /s[/{id}]/{action}
 */
$routeObj->get('/?v?1?/s/([a-z0-9]+)', array('ResourceMapController', 'render'), EpiApi::external); // create a resource map (/s/{id}/view.json)

/*
 * Tag endpoints
 * All tag endpoints follow the same convention.
 * Everything in []'s are optional
 * /tag[s][/{id}/]{action}
 */
$routeObj->get('/tags/list', array('TagController', 'list_')); // view tags

/*
 * User endpoints
 * All user endpoints follow the same convention.
 * Everything in []'s are optional
 * /user/{action}
 */
$routeObj->get('/user/logout', array('UserController', 'logout')); // logout
$routeObj->get('/user/settings', array('UserController', 'settings'));

/*
 * Webhook endpoints follow the same convention.
 * Everything in []'s are optional
 * /webhook[s][/{id}]/{action}
 */
$routeObj->post('/?1?/webhook/subscribe', array('WebhookController', 'subscribe'));

/*
 * OAuth endpoints
 * All oauth endpoints follow the same convention.
 * /v{version}/oauth/{action}
 */
$routeObj->get('/v[1]/oauth/authorize', array('OAuthController', 'authorize'));
$routeObj->post('/v[1]/oauth/authorize', array('OAuthController', 'authorizePost'));
$routeObj->post('/v[1]/oauth/token/access', array('OAuthController', 'tokenAccess'));
$routeObj->get('/v[1]/oauth/token/access', array('OAuthController', 'tokenAccess'));
$routeObj->post('/v[1]/oauth/token/request', array('OAuthController', 'tokenRequest'));
$routeObj->get('/v[1]/oauth/test', array('OAuthController', 'test'));
$routeObj->get('/v[1]/oauth/flow', array('OAuthController', 'flow'));

if($runUpgrade)
  require $configObj->get('paths')->libraries . '/routes-upgrade.php';

require $configObj->get('paths')->libraries . '/routes-error.php';

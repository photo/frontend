<?php
require getConfig()->get('paths')->libraries . '/routes-api.php';

/*
 * Home page, optionally redirects if the theme doesn't have a front.php
 */
getRoute()->get('/', array('GeneralController', 'home'));

/*
 * Action endpoints
 * All action endpoints follow the same convention.
 * Everything in []'s are optional
 * /action/{id}[/{additional}]
 */
getRoute()->post('/action/([a-zA-Z0-9]+)/(photo)/create', array('ActionController', 'create')); // post an action (/action/{id}/{type}/create)

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
getRoute()->get('/photo/([a-zA-Z0-9]+)/edit', array('PhotoController', 'edit')); // edit form for a photo (/photo/{id}/edit)
getRoute()->get('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).jpg', array('PhotoController', 'create')); // create a version of a photo (/photo/create/{id}/{options}.jpg)
getRoute()->get('/photo/([a-zA-Z0-9]+)/?(.+)?/view', array('PhotoController', 'view')); // view a photo (/photo/{id}[/{options}])/view
getRoute()->get('/p/([a-zA-Z0-9]+)/?(.+)?', array('PhotoController', 'view')); // (shortcut for photo/view) view a photo (/p/{id}[/{options}])
getRoute()->post('/photo/([a-zA-Z0-9]+)/update', array('PhotoController', 'update')); // update a photo (/photo/{id}/update
getRoute()->post('/photo/upload', array('PhotoController', 'uploadPost')); // upload a photo
getRoute()->get('/photos/upload', array('PhotoController', 'upload')); // view the upload photo form
getRoute()->get('/photos/?(.+)?/list', array('PhotoController', 'list_')); // view all photos / optionally filter (/photos[/{options})]/list

/*
 * Tag endpoints
 * All tag endpoints follow the same convention.
 * Everything in []'s are optional
 * /tag[s][/{id}/]{action}
 */
getRoute()->get('/tags/list', array('TagController', 'list_')); // view tags

/*
 * User endpoints
 * All user endpoints follow the same convention.
 * Everything in []'s are optional
 * /user/{action}
 */
getRoute()->get('/user/logout', array('UserController', 'logout')); // logout
getRoute()->get('/user/settings', array('UserController', 'settings'));
getRoute()->post('/user/login/mobile', array('UserController', 'loginMobile'));
getRoute()->post('/user/mobile/passphrase', array('UserController', 'mobilePassphrase'));

/*
 * Webhook endpoints follow the same convention.
 * Everything in []'s are optional
 * /webhook[s][/{id}]/{action}
 */
getRoute()->post('/webhook/subscribe', array('WebhookController', 'subscribe'));

/*
 * OAuth endpoints
 * All oauth endpoints follow the same convention.
 * /v{version}/oauth/{action}
 */
getRoute()->get('/v[1]/oauth/authorize', array('OAuthController', 'authorize'));
getRoute()->post('/v[1]/oauth/authorize', array('OAuthController', 'authorizePost'));
getRoute()->post('/v[1]/oauth/token/access', array('OAuthController', 'tokenAccess'));
getRoute()->get('/v[1]/oauth/token/access', array('OAuthController', 'tokenAccess'));
getRoute()->post('/v[1]/oauth/token/request', array('OAuthController', 'tokenRequest'));
getRoute()->get('/v[1]/oauth/test', array('OAuthController', 'test'));
getRoute()->get('/v[1]/oauth/flow', array('OAuthController', 'flow'));

if($runUpgrade)
  require getConfig()->get('paths')->libraries . '/routes-upgrade.php';

require getConfig()->get('paths')->libraries . '/routes-error.php';

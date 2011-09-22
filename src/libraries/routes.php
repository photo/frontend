<?php
// home page
getRoute()->get('/', array('GeneralController', 'home'));
// post an action
getRoute()->post('/action/(photo)/([a-zA-Z0-9]+)', array('ActionController', 'post'), EpiApi::external);
// edit a photo
getRoute()->get('/photo/edit/([a-zA-Z0-9]+)', array('PhotoController', 'edit'));
// create a version of a photo
getRoute()->get('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).jpg', array('PhotoController', 'create'));
// view a photo
getRoute()->get('/photo/([a-zA-Z0-9]+)/?(\d+x\d+x?[A-Zx]*)?', array('PhotoController', 'photo'));
// update a photo
getRoute()->post('/photo/([a-zA-Z0-9]+)', array('PhotoController', 'update'));
// upload a photo
getRoute()->post('/photo/upload', array('PhotoController', 'uploadPost'));
// view the upload photo form
getRoute()->get('/photos/upload', array('PhotoController', 'upload'));
// view all photos / optionally filter
getRoute()->get('/photos/?(.+)?', array('PhotoController', 'photos'));

// view tags
getRoute()->get('/tags', array('TagController', 'tags')); 
// logout
getRoute()->get('/user/logout', array('UserController', 'logout'));

// oauth endpoints
getRoute()->get('/v[1]/oauth/authorize', array('OAuthController', 'authorize'));
getRoute()->post('/v[1]/oauth/authorize', array('OAuthController', 'authorizePost'));
getRoute()->post('/v[1]/oauth/token/access', array('OAuthController', 'tokenAccess'));
getRoute()->post('/v[1]/oauth/token/request', array('OAuthController', 'tokenRequest'));
getRoute()->get('/v[1]/oauth/test', array('OAuthController', 'test'));
getRoute()->get('/v[1]/oauth/flow', array('OAuthController', 'flow'));

// error routes
getRoute()->get('/error/403', array('GeneralController', 'error403'));
getRoute()->get('/error/404', array('GeneralController', 'error404'));


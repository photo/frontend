<?php
// home page
getRoute()->get('/', array('GeneralController', 'home'));
// update a photo
getRoute()->post('/photo/([a-zA-Z0-9]+)', array('PhotosController', 'update'));
// view a photo
getRoute()->get('/photo/([a-zA-Z0-9]+)/?(\d+x\d+x?[A-Zx]*)?', array('PhotosController', 'photo'));
// create a version of a photo
getRoute()->get('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).jpg', array('PhotosController', 'create'));
// upload a photo
getRoute()->post('/photo/upload', array('PhotosController', 'uploadPost'));
// view the upload photo form
getRoute()->get('/photos/upload', array('PhotosController', 'upload'));
// view all photos / optionally filter
getRoute()->get('/photos/?(.+)?', array('PhotosController', 'photos'));

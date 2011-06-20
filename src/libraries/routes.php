<?php
getRoute()->get('/', array('PhotosController', 'home'));
getRoute()->get('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).jpg', array('PhotosController', 'create'), EpiApi::external);
getRoute()->post('/photo/upload', array('PhotosController', 'uploadPost'));
getRoute()->get('/photos', array('PhotosController', 'home'));
getRoute()->get('/photos/upload', array('PhotosController', 'upload'));

<?php
getRoute()->get('/', array('PhotosController', 'photos'));
getRoute()->get('/photo/([a-zA-Z0-9]+)/?(\d+x\d+x?[A-Zx]*)?', array('PhotosController', 'photo'));
getRoute()->get('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).jpg', array('PhotosController', 'create'));
getRoute()->post('/photo/upload', array('PhotosController', 'uploadPost'));
getRoute()->get('/photos', array('PhotosController', 'photos'));
getRoute()->get('/photos/upload', array('PhotosController', 'upload'));

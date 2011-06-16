<?php
getRoute()->get('/', array('GeneralController', 'home'));
getRoute()->post('/photo/upload', array('PhotosController', 'uploadPost'));
getRoute()->get('/photos', array('PhotosController', 'home'));
getRoute()->get('/photos/upload', array('PhotosController', 'upload'));

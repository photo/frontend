<?php
getRoute()->get('/', array('GeneralController', 'home'));
getRoute()->get('/photo/upload', array('PhotosController', 'upload'));
getRoute()->get('/photos', array('PhotosController', 'home'));

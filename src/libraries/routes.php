<?php
getRoute()->get('/', array('GeneralController', 'home'));
getRoute()->get('/photos', array('PhotosController', 'home'));
getRoute()->get('/photos/upload', array('PhotosController', 'upload'));

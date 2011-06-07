<?php
getRoute()->get('/', array('GeneralController', 'home'));
getRoute()->get('/photos', array('PhotosController', 'home'));

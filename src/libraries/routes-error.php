<?php
/*
 * Error endpoints
 * All error endpoints follow the same convention.
 * /error/{code}
 */
getRoute()->get('/error/403', array('GeneralController', 'error403'));
getRoute()->get('/error/404', array('GeneralController', 'error404'));
getRoute()->get('/error/500', array('GeneralController', 'error500'));
getRoute()->get('.*', array('GeneralController', 'error404'));

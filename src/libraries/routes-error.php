<?php
/*
 * Asset endpoints
 * All asset endpoints follow the same convention.
 * Everything in []'s are optional
 * /assets/cache/:version/:type/:file[,:file]
 */
getRoute()->get('/assets/cache/[^/]+/(js|css)/(c|m)(/.+)', array('AssetsController', 'get'));

/*
 * Error endpoints
 * All error endpoints follow the same convention.
 * /error/{code}
 */
getRoute()->get('/error/403', array('GeneralController', 'error403'));
getRoute()->get('/error/404', array('GeneralController', 'error404'));
getRoute()->get('/error/500', array('GeneralController', 'error500'));
getRoute()->get('.*', array('GeneralController', 'error404'));

<?php
getApi()->post('/photo/([a-zA-Z0-9]+)/delete.json', array('ApiController', 'photoDelete'), EpiApi::external);
// TODO, make internal for now
getApi()->get('/photo/([a-zA-Z0-9]+)/url/(\d+)x(\d+)x?([A-Zx]*)?.json', array('ApiController', 'photoDynamicUrl'), EpiApi::external);
//getApi()->post('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).json', array('ApiController', 'photoDynamic'), EpiApi::external);
getApi()->post('/photo/upload.json', array('ApiController', 'photoUpload'), EpiApi::external);
getApi()->get('/photo/([a-zA-Z0-9]+)/?(\d+x\d+x?[A-Zx]*)?.json', array('ApiController', 'photo'), EpiApi::external);
getApi()->get('/photos.json', array('ApiController', 'photos'), EpiApi::external);

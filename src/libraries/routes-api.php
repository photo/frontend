<?php
// delete a photo
getApi()->post('/photo/([a-zA-Z0-9]+)/delete.json', array('ApiController', 'photoDelete'), EpiApi::external);
// update a photo
getApi()->post('/photo/([a-zA-Z0-9]+).json', array('ApiController', 'photoUpdate'), EpiApi::external);
// generate a dynamic photo url
// TODO, make internal for now
getApi()->get('/photo/([a-zA-Z0-9]+)/url/(\d+)x(\d+)x?([A-Zx]*)?.json', array('ApiController', 'photoDynamicUrl'), EpiApi::external);
//getApi()->post('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).json', array('ApiController', 'photoDynamic'), EpiApi::external);
// upload a photo
getApi()->post('/photo/upload.json', array('ApiController', 'photoUpload'), EpiApi::external);
// get a photo's information
getApi()->get('/photo/([a-zA-Z0-9]+)/?(\d+x\d+x?[A-Zx]*)?.json', array('ApiController', 'photo'), EpiApi::external);
// get all photos / optionally filter
getApi()->get('/photos/?(.+)?.json', array('ApiController', 'photos'), EpiApi::external);

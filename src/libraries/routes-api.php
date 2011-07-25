<?php
getApi()->get('/hello.json', array('ApiController', 'hello'), EpiApi::external);
// delete an action
getApi()->post('/action/([a-zA-Z0-9]+)/delete.json', array('ApiActionController', 'delete'), EpiApi::external);
// post an action
getApi()->post('/action/(photo)/([a-zA-Z0-9]+).json', array('ApiActionController', 'post'), EpiApi::external);

// delete a photo
getApi()->post('/photo/([a-zA-Z0-9]+)/delete.json', array('ApiPhotoController', 'delete'), EpiApi::external);
// upload a photo
getApi()->post('/photo/upload.json', array('ApiPhotoController', 'upload'), EpiApi::external);
// generate a dynamic photo url
// TODO, make internal for now
getApi()->get('/photo/([a-zA-Z0-9]+)/url/(\d+)x(\d+)x?([A-Zx]*)?.json', array('ApiPhotoController', 'dynamicUrl'), EpiApi::external);
//getApi()->post('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).json', array('ApiPhotoController', 'dynamic'), EpiApi::external);

// below are general endpoints that need to be included last
// update a photo
getApi()->post('/photo/([a-zA-Z0-9]+).json', array('ApiPhotoController', 'update'), EpiApi::external);
// get a photo's information
getApi()->get('/photo/([a-zA-Z0-9]+)/?(\d+x\d+x?[A-Zx]*)?.json', array('ApiPhotoController', 'photo'), EpiApi::external);
// get all photos / optionally filter
getApi()->get('/photos/?(.+)?.json', array('ApiPhotoController', 'photos'), EpiApi::external);

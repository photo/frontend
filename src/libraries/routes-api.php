<?php
getApi()->post('/photo/([a-zA-Z0-9]+)/delete.json', array('ApiController', 'photoDelete'), EpiApi::external);
getApi()->post('/photo/upload.json', array('ApiController', 'photoUpload'), EpiApi::external);
getApi()->get('/photos.json', array('ApiController', 'photos'), EpiApi::external);

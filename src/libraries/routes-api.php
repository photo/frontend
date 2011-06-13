<?php
getApi()->get('/photo/upload.json', array('ApiController', 'photoUpload'), EpiApi::external);
getApi()->get('/photos.json', array('ApiController', 'photos'), EpiApi::external);

<?php
$apiObj->get('/v1/albums/list.json', array('ApiAlbumV1Controller', 'list_'), EpiApi::external); // retrieve activities (/albums/list.json)
$apiObj->get('/v1/photos/?(.+)?/list.json', array('ApiPhotoV1Controller', 'list_'), EpiApi::external); // get all photos / optionally filter (/photos[/{options}]/view.json)

<?php
/*
 * Activity endpoints
 * All activity endpoints follow the same convention.
 * Everything in []'s are optional
 * /activit(y|ies)/{action}.json
 */
$routeObj->get('/activities/?(.+)?/list.(atom|rss)', array('FeedActivityController', 'list_'), EpiApi::external); // retrieve activities (/activities/list.atom)

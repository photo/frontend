<?php
/*
 * Hello world test endpoint.
 * If ?auth=true is passed then it will run OAuth validation on the request.
 */
getApi()->get('/hello.json', array('ApiController', 'hello'), EpiApi::external);

/*
 * Action endpoints
 * All action endpoints follow the same convention.
 * Everything in []'s are optional
 * /action/{id}[/{additional}].json
 */
getApi()->get('/action/([a-zA-Z0-9]+)/view.json', array('ApiActionController', 'view'), EpiApi::external); // retrieve an action (/action/{id}/view.json)
getApi()->post('/action/([a-zA-Z0-9]+)/delete.json', array('ApiActionController', 'delete'), EpiApi::external); // delete an action (/action/{id}/delete.json)
getApi()->post('/action/([a-zA-Z0-9]+)/(photo)/create.json', array('ApiActionController', 'create'), EpiApi::external); // post an action (/action/{id}/{type}/update.json)

/*
 * Photo endpoints
 * All photo endpoints follow the same convention.
 * Everything in []'s are optional
 * /photo[s]/{id}/{action}[/{additional}].json
 */
getApi()->post('/photo/([a-zA-Z0-9]+)/delete.json', array('ApiPhotoController', 'delete'), EpiApi::external); // delete a photo (/photo/{id}/delete.json)
getApi()->get('/photo/([a-zA-Z0-9]+)/edit.json', array('ApiPhotoController', 'edit'), EpiApi::external); // edit form for photo (/photo/{id}/edit.json)
getApi()->post('/photo/([a-zA-Z0-9]+)/update.json', array('ApiPhotoController', 'update'), EpiApi::external); // update a photo (/photo/{id}/update.json)
getApi()->get('/photo/([a-zA-Z0-9]+)/view.json', array('ApiPhotoController', 'view'), EpiApi::external); // get a photo's information (/photo/view/{id}.json)
getApi()->get('/photos/?(.+)?/list.json', array('ApiPhotoController', 'list_'), EpiApi::external); // get all photos / optionally filter (/photos[/{options}]/view.json)
getApi()->post('/photo/upload.json', array('ApiPhotoController', 'upload'), EpiApi::external); // upload a photo
getApi()->get('/photo/([a-zA-Z0-9]+)/url/(\d+)x(\d+)x?([A-Zx]*)?.json', array('ApiPhotoController', 'dynamicUrl'), EpiApi::external); // generate a dynamic photo url (/photo/{id}/url/{options}.json) TODO, make internal for now
getApi()->get('/photo/([a-zA-Z0-9]+)/nextprevious/?(.+)?.json', array('ApiPhotoController', 'nextPrevious'), EpiApi::external); // get a photo's next/previous (/photo/{id}/nextprevious[/{options}].json)
//getApi()->post('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).json', array('ApiPhotoController', 'dynamic'), EpiApi::external);

/*
 * Tag endpoints
 * All tag endpoints follow the same convention.
 * Everything in []'s are optional
 * /tag[s][/{id}/]{action}.json
 */
getApi()->post('/tag/create.json', array('ApiTagController', 'create'), EpiApi::external); // post a tag (/tag/{id}/update.json)
getApi()->post('/tag/(.+)/delete.json', array('ApiTagController', 'delete'), EpiApi::external); // post a tag (/tag/{id}/update.json)
getApi()->post('/tag/(.+)/update.json', array('ApiTagController', 'update'), EpiApi::external); // post a tag (/tag/{id}/update.json)
getApi()->get('/tags/list.json', array('ApiTagController', 'list_'), EpiApi::external); // retrieve tags

/*
 * User endpoints
 * All user endpoints follow the same convention.
 * Everything in []'s are optional
 * /user[/{provider}]/{action}.json
 */
getApi()->post('/user/([a-z0-9]+)/login.json', array('ApiUserController', 'login'), EpiApi::external);
getApi()->post('/user/login/mobile.json', array('ApiUserController', 'loginMobile'), EpiApi::external);
getApi()->get('/user/logout.json', array('ApiUserController', 'logout'), EpiApi::external);

/*
 * OAuth endpoints
 * All oauth endpoints follow the same convention.
 * /v{version}/oauth[/{id}]/{action}
 */
getApi()->get('/oauth/([a-zA-Z0-9]+)/view.json', array('ApiOAuthController', 'view'), EpiApi::external);
getApi()->get('/oauth/list.json', array('ApiOAuthController', 'list_'), EpiApi::external);
getApi()->post('/oauth/([a-zA-Z0-9]+)/delete.json', array('ApiOAuthController', 'delete'), EpiApi::external);

/*
 * Group endpoints follow the same convention.
 * Everything in []'s are optional
 * /group[s]/{action}.json
 */
getApi()->post('/group/create.json', array('ApiGroupController', 'create'), EpiApi::external);
getApi()->post('/group/([a-zA-Z0-9]+)/delete.json', array('ApiGroupController', 'delete'), EpiApi::external);
getApi()->post('/group/([a-zA-Z0-9]+)/update.json', array('ApiGroupController', 'update'), EpiApi::external);
getApi()->get('/group/([a-zA-Z0-9]+)/view.json', array('ApiGroupController', 'view'), EpiApi::external);
getApi()->get('/groups/list.json', array('ApiGroupController', 'list_'), EpiApi::external);

/*
 * Plugin endpoints follow the same convention.
 * Everything in []'s are optional
 * /plugin[s]/{action}.json
 */
getApi()->post('/plugin/([a-zA-Z0-9]+)/update.json', array('ApiPluginController', 'update'), EpiApi::external);
//getApi()->get('/plugin/([a-zA-Z0-9]+)/view.json', array('ApiPluginController', 'view'), EpiApi::external);
getApi()->get('/plugins/list.json', array('ApiPluginController', 'list_'), EpiApi::external);

/*
 * Webhook endpoints follow the same convention.
 * Everything in []'s are optional
 * /webhook[s][/{id}]/{action}.json
 */
getApi()->post('/webhook/create.json', array('ApiWebhookController', 'create'), EpiApi::internal);
getApi()->post('/webhook/([a-zA-Z0-9]+)/delete.json', array('ApiWebhookController', 'delete'), EpiApi::external);
getApi()->post('/webhook/([a-zA-Z0-9]+)/update.json', array('ApiWebhookController', 'update'), EpiApi::internal);
getApi()->get('/webhook/([a-zA-Z0-9]+)/view.json', array('ApiWebhookController', 'view'), EpiApi::external);
getApi()->get('/webhooks/?(.*)?/list.json', array('ApiWebhookController', 'list_'), EpiApi::internal);

/*
 * System endpoints follow the same convention.
 * Everything in []'s are optional
 * /system/{action}.json
 */
getApi()->get('/system/diagnostics.json', array('ApiController', 'diagnostics'), EpiApi::external);
getApi()->get('/system/version.json', array('ApiController', 'version'), EpiApi::external);

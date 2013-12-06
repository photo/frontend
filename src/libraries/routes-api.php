<?php
require $configObj->get('paths')->libraries . '/routes-api-deprecated.php';

/*
 * Hello world test endpoint.
 * If ?auth=true is passed then it will run OAuth validation on the request.
 */
$apiObj->get('/v0/hello.json', array('ApiController', 'helloV0'), EpiApi::external); // fake test endpoint to validate versioning works
$apiObj->get('/?v?[1-2]?/hello.json', array('ApiController', 'hello'), EpiApi::external);

/*
 * Action endpoints
 * All action endpoints follow the same convention.
 * Everything in []'s are optional
 * /action/{id}[/{additional}].json
 */
$apiObj->get('/?v?[1-2]?/action/([a-zA-Z0-9]+)/view.json', array('ApiActionController', 'view'), EpiApi::external); // retrieve an action (/action/{id}/view.json)
$apiObj->post('/?v?[1-2]?/action/([a-zA-Z0-9]+)/delete.json', array('ApiActionController', 'delete'), EpiApi::external); // delete an action (/action/{id}/delete.json)
$apiObj->post('/?v?[1-2]?/action/([a-zA-Z0-9]+)/(photo)/create.json', array('ApiActionController', 'create'), EpiApi::external); // post an action (/action/{id}/{type}/create.json)

/*
 * Activity endpoints
 * All activity endpoints follow the same convention.
 * Everything in []'s are optional
 * /activit(y|ies)/{action}.json
 */
$apiObj->get('/?v?[1-2]?/activities/?(.+)?/list.json', array('ApiActivityController', 'list_'), EpiApi::external); // retrieve activities (/activities/list.json)
$apiObj->post('/?v?[1-2]?/activities/purge.json', array('ApiActivityController', 'purge'), EpiApi::external); // purge all activities (/activities/purge.json)
$apiObj->get('/?v?[1-2]?/activity/([a-zA-Z0-9]+)/view.json', array('ApiActivityController', 'view'), EpiApi::external); // retrieve activity (/activity/:id/view.json)
$apiObj->post('/?v?[1-2]?/activity/create.json', array('ApiActivityController', 'create'), EpiApi::internal); // post an action (/action/{id}/{type}/create.json)

/*
 * Album endpoints
 * All album endpoints follow the same convention.
 * Everything in []'s are optional
 * /album[s][/:id]/{action}.json
 */
$apiObj->post('/?v?[1-2]?/album/([a-zA-Z0-9]+)/cover/([a-zA-Z0-9]+)/update.json', array('ApiAlbumController', 'coverUpdate'), EpiApi::external); // update an album cover (/album/:albumId/cover/:photoId/update.json)
$apiObj->post('/?v?[1-2]?/album/create.json', array('ApiAlbumController', 'create'), EpiApi::external); // post an activity (/activity/create.json)
$apiObj->post('/?v?[1-2]?/album/([a-zA-Z0-9]+)/delete.json', array('ApiAlbumController', 'delete'), EpiApi::external); // post an activity (/activity/create.json)
$apiObj->get('/?v?[1-2]?/album/form.json', array('ApiAlbumController', 'form'), EpiApi::external); // post an activity (/activity/create.json)
$apiObj->post('/?v?[1-2]?/album/([a-zA-Z0-9]+)/(photo)/(add|remove).json', array('ApiAlbumController', 'updateIndex'), EpiApi::external); // post an action (/action/{id}/{type}/{action}.json)
$apiObj->get('/?v?[1-2]?/albums/list.json', array('ApiAlbumController', 'list_'), EpiApi::external); // retrieve activities (/albums/list.json)
$apiObj->post('/?v?[1-2]?/album/([a-zA-Z0-9]+)/update.json', array('ApiAlbumController', 'update'), EpiApi::external); // update an album (/album/{id}/update.json)
$apiObj->get('/?v?[1-2]?/album/([a-zA-Z0-9]+)/view.json', array('ApiAlbumController', 'view'), EpiApi::external); // retrieve activity (/activity/:id/view.json)

/*
 * Manage endpoints
 * All manage endpoints follow the same convention.
 * /manage/{action}.json
 */
$apiObj->post('/?v?[1-2]?/manage/settings.json', array('ApiManageController', 'settingsPost'), EpiApi::external); // update features (/manage/settings.json)

/*
 * Notification endpoints
 * All notifiation endpoints follow the same convention.
 * /notification/{action}.json
 * API version >= 2
 */
$apiObj->get('/?v?[2]?/notification/?(flash|static)?/view.json', array('ApiNotificationController', 'view'), EpiApi::external); // fetch the next notification in the queue
$apiObj->post('/?v?[2]?/notification/create.json', array('ApiNotificationController', 'create'), EpiApi::external); // create a notifiation
$apiObj->post('/?v?[2]?/notification/delete.json', array('ApiNotificationController', 'delete'), EpiApi::external); // delete the next/current static notifiation

/*
 * Photo endpoints
 * All photo endpoints follow the same convention.
 * Everything in []'s are optional
 * /photo[s]/{id}/{action}[/{additional}].json
 */
$apiObj->post('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/delete.json', array('ApiPhotoController', 'delete'), EpiApi::external); // delete a photo (/photo/{id}/delete.json)
$apiObj->post('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/source/delete.json', array('ApiPhotoController', 'deleteSource'), EpiApi::external); // delete the source files for a photo record (/photo/{id}/source/delete.json)
$apiObj->get('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/edit.json', array('ApiPhotoController', 'edit'), EpiApi::external); // edit form for photo (/photo/{id}/edit.json)
$apiObj->post('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/replace.json', array('ApiPhotoController', 'replace'), EpiApi::external); // update a photo (/photo/{id}/update.json)
$apiObj->post('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/update.json', array('ApiPhotoController', 'update'), EpiApi::external); // update a photo (/photo/{id}/update.json)
$apiObj->get('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/?(.+)?/view.json', array('ApiPhotoController', 'view'), EpiApi::external); // get a photo's information (/photo/[{options}/]{id}/view.json)
$apiObj->get('/?v?[1-2]?/photos/?(.+)?/list.json', array('ApiPhotoController', 'list_'), EpiApi::external); // get all photos / optionally filter (/photos[/{options}]/view.json)
$apiObj->get('/?v?[1-2]?/photos/(.+)/share.json', array('ApiPhotoController', 'share'), EpiApi::external); // get all photos / optionally filter (/photos[/{options}]/view.json)
$apiObj->post('/?v?[1-2]?/photos/(.+)/share.json', array('ApiPhotoController', 'sharePost'), EpiApi::external); // get all photos / optionally filter (/photo/:id/delete.json)
$apiObj->post('/?v?[1-2]?/photos/delete.json', array('ApiPhotoController', 'deleteBatch'), EpiApi::external); // delete multiple photos (/photos/delete.json)
$apiObj->post('/?v?[1-2]?/photos/update.json', array('ApiPhotoController', 'updateBatch'), EpiApi::external); // update multiple photos (/photos/update.json)
$apiObj->get('/?v?[1-2]?/photos/update.json', array('ApiPhotoController', 'updateBatchForm'), EpiApi::external); // update multiple photos (/photos/update.json)
$apiObj->post('/?v?[1-2]?/photo/upload.json', array('ApiPhotoController', 'upload'), EpiApi::external); // upload a photo
$apiObj->post('/?v?[1-2]?/photos/upload/confirm.json', array('ApiPhotoController', 'uploadConfirm'), EpiApi::external); // confirmaton after upload
$apiObj->get('/?v?[1-2]?/photos/upload/token/dialog.json', array('ApiPhotoController', 'uploadTokenDialog'), EpiApi::external); // upload a photo
$apiObj->post('/?v?[1-2]?/photos/upload/([a-z0-9]{10})/notify.json', array('ApiPhotoController', 'uploadNotify'), EpiApi::external); // confirmaton after upload
$apiObj->get('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/url/(\d+)x(\d+)x?([A-Zx]*)?.json', array('ApiPhotoController', 'dynamicUrl'), EpiApi::external); // generate a dynamic photo url (/photo/{id}/url/{options}.json) TODO, make internal for now
$apiObj->get('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/nextprevious/?(.+)?.json', array('ApiPhotoController', 'nextPrevious'), EpiApi::external); // get a photo's next/previous (/photo/{id}/nextprevious[/{options}].json)
//$apiObj->post('/photo/([a-zA-Z0-9]+)/create/([a-z0-9]+)/([0-9]+)x([0-9]+)x?(.*).json', array('ApiPhotoController', 'dynamic'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/photo/([a-zA-Z0-9]+)/transform.json', array('ApiPhotoController', 'transform'), EpiApi::external); // transform a photo

/*
 * Share endpoints
 */
$apiObj->get('/?v?[1-2]?/share/(album|photo)/(.+)/view.json', array('ApiShareController', 'view'), EpiApi::external); // sharing form (/share/:type]/:ids/view.json)
$apiObj->post('/?v?[1-2]?/share/(album|photo)/(.+)/send.json', array('ApiShareController', 'send'), EpiApi::external); // send share email (/shrae/:type/:ids/send.json)

/*
 * Tag endpoints
 * All tag endpoints follow the same convention.
 * Everything in []'s are optional
 * /tag[s][/{id}/]{action}.json
 */
$apiObj->post('/?v?[1-2]?/tag/create.json', array('ApiTagController', 'create'), EpiApi::external); // post a tag (/tag/{id}/update.json)
$apiObj->post('/?v?[1-2]?/tag/(.+)/delete.json', array('ApiTagController', 'delete'), EpiApi::external); // post a tag (/tag/{id}/update.json)
$apiObj->post('/?v?[1-2]?/tag/(.+)/update.json', array('ApiTagController', 'update'), EpiApi::external); // post a tag (/tag/{id}/update.json)
$apiObj->get('/?v?[1-2]?/tag/(.+)/view.json', array('ApiTagController', 'view'), EpiApi::external); // retrieve tags
$apiObj->get('/?v?[1-2]?/tags/list.json', array('ApiTagController', 'list_'), EpiApi::external); // retrieve tags

/*
 * Token endpoints
 * All token endpoints follow the same convention.
 * Everything in []'s are optional
 * /tag[s][/{id}/]{action}.json
 */
$apiObj->post('/?v?[1-2]?/token/(album|photo)/([a-zA-Z0-9]+)/create.json', array('ApiTokenController', 'create'), EpiApi::external); // create a sharing token (/token/:type/:data/create.json)
$apiObj->post('/?v?[1-2]?/token/([a-zA-Z0-9]+)/delete.json', array('ApiTokenController', 'delete'), EpiApi::external); // delete a sharing token (/token/:id/delete.json)
$apiObj->get('/?v?[1-2]?/token/([a-zA-Z0-9]+)/view.json', array('ApiTokenController', 'view'), EpiApi::external); // get an existing sharing token (/token/:id/view.json)
$apiObj->get('/?v?[1-2]?/token/(album|photo)/([a-zA-Z0-9]+)/list.json', array('ApiTokenController', 'listByTarget'), EpiApi::external); // list sharing tokens for a target (/token/:type/:data/view.json)
$apiObj->get('/?v?[1-2]?/tokens/list.json', array('ApiTokenController', 'list_'), EpiApi::external); // list sharing tokens

/*
 * Resource mapping endpoints
 * All shortener endpoints follow the same convention.
 * Everything in []'s are optional
 * /s[/{id}]/{action}.json
 */
$apiObj->post('/?v?[1-2]?/s/create.json', array('ApiResourceMapController', 'create'), EpiApi::external); // create a resource map (/s/create.json)
$apiObj->get('/?v?[1-2]?/s/([a-z0-9]+)/view.json', array('ApiResourceMapController', 'view'), EpiApi::external); // create a resource map (/s/{id}/view.json)

/*
 * User endpoints
 * All user endpoints follow the same convention.
 * Everything in []'s are optional
 * /user[/{provider}]/{action}.json
 */
$apiObj->post('/?v?[1-2]?/user/([a-z0-9]+)/login.json', array('ApiUserController', 'login'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/user/logout.json', array('ApiUserController', 'logout'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/user/password/request.json', array('ApiUserController', 'passwordRequest'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/user/password/reset.json', array('ApiUserController', 'passwordReset'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/user/profile.json', array('ApiUserController', 'profile'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/user/profile.json', array('ApiUserController', 'profilePost'), EpiApi::external);

/*
 * OAuth endpoints
 * All oauth endpoints follow the same convention.
 * /v{version}/oauth[/{id}]/{action}
 */
$apiObj->get('/?v?[1-2]?/oauth/([a-zA-Z0-9]+)/view.json', array('ApiOAuthController', 'view'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/oauth/([a-zA-Z0-9]+)/markup.json', array('ApiOAuthController', 'markup'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/oauth/list.json', array('ApiOAuthController', 'list_'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/oauth/([a-zA-Z0-9]+)/delete.json', array('ApiOAuthController', 'delete'), EpiApi::external);

/*
 * Group endpoints follow the same convention.
 * Everything in []'s are optional
 * /group[s]/{action}.json
 */
$apiObj->post('/?v?[1-2]?/group/create.json', array('ApiGroupController', 'create'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/group/([a-zA-Z0-9]+)/delete.json', array('ApiGroupController', 'delete'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/group/form.json', array('ApiGroupController', 'form'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/group/([a-zA-Z0-9]+)/update.json', array('ApiGroupController', 'update'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/group/([a-zA-Z0-9]+)/view.json', array('ApiGroupController', 'view'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/groups/list.json', array('ApiGroupController', 'list_'), EpiApi::external);

/*
 * Plugin endpoints follow the same convention.
 * Everything in []'s are optional
 * /plugin[s]/{action}.json
 */
$apiObj->post('/?v?[1-2]?/plugin/([a-zA-Z0-9]+)/update.json', array('ApiPluginController', 'update'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/plugin/([a-zA-Z0-9]+)/(activate|deactivate).json', array('ApiPluginController', 'updateStatus'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/plugin/([a-zA-Z0-9]+)/view.json', array('ApiPluginController', 'view'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/plugins/list.json', array('ApiPluginController', 'list_'), EpiApi::external);

/*
 * Webhook endpoints follow the same convention.
 * Everything in []'s are optional
 * /webhook[s][/{id}]/{action}.json
 */
$apiObj->post('/?v?[1-2]?/webhook/create.json', array('ApiWebhookController', 'create'), EpiApi::internal);
$apiObj->post('/?v?[1-2]?/webhook/([a-zA-Z0-9]+)/delete.json', array('ApiWebhookController', 'delete'), EpiApi::external);
$apiObj->post('/?v?[1-2]?/webhook/([a-zA-Z0-9]+)/update.json', array('ApiWebhookController', 'update'), EpiApi::internal);
$apiObj->get('/?v?[1-2]?/webhook/([a-zA-Z0-9]+)/view.json', array('ApiWebhookController', 'view'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/webhooks/?(.*)?/list.json', array('ApiWebhookController', 'list_'), EpiApi::internal);

/*
 * System endpoints follow the same convention.
 * Everything in []'s are optional
 * /system/{action}.json
 */
$apiObj->get('/?v?[1-2]?/system/diagnostics.json', array('ApiController', 'diagnostics'), EpiApi::external);
$apiObj->get('/?v?[1-2]?/system/version.json', array('ApiController', 'version'), EpiApi::external);

Open Photo API
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### How do I authenticate?

The Open Photo API uses [OAuth1.0a][oauth1.0a] for authentication. See the complete [guide on authentication][authentication] for details.

### What's the response format?

Every API endpoint returns a JSON response in a [standard envelope][Envelope].

    {
      message: "A string describing the response",
      code: 200,
      result: {
        foo: "bar"
      }
    }

### API Endpoints

#### Test / diagnostics endpoints
1.  [GET /hello.json][GetHelloWorld]
    Test endpoint.

#### Action endpoings (comments, favorites, etc)
1.  [POST /action/:id/:type/create.json][PostActionCreate]
    Create an action.
1.  [POST /action/:id/delete.json][PostActionDelete]
    Delete an action.

#### Photo endpoints
1.  [POST /photos/:id/delete.json][PostPhotoDelete]
    Delete a user's photo.
1.  [POST /photos/:id/update.json][PostPhotoUpdate]
    Update data on a user's photo.
1.  [GET /photo/:id/view.json][GetPhoto]
    Get a user's photo.
1.  [GET /photos/list.json][GetPhotos]
    Get a list of the user's photos.
1.  [GET /photo/:id/nextprevious.json][GetPhotoNextPrevious]
    Get the next and previous photo.
1.  [POST /photo/upload.json][PostPhotoUpload]
    Upload a new photo.

#### Tag endpoints
1.  [GET /tags/list.json][GetTags]
    Get a user's tags.
1.  [POST /tag/:id/create.json][PostTagCreate]
    Create a tag for the user.
1.  [POST /tag/:id/update.json][PostTagUpdate]
    Modify meta data for a user's tag.

#### Group endpoints
1.  [GET /group/:id/view.json][GetGroup]
    Get a group.
1.  [GET /groups/list.json][GetGroups]
    Get a listing of a user's groups.
1.  [POST /group/create.json][PostGroupCreate]
    Create a group.
1.  [POST /group/delete.json][PostGroupDelete]
    Delete a group.
1.  [POST /group/update.json][PostGroupUpdate]
    Update a group.

#### Webhook endpoints
1.  [POST /webhook/subscribe][PostWebHookSubscribe]
    Update an eixsting webhook.
1.  [GET /webhook/:id/view.json][GetWebhook]
    Get a user's webhook by id.
1.  [POST /webhook/:id/delete.json][PostWebHookDelete]
    Delete an existing webhook.

[Envelope]: Envelope.markdown
[GetHelloWorld]: GetHelloWorld.markdown
[GetPhotos]: GetPhotos.markdown
[GetPhoto]: GetPhoto.markdown
[GetPhotoNextPrevious]: GetPhotoNextPrevious.markdown
[PostPhotoDelete]: PostPhotoDelete.markdown
[PostPhotoUpdate]: PostPhotoUpdate.markdown
[PostPhotoUpload]: PostPhotoUpload.markdown
[PostActionCreate]: PostActionCreate.markdown
[PostActionDelete]: PostActionDelete.markdown
[GetTags]: GetTags.markdown
[PostTagCreate]: PostTagCreate.markdown
[PostTagUpdate]: PostTagUpdate.markdown
[GetGroup]: GetGroup.markdown
[GetGroups]: GetGroups.markdown
[PostGroupCreate]: PostGroupCreate.markdown
[PostGroupUpdate]: PostGroupUpdate.markdown
[PostGroupDelete]: PostGroupDelete.markdown
[GetWebhook]: GetWebhook.markdown
[PostWebhookSubscribe]: PostWebhookSubscribe.markdown
[PostWebhookDelete]: PostWebhookDelete.markdown
[authentication]: Authentication.markdown
[oauth1.0a]: http://oauth.net/core/1.0a/

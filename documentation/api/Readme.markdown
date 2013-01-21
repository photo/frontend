Open Photo API
=======================


----------------------------------------

### How do I authenticate?

The Open Photo API uses [OAuth1.0a](http://oauth.net/core/1.0a/) for authentication. See the complete [guide on authentication](http://theopenphotoproject.org/documentation/api/Authentication) for details.

### What's the response format?

Every API endpoint returns a JSON response in a [standard envelope](http://theopenphotoproject.org/documentation/api/Envelope).

    {
      message: "A string describing the response",
      code: 200,
      result: {
        foo: "bar"
      }
    }

### API Endpoints

#### Test / diagnostics endpoints
1.  [GET /hello.json](http://theopenphotoproject.org/documentation/api/GetHelloWorld)
    Test endpoint.

#### Action endpoings (comments, favorites, etc)
1.  [POST /action/:id/:type/create.json](http://theopenphotoproject.org/documentation/api/PostActionCreate)
    Create an action.
1.  [POST /action/:id/delete.json](http://theopenphotoproject.org/documentation/api/PostActionDelete)
    Delete an action.

#### Photo endpoints
1.  [POST /photos/:id/delete.json](http://theopenphotoproject.org/documentation/api/PostPhotoDelete)
    Delete a user's photo.
1.  [POST /photos/:id/update.json](http://theopenphotoproject.org/documentation/api/PostPhotoUpdate)
    Update data on a user's photo.
1.  [GET /photo/:id/view.json](http://theopenphotoproject.org/documentation/api/GetPhoto)
    Get a user's photo.
1.  [GET /photos/list.json](http://theopenphotoproject.org/documentation/api/GetPhotos)
    Get a list of the user's photos.
1.  [GET /photo/:id/nextprevious.json](http://theopenphotoproject.org/documentation/api/GetPhotoNextPrevious)
    Get the next and previous photo.
1.  [POST /photo/upload.json](http://theopenphotoproject.org/documentation/api/PostPhotoUpload)
    Upload a new photo.

#### Tag endpoints
1.  [GET /tags/list.json](http://theopenphotoproject.org/documentation/api/GetTags)
    Get a user's tags.
1.  [POST /tag/create.json](http://theopenphotoproject.org/documentation/api/PostTagCreate)
    Create a tag for the user.
1.  [POST /tag/:id/update.json](http://theopenphotoproject.org/documentation/api/PostTagUpdate)
    Modify meta data for a user's tag.

#### Group endpoints
1.  [GET /group/:id/view.json](http://theopenphotoproject.org/documentation/api/GetGroup)
    Get a group.
1.  [GET /groups/list.json](http://theopenphotoproject.org/documentation/api/GetGroups)
    Get a listing of a user's groups.
1.  [POST /group/create.json](http://theopenphotoproject.org/documentation/api/PostGroupCreate)
    Create a group.
1.  [POST /group/delete.json](http://theopenphotoproject.org/documentation/api/PostGroupDelete)
    Delete a group.
1.  [POST /group/update.json](http://theopenphotoproject.org/documentation/api/PostGroupUpdate)
    Update a group.

#### Webhook endpoints
1.  [POST /webhook/subscribe](http://theopenphotoproject.org/documentation/api/PostWebHookSubscribe)
    Update an eixsting webhook.
1.  [GET /webhook/:id/view.json](http://theopenphotoproject.org/documentation/api/GetWebhook)
    Get a user's webhook by id.
1.  [POST /webhook/:id/delete.json](http://theopenphotoproject.org/documentation/api/PostWebHookDelete)
    Delete an existing webhook.

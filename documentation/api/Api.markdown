Open Photo API
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### How do I authenticate?

The Open Photo API uses [OAuth1.0a][oauth1.0a] for authentication. See the complete [guide on authentication][authentication] for details.

_NOTE:_ OAuth2 isn't yet enabled.

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

1.  [GET /hello.json][GetHelloWorld]
    Test endpoint.
1.  [GET /photos/list.json][GetPhotos]
    Get a list of the user's photos.
1.  [GET /photo/:id/view.json][GetPhoto]
    Get a user's photo.
1.  [POST /photo/upload.json][PostPhotoUpload]
    Upload a new photo.
1.  [GET /tags/list.json][GetTags]
    Get a user's tags.
1.  [POST /tag/:id/update.json][PostTag]
    Modify meta data for a user's tag.

[Envelope]: Envelope.markdown
[GetHelloWorld]: GetHelloWorld.markdown
[GetPhotos]: GetPhotos.markdown
[GetPhoto]: GetPhoto.markdown
[GetTags]: GetTags.markdown
[PostPhotoUpload]: PostPhotoUpload.markdown
[PostTag]: PostTag.markdown
[authentication]: Authentication.markdown
[oauth1.0a]: http://oauth.net/core/1.0a/

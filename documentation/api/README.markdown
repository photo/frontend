Open Photo API
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### How do I authenticate?

The Open Photo API uses [OAuth2][oauth2] for authentication. See the complete [guide on authentication][authentication] for details.

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

1.  [GET /photos.json][GetPhotos]
    Get a list of the user's photos.
1.  [GET /photo/:id.json][GetPhoto]
    Get a user's photo.
1.  [POST /photo/upload.json][PostPhotoUpload]
    Upload a new photo.

[Envelope]: api/Envelope.markdown
[GetPhotos]: api/GetPhotos.markdown
[GetPhoto]: api/GetPhoto.markdown
[PostPhotoUpload]: api/PostPhotoUpload.markdown
[authentication]: api/Authentication.markdown
[oauth2]: http://wiki.oauth.net/w/page/25236487/OAuth-2

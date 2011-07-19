Documentation
=======================
#### OpenPhoto, a photo service for the masses

Social entries such as comments and favorites are stored in their own database (or table). The structure the OpenPhoto application expects from the adapter is below.

### Schema

    [
      {
        id: (string),
        photoId: (string), // FK Photos.id
        name: (string),
        avatar: (string),
        website: (string),
        targetUrl: (string), // link to the target which this row is a child of
        permalink: (string), // link to this social element (comment, favorite)
        type: (string), // comment, favorite
        value: (string),
        datetime: (string),
        timestamp: (string),
        status: (int)
      }
    ]


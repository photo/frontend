Documentation
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### What's a Social object for?

The Social object stores social actions taken on a user's [Photo][Photo].
This includes comments and favorites and could include other social actions in the future.

----------------------------------------

### Schema for a Social object

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
        datePosted: (string),
        status: (int)
      }
    ]

----------------------------------------

### Schema description

  * id, base 36 value of a base 10 auto-incremented value
  * photoId, a foreign key to the [Photo][Photo] object this action was taken on
  * name, name of the user taking this action
  * avatar, URL to an image which represents this user's avatar or profile photo
  * website, URL to the user's website
  * targetUrl, URL to the [Photo][Photo] referenced by photoId in this entry
  * permalink, URL to this action
  * type, Enumeration determining the type of action - comment or favorite
  * value, Content of the action such as the text of a comment
  * datePosted, UNIX timestamp of the action
  * status, Binary value if the action is active or not


[User]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/User.markdown
[Photo]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Photo.markdown
[Social]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Social.markdown

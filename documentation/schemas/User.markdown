Documentation
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### What's a User object for?

The User object stores information for a given user.
This includes personal information like name and email address but it also includes the last uploaded [Photo][Photo] id.

----------------------------------------

### Schema for a User object

    {
      name: (string),
      email: (string),
      website: (string),
      lastPhotoId: (string)
    }

----------------------------------------

### Schema description

  * name, the user's full name
  * email, the user's email address
  * website, the user's website
  * lastPhotoId, most recent photo id

[User]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/User.markdown
[Photo]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Photo.markdown
[Action]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Action.markdown

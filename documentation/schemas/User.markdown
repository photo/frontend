Schema for a User object
=======================


----------------------------------------

### What's a User object for?

The User object stores information for a given user.
This includes the last uploaded [Photo][Photo]  and [Action][Action] id.

----------------------------------------

### Schema for a User object

    {
      id: (string),
      lastPhotoId: (string),
      lastActionId: (string)
    }

----------------------------------------

### Schema description

  * id, the user id
  * last{Object}Id, most recent [base 32](http://en.wikipedia.org/wiki/Base32#base32hex) id for the specified object (photo, action, activity, webhook, etc.)

[User]: http://theopenphotoproject.org/documentation/schemas/User
[Photo]: http://theopenphotoproject.org/documentation/schemas/Photo
[Action]: http://theopenphotoproject.org/documentation/schemas/Action

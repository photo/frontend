Schema for a Tag object
=======================


----------------------------------------

### What's a Tag object for?

A Tag object stores information about a specific tag.
This includes but is not limited to the number of objects containing this tag.
The Tag objects schema is loose meaning that it can be flexible but at minimum it contains an `id` and `count`.

For example, the tag `sunnyvale` can have a `latitude` and `longitude` property.

----------------------------------------

### Schema for a Tag object

    {
      id: (string),
      count: (int),
      email: (string),
      latitude: (float),
      longitude: (float)
    }

----------------------------------------

### Schema description

  * id, [Base 32](http://en.wikipedia.org/wiki/Base32#base32hex) value of a base 10 auto-incremented value
  * count, The number of objects with this tag
  * email, Email address if applicable
  * latitude, Latitude if applicable
  * longitude, Longitude if applicable

[User]: http://theopenphotoproject.org/documentation/schemas/User
[Photo]: http://theopenphotoproject.org/documentation/schemas/Photo
[Action]: http://theopenphotoproject.org/documentation/schemas/Action
[Tag]: http://theopenphotoproject.org/documentation/schemas/Tag

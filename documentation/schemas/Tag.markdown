Documentation
=======================
#### OpenPhoto, a photo service for the masses

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
      count: (int)
    }

----------------------------------------

### Schema description

  * id, Base 36 value of a base 10 auto-incremented value
  * count, The number of objects with this tag

[User]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/User.markdown
[Photo]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Photo.markdown
[Action]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Action.markdown
[Tag]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Tag.markdown


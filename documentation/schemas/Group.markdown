Documentation
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### What's a Group object for?

The Group object stores information for a given group.
A Group consists of an `id`, `name` and array of `email addresses`.

----------------------------------------

### Schema for a Group object

    {
      id: (string),
      appId: (string),
      name: (string),
      members: (set)
    }

----------------------------------------

### Schema description

  * id, a string idenfier
  * appId, the appId which created this group
  * name, the name of the group
  * members: a set of email addresses who belong to this group

[User]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/User.markdown
[Photo]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Photo.markdown
[Action]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Action.markdown


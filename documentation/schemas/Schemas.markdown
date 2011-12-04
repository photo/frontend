OpenPhoto
=======================


----------------------------------------

### What are schemas?

Schemas are the blueprint for various objects in the OpenPhoto platform.
The most logical example is the [Photo][Photo] object which represents a single photo in a user's photo collection.
Their photo library consists of a collection of [Photo][Photo] objects.
Other examples are a [User][User] object and [Action][Action] object.

----------------------------------------

### Why are schemas important?

By defining these schemas we enable different OpenPhoto applications to share the same data.
Since a [Photo][Photo] object has a predictable set of properties it means that any application can easily interact with it.

----------------------------------------

### Available schemas

1. [User][User] - Settings for a user.
1. [Credential][Credential] - Properties for a user's OAuth credential.
1. [Photo][Photo] - Properties for a single photo.
1. [Action][Action] - Comments and favorites on a photo.
1. [Tag][Tag] - Meta information for tags.

----------------------------------------

[User]: User.markdown
[Credential]: Credential.markdown
[Photo]: Photo.markdown
[Action]: Action.markdown
[Tag]: Tag.markdown

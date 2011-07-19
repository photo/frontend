OpenPhoto
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### What are schemas?

Schemas are the blueprint for various objects in the OpenPhoto platform. 
The most logical example is the [Photo][Photo] object which represents a user's single photo. 
Their photo library consists of a collection of [Photo][Photo] objects. 
Other examples are a [User][User] object and [Social][Social] object.

----------------------------------------

### Why are schemas important?

By defining these schemas we enable different OpenPhoto applications to share the same data.
Since a [Photo][Photo] object has a predictable set of properties it means that any application can easily interact with it.

----------------------------------------

### Available schemas

1. [User][User] - Settings for a user.
1. [Photo][Photo] - Properties for a single photo.
1. [Social][Social] - Comments and favorites on a photo.

----------------------------------------

[User]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/User.markdown
[Photo]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Photo.markdown
[Social]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Social.markdown


Schema for a Action object
=======================

----------------------------------------

### What's an Action object for?

The Social object stores social actions taken on a user's [Photo][Photo].
This includes comments and favorites and could include other social actions in the future.

----------------------------------------

### Schema for a Action object

    {
      id: (string),
      appId: (string),
      targetId: (string), // FK Photos.id or Social.id
      targetType: (string), // photo, social
      email: (string),
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

----------------------------------------

### Schema description

  * id, [base 32](http://en.wikipedia.org/wiki/Base32#base32hex) value of a base 10 auto-incremented value
  * appId, A string identifing the application creating this entry
  * targetId, a foreign key to a [Photo][Photo] or [Action][Action] object this action was taken on
  * targetType, a reference to the target type: photo or social
  * email, email address of the user taking this action
  * name, name of the user taking this action
  * avatar, URL to an image which represents this user's avatar or profile photo
  * website, URL to the user's website
  * targetUrl, URL to the [Photo][Photo] referenced by photoId in this entry
  * permalink, URL to this action
  * type, Enumeration determining the type of action - comment or favorite
  * value, Content of the action such as the text of a comment
  * datePosted, UNIX timestamp of the action
  * status, Binary value if the action is active or not


[User]: http://theopenphotoproject.org/documentation/schemas/User
[Photo]: http://theopenphotoproject.org/documentation/schemas/Photo
[Action]: http://theopenphotoproject.org/documentation/schemas/Action

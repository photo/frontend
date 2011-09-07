Documentation
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### What's a Credential object for?

The Credential object stores permissioning OAuth tokens granted by the user to various applications.

Due to the distrubuted nature of the platform the model for applications and users is flattened and not relational. For every application there will be exactly 1 user.

----------------------------------------

### Schema for a User object

    {
      id: (string),
      name: (string),
      image: (string),
      client_secret: (string),
      user_token: (string),
      user_secret: (string),
      permissions: (set),
      verifier: (string),
      type: (enum),
      status: (bool)
    }

----------------------------------------

### Schema description

  * id, A (quasi-)public token which identifies the application
  * name, A human readable name describing the client
  * image, The base64 encoded version of a 100x100 pixel image
  * client_secret, A shared secret used to verify requests originated from the application
  * user_token, A (quasi-)public token which idenfies the user
  * user_secret, A shared secret to verify that the request originated from the application for the user
  * permissions, A set of permissions the credential has (create, read, write, delete)
  * verifier, A verification string to ensure that the `oauth_callback` parameter wasn't spoofed
  * type, An enumerated field specifying the type of token (unauthorized_request, request, access)
  * status, Numeric representation of the status (0=deleted, 1=active)

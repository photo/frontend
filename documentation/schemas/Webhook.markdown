Schema for a Webhook object
=======================


----------------------------------------

### What's a Webhook object for?

Webhooks allow each OpenPhoto instance to be _programmable_.
Developers use webhooks to be notified of events so they can process them.

http://wiki.webhooks.org/w/page/13385124/FrontPage

----------------------------------------

### Schema for a Action object

    {
      id: (string),
      appId: (string),
      callback: (string),
      topic: (enum),
      verifyToken: (string),
      challenge: (string),
      secret: (string)
    }

----------------------------------------

### Schema description

  * id, A random unique 40 byte string to identify the webhook
  * appId, A string identifing the application creating this entry
  * callback, URL to which the event information is POSTed
  * topic, An enumerated string with predefined values (i.e. photoupload, photoupdate, newcomment, etc.)
  * verifyToken, A string supplied by the subscriber **not used for sync**
  * challenge, A challenge string used in the verification process **not used for sync**
  * secret, An optional subscriber supplied secret for request signing

Create/Subscribe to Webhook
=======================


----------------------------------------

1. [Purpose][purpose]
1. [Endpoint][endpoint]
1. [Parameters][parameters]

----------------------------------------

<a name="purpose"></a>
### Purpose of the POST Webhook create API

Use this API to create a new webhook for a user.

This API differs from our others in that it's both interactive and does not return a [Standard Envelope](http://theopenphotoproject.org/documentation/api/Envelope). These are the steps required to complete a webhook subscription.

<a name="verification"></a>

1.  The consumer _(you)_ makes a POST request to the provider _(the API host)_ to `http://apihost.com/webhook/subscribe` with the <a href="#">required parameters</a>.
1.  The provider makes a GET request back to your `callback` URL passing along a `mode`, `topic`, and `challenge` parameter. A `verifyToken` parameter is passed back if originally supplied.
1.  The consumer must validate that the subscription was intended (typically using the `verifyToken`) and print out the `challenge` value with a HTTP 200 response code.
1.  If the consumer response is a HTTP 200 and the content body was equal to `challenge` then the provider completes the subscription.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    POST /webhook/subscribe

<a name="parameters"></a>
### Parameters

1.  callback (required), A URL to POST to. This also needs to handle GET calls for the <a href="#verification">verification process</a>.
1.  topic (required), _i.e. photo.upload_ - The topic which you'd like to subscribe to.
1.  mode (required), Only _sync_ is supported at this time.
1.  verifyToken (optional), A provider generated string to which is passed back as part of the <a href="#verification">verification process</a>.

----------------------------------------


[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters

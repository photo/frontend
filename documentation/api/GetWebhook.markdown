Get Webhook
=======================


----------------------------------------

1. [Purpose][purpose]
1. [Endpoint][endpoint]
1. [Parameters][parameters]
1. [Examples][examples]
  * [Command line][example-cli]
  * [PHP][example-php]
1. [Response][response]
  * [Sample][sample]

----------------------------------------

<a name="purpose"></a>
### Purpose of the GET Webhook API

Use this API to get a user's Webhook.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    GET /webhook/:id/view.json

<a name="parameters"></a>
### Parameters

_None_

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    source secrets.sh
    ./openphoto -p -h current.trovebox.com -e /webhook/abcdefghijklmnopqrstuvwxyz/view.json

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->get("/webhook/abcdefghijklmnopqrstuvwxyz/view.json");

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, A [Webhook][Webhook] object

<a name="sample"></a>
#### Sample

    {
      "message" : "Your group",
      "code" : 200,
      "result" : {
        id: "abcdefghijklmnopqrstuvwxyz",
        appId: "current.trovebox.com",
        callback: "http://somehost.com/somepath",
        topic: "photo.upload",
        verifyToken: "qazwsxedcrfvz",
        challenge: "plmoknijbuhv",
        secret: "rfvtgbyhn"
      }
    }


[Webhook]: http://theopenphotoproject.org/documentation/schemas/Webhook
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-cli]: #example-cli
[example-php]: #example-php
[response]: #response
[sample]: #sample
[openphoto-php]: https://github.com/photo/openphoto-php

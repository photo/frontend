Delete Webhook
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
### Purpose of the POST Webhook delete API

Use this API to delete an existing webhook for a user.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    POST /webhook/:id/delete.json

<a name="parameters"></a>
### Parameters

_None_

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    source secrets.sh
    ./openphoto -p -X POST -h current.trovebox.com -e /webhook/abcdefghijklmnopqrstuvwxyz/delete.json

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->post('/webhook/abcdefghijklmnopqrstuvwxyz/delete.json');

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _204_ on success
* _result_, A boolean

<a name="sample"></a>
#### Sample

    {
      "message" : "Webhook deleted successfully",
      "code" : 204,
      "result" : true
    }


[Webhook]: http://theopenphotoproject.org/documentation/schemas/Webhook
[webhookverification]: http://theopenphotoproject.org/documentation/faq/WebhookVerification
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-cli]: #example-cli
[example-php]: #example-php
[response]: #response
[sample]: #sample
[openphoto-php]: https://github.com/photo/openphoto-php


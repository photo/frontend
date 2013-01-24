Hello World
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
### Purpose of the Get Hello World API

This endpoint is used to test connectivity and/or authentication. Any parameters passed in the query string are returned in the response.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: optional_

    GET /hello.json

<a name="parameters"></a>
### Parameters

1.  auth (optional), Pass this in with a value of `true` to test OAuth requests.

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    # without authentication
    ./openphoto -p -h current.trovebox.com -e /hello.json

    # with authentication
    ./openphoto -p -h current.trovebox.com -e /hello.json -F 'auth=true'

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    // without authentication
    $client = new OpenPhotoOAuth($host);
    $response = $client->get("/hello.json");

    // with authentication
    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->get("/hello.json", array('auth' => 'true'));

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, Any GET parameters passed in to the request plus `__route__`.

<a name="sample"></a>
#### Sample

    {
      "message":"Hello, world!",
      "code":200,
      "result":
      {
        "__route__":"\/hello.json"
      }
    }


[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-cli]: #example-cli
[example-php]: #example-php
[response]: #response
[sample]: #sample
[openphoto-php]: https://github.com/photo/openphoto-php

Get Tags
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
### Purpose of the Get Tags API

Use this API to get a user's tags.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: optional_

    GET /tags/list.json

<a name="parameters"></a>
### Parameters

_None_

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    ./openphoto -p -h current.trovebox.com -e /tags/list.json

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->get("/tags/list.json");

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, An array of [Tag][Tag] objects

<a name="sample"></a>
#### Sample

    {
      "message":"",
      "code":200,
      "result":
      [
        {
          "id": "mountain",
          "count": 1
        },
        {
          "id": "jaisen",
          "count": 10,
          "email": "jaisen@jmathai.com"
        },
        {
          "id": "New York",
          "count": 9,
          "latitude": 12.3456,
          "longitude": 78.9012
        },
        {
          "id": "Sunnyvale",
          "count":23
          "latitude": 13.579,
          "longitude": 24.68
        },
        ....
      ]
    }


[Tag]: http://theopenphotoproject.org/documentation/schemas/Tag
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-cli]: #example-cli
[example-php]: #example-php
[response]: #response
[sample]: #sample
[openphoto-php]: https://github.com/photo/openphoto-php

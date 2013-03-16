Delete Action
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
### Purpose of the delete action API

Use this API to delete an action on a photo. This includes comments and favorites.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    POST /action/photo/:targetId/delete.json

<a name="parameters"></a>
### Parameters

_N/A_

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    ./openphoto -p -X POST -h current.trovebox.com -e /action/photo/a/delete.json

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->post("/action/photo/a/delete.json");

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _204_ on success
* _result_, boolean

<a name="sample"></a>
#### Sample

    {
      "message":"",
      "code":204,
      "result": true
    }


[Action]: http://theopenphotoproject.org/documentation/schemas/Action
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-cli]: #example-cli
[example-php]: #example-php
[response]: #response
[sample]: #sample
[openphoto-php]: https://github.com/photo/openphoto-php

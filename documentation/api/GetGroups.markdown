Get Groups
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
### Purpose of the Get Groups API

Use this API to get a list of the user's groups.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    GET /groups/list.json

<a name="parameters"></a>
### Parameters

_None_

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    source secrets.sh
    ./openphoto -p -h current.trovebox.com -e /groups/list.json

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->get("/groups/list.json");

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, An array of [Group][Group] objects

<a name="sample"></a>
#### Sample

    {
      "message" : "A list of your groups",
      "code" : 200,
      "result" : [
        {
          "id" : "e",
          "Name" : "e",
          "name" : "Everyone else",
          "members" : [
            "rachel.mathai@yahoo.com",
            "jaisen@jmathai.com",
            "joe@joe.com"
          ],
          "appId" : "openphoto-frontend"
        },
        {
          "id" : "d",
          "Name" : "d",
          "name" : "Rachel and Jaisen",
          "members" : [
            "rachel.mathai@yahoo.com",
            "jaisen@jmathai.com"
          ],
          "appId" : "openphoto-frontend"
        },
        {
          "id" : "f",
          "Name" : "f",
          "name" : "Random",
          "members" : [
            "joe@joe.com"
          ],
          "appId" : "openphoto-frontend"
        }
      ]
    }


[Group]: http://theopenphotoproject.org/documentation/schemas/Group
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-cli]: #example-cli
[example-php]: #example-php
[response]: #response
[sample]: #sample
[openphoto-php]: https://github.com/photo/openphoto-php

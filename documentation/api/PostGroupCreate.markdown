Create Group
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
### Purpose of the create Group API

Use this API to create a group.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    POST /group/create.json

<a name="parameters"></a>
### Parameters

1.  name (required), The name of the group to create
1.  members (optional), _i.e. jaisen@jmathai.com,hello@openphoto.me_ - A comma delimited list of email addresses

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    ./openphoto -p -X POST -h current.trovebox.com -e /group/create.json -F 'name=My Group' -F 'members=jaisen@jmathai.com'

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->post("/group/create.json", array('name' => 'My Group', 'members' => 'jaisen@jmathai.com'));

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, A [Group][Group] object or FALSE on error

<a name="sample"></a>
#### Sample

    {
      "message":"",
      "code":200,
      "result":
      {
        id: 'a',
        appId: 'current.openphoto.me',
        name: 'My Group',
        members: ['jaisen@jmathai.com','hello@openphoto.me']
      }
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

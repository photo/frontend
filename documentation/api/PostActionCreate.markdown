Create Action
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
### Purpose of the create action API

Use this API to create an action on a photo. This includes comments and favorites.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    POST /action/:targetId/photo/create.json

<a name="parameters"></a>
### Parameters

1.  email (required), Email address of the user performing this action
1.  name (optional), Name of the user performing this action
1.  website (optional), URL of the user performing this action
1.  targetUrl (optional), URL of the object this action is being performed on
1.  permalink (optional), Permalink URL of this action
1.  type (required), _i.e. comment or favorite_ - Type of action
1.  value (required), Text representing the comment or favorite

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    ./openphoto -p -X POST -h current.trovebox.com -e /action/photo/a/create.json -F 'type=comment' -F 'value=Here is my comment' -F 'email=jaisen@jmathai.com'

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->post("/action/photo/a/create.json", array('type' => 'comment', 'value' => 'Here is my comment', 'email' => 'jaisen@jmathai.com'));

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, An [Action][Action] object or FALSE on error

<a name="sample"></a>
#### Sample

    {
      "message":"",
      "code":200,
      "result":
      {
        id: "a",
        appId: "current.trovebox.com",
        targetId: "b",
        targetType: "photo",
        email: "jaisen@jmathai.com",
        type: "comment",
        value: "Here is my comment",
        datePosted: "1318281477",
        status: 1
      }
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

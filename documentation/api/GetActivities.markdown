Get Activities
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
### Purpose of the Get Activities API

Use this API to get a user's activity feed.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: optional_

    GET /activities/list.json

<a name="parameters"></a>
### Parameters

1.  groupBy (optional), Time period to group activities by 

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    ./openphoto -p -h current.trovebox.com -e /activities/list.json

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->get("/tags/activities.json");

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, An array of activities

<a name="sample"></a>
#### Sample without `groupBy`

    {
      "message" : "User's list of activities",
      "code" : 200,
      "result" : [
        { // Photo object
          "id" : "l", // activity id, not photo id. See photo object for photo id
          "owner" : "jaisen+test@jmathai.com",
          "appId" : "openphoto-frontend",
          "type" : "photo-upload",
          "data" : {
            // Photo object
          }
        },
        { // comment
          "id" : "p", // activity id, not photo id. See photo object for photo id
          "owner" : "jaisen+test@jmathai.com",
          "appId" : "openphoto-frontend",
          "type" : "action-create",
          "data" : {
            "targetType" : "photo",
            "target" : {
              // Photo object
            },
            "action" : {
              // Action object
            }
          },
          "permission" : "1",
          "dateCreated" : "1328851975"
        }
      ]
    }

#### Sample with `groupBy`

    {
      "message" : "User's list of activities",
      "code" : 200,
      "result" : {
        "2012020921-photo-upload" : [ // photo uploads
          {
            "id" : "l", // activity id, not photo id. See photo object for photo id
            "type" : "photo-upload",
            "data" : {
              // Photo object
            },
            "permission" : "1",
            "dateCreated" : "1328851361"
          },
          {
            "id" : "m", // activity id, not photo id. See photo object for photo id
            "type" : "photo-upload",
            "data" : {
              // Photo object
            },
            "permission" : "1",
            "dateCreated" : "1328851363"
          }
        ],
        "2012020921-action-create" : [
          {
            "id" : "p", // activity id, not photo id. See photo object for photo id
            "type" : "action-create",
            "data" : {
              "targetType" : "photo",
              "target" : {
                // Photo object
              },
              "action" : {
                // Action object
              }
            },
            "permission" : "1",
            "dateCreated" : "1328851975"
          },
          {
            "id" : "q", // activity id, not photo id. See photo object for photo id
            "owner" : "jaisen+test@jmathai.com",
            "appId" : "openphoto-frontend",
            "type" : "action-create",
            "data" : {
              "targetType" : "photo",
              "target" : {
                // Photo object
              },
              "action" : {
                // Action object
              }
            },
            "permission" : "1",
            "dateCreated" : "1328852131"
          }
        ]
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


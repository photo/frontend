Get Photos
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
### Purpose of the Get Photos API

Use this API to get a set of photos for a user.

_NOTE:_ Always pass in the `returnSizes` parameter for sizes you plan on using. It's the only way to guarantee that a URL for that size will be present in the response. See [Photo Generation](http://theopenphotoproject.org/documentation/faq/PhotoGeneration) for details.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: optional_

    GET /photos.json

<a name="parameters"></a>
### Parameters

1.  returnSizes (optional), (e.g. 20x20 or 30x30xCR,40x40) The photo sizes you'd like in the response. Specify every size you plan on using. [Docs for this parameter](http://theopenphotoproject.org/documentation/faq/ReturnSizes)
1.  generate (optional), (i.e. true or false) Tells the API to generate the sizes from `returnSizes` instead of returning a _create_ URL. [Docs for this parameter](http://theopenphotoproject.org/documentation/faq/ReturnSizes)
1.  pageSize (optional), Number of photos to return per request.
1.  page (optional), Page number when browsing through photos. Starts at 1.
1.  tags (optional), _e.g. dog,cat_ - A comma delimited string of strings.

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    ./openphoto -p -h current.trovebox.com -e /photos/list.json -F 'tags=sunnyvale'

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->get("/photos/list.json", array('tags' => 'sunnyvale'));

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, An array of [Photo][Photo] objects

<a name="sample"></a>
#### Sample

    {
      "message":"",
      "code":200,
      "result":[
        {
          "tags":[
             ""
          ],
          "pathBase":"\/base\/201107\/1311045184-opme7Z0WBh.jpg",
          "appId":"opme",
          "host":"testjmathai1.s3.amazonaws.com",
          "dateUploadedMonth":"07",
          "status":"1",
          "hash":"fba49a238426ac3485af6d69967ccd2d08c1fe5c",
          "width":"569",
          "dateTakenMonth":"07",
          "dateTakenDay":"18",
          "permission":"0",
          "pathOriginal":"\/original\/201107\/1311045184-opme7Z0WBh.jpg",
          "exifCameraMake":"",
          "size":"0",
          "dateTaken":"1311045184",
          "height":"476",
          "views":"0",
          "dateUploadedYear":"2011",
          "dateTakenYear":"2011",
          "creativeCommons":"BY-NC",
          "dateUploadedDay":"18",
          "dateUploaded":"1311045188",
          "exifCameraModel":"",
          "path200x200":"\/custom\/201107\/1311045184-opme7Z0WBh_200x200.jpg",
          "id":"hl"
        },
        {
          "tags":[
             ""
          ],
          "pathBase":"\/base\/201107\/1311027064-opme0WBhqP.jpg",
          "appId":"opme",
          "host":"testjmathai1.s3.amazonaws.com",
          "dateUploadedMonth":"07",
          "status":"1",
          "hash":"fba49a238426ac3485af6d69967ccd2d08c1fe5c",
          "width":"569",
          "dateTakenMonth":"07",
          "dateTakenDay":"18",
          "permission":"0",
          "pathOriginal":"\/original\/201107\/1311027064-opme0WBhqP.jpg",
          "exifCameraMake":"",
          "size":"0",
          "dateTaken":"1311027064",
          "height":"476",
          "views":"0",
          "dateUploadedYear":"2011",
          "dateTakenYear":"2011",
          "creativeCommons":"BY-NC",
          "dateUploadedDay":"18",
          "dateUploaded":"1311027066",
          "exifCameraModel":"",
          "id":"ob"
        }
      ]
    }


[Photo]: http://theopenphotoproject.org/documentation/schemas/Photo
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-cli]: #example-cli
[example-php]: #example-php
[response]: #response
[sample]: #sample
[photogeneration]: http://theopenphotoproject.org/documentation/faq/PhotoGeneration
[ReturnSizes]: http://theopenphotoproject.org/documentation/faq/ReturnSizes
[openphoto-php]: https://github.com/photo/openphoto-php

Open Photo API / Get Photo
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

1. [Purpose][purpose]
1. [Endpoint][endpoint]
1. [Parameters][parameters]
1. [Examples][examples]
  * [Curl][example-curl]
  * [PHP][example-php]
1. [Response][response]
  * [Sample][sample]

----------------------------------------

<a name="purpose"></a>
### Purpose of the Get Photo API

Use this API to get a single of photo for a user.

_NOTE:_ Always pass in the `returnSizes` parameter for sizes you plan on using. It's the only way to guarantee that a URL for that size will be present in the response. See [Photo Generation][photogeneration] for details.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: optional_

    GET /photo/:id.json

<a name="parameters"></a>
### Parameters

1.  returnSizes (optional), The photo sizes you'd like in the response. Specify every size you plan on using.

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-curl"></a>
#### Command line curl

    curl http://jmathai.openphoto.me/photo/abc.json

<a name="example-php"></a>
#### PHP

    $ch = curl_init('http://jmathai.openphoto.me/photo/abc.json');
    curl_exec($ch);

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope][Envelope].

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, A [Photo][Photo] object

<a name="sample"></a>
#### Sample

    {
      "message":"",
      "code":200,
      "result":{
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
      }
    }

[Envelope]: api/Envelope.markdown
[Photo]: https://github.com/openphoto/frontend/blob/master/documentation/schemas/Photo.markdown
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-curl]: #example-curl
[example-php]: #example-php
[response]: #response
[sample]: #sample
[photogeneration]: ../PhotoGeneration.markdown

Open Photo API / Get Photos
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
### Purpose of the Get Photos API


----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: optional_

    GET /photos.json

<a name="parameters"></a>
### Parameters

1.  page (optional), Page number when browsing through photos. Starts at 1.
1.  tags (optional), _i.e. dog,cat_ - A comma delimited string of alpha numeric strings.

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-curl"></a>
#### Command line curl

    curl -F 'photo=@/path/to/photo.jpg' -F 'tags=dog,cat' http://jmathai.openphoto.me/photo/upload.json
    curl -F 'photo=base64_encoded_string_representation_of_photo' -F 'title=My first day at work' http://jmathai.openphoto.me/photo/upload.json

<a name="example-php"></a>
#### PHP

    $ch = curl_init('http://jmathai.openphoto.me/photo/upload.json');
    curl_setopt(
      $ch, 
      CURLOPT_POSTFIELDS, 
      array('photo' => '@/path/to/photo.jpg', 'tags' => 'dog,cat', returnOptions' => '300x300')
    );
    curl_exec($ch);

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope][Envelope].

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


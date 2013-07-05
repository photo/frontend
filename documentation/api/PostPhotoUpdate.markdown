Update Photo
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
### Purpose of the Photo update API

This API is used to update an existing photo's metadata for a user.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    POST /photo/:id/update.json

<a name="parameters"></a>
### Parameters

1.  permission (optional), 0 for private and 1 for public.
1.  title (optional), _e.g. My first day at work_ - A string title to describe the photo.
1.  description (optional), _e.g. A much longer description of my first day_ - A string to describe the photo in detail.
1.  tags (optional), _e.g. dog,cat_ - A comma delimited string of alpha numeric strings.
1.  tagsAdd (optional), _e.g. dog,cat_ - A comma delimited string of alpha numeric strings to be added.
1.  tagsRemove (optional), _e.g. dog,cat_ - A comma delimited string of alpha numeric strings to be removed.
1.  dateUploaded (optional), _e.g. 1311059035_ - A unix timestamp of the date the photo was uploaded
1.  dateTaken (optional), _e.g. 1311059035_ - A unix timestamp of the date the photo was taken which overrides EXIF data if present
1.  license (optional), _e.g. CC BY-SA or My Custom License_ - A string representing a custom or Creative Commons license.
1.  latitude (optional), _e.g. 34.76_ - A decimal representation of latitude.
1.  longitude (optional), _e.g. -87.45_ - A decimal representation of longitude.

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    source secrets.sh
    ./openphoto -p -X POST -h current.trovebox.com -e /photo/a/update.json -F 'title=My Photo Title' -F 'tags=sunnyvale,downtown'

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->post("/photo/a/update.json", array('title' => 'My Photo Title', 'tags' => 'sunnyvale,downtown'));

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _202_ on success
* _result_, A [Photo][Photo] object or FALSE on error

<a name="sample"></a>
#### Sample

    {
      "message":"Photo 8i uploaded successfully",
      "code":202,
      "result":{
         "id":"8i",
         "tags":[
            "dog",
            "cat"
         ],
         "pathBase":"\/base\/201107\/1311053366-huge.jpg",
         "appId":"opme",
         "host":"testjmathai1.s3.amazonaws.com",
         "dateUploadedMonth":"07",
         "status":"1",
         "hash":"6d7a9b0af31073a76ff2e79ee44b5c4951671fa2",
         "width":"4288",
         "dateTakenMonth":"07",
         "dateTakenDay":"03",
         "permission":"0",
         "pathOriginal":"\/original\/201107\/1311053366-huge.jpg",
         "exifCameraMake":"NIKON CORPORATION",
         "size":"5595",
         "dateTaken":"1309707719",
         "height":"2848",
         "views":"0",
         "dateUploadedYear":"2011",
         "dateTakenYear":"2011",
         "creativeCommons":"BY-NC",
         "dateUploadedDay":"18",
         "dateUploaded":"1311053403",
         "exifCameraModel":"NIKON D90",
         "longitude":"-89.24",
         "latitude":"37.65",
         "path300x300":"\/custom\/201107\/1311053366-huge_300x300.jpg",
      }
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
[openphoto-php]: https://github.com/photo/openphoto-php

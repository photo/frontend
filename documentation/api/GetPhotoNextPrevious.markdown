Get Next/Previous Photo
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
### Purpose of the Get Photo API

Use this API to get the next and previous photo given a photo in the middle.

_NOTE:_ Always pass in the `returnSizes` parameter for sizes you plan on using. It's the only way to guarantee that a URL for that size will be present in the response. See [Photo Generation](http://theopenphotoproject.org/documentation/faq/PhotoGeneration) for details.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: optional_

    GET /photo/:id/nextprevious.json

<a name="parameters"></a>
### Parameters

1.  returnSizes (optional), (i.e. 20x20 or 30x30xCR,40x40) The photo sizes you'd like in the response. Specify every size you plan on using. [Docs for this parameter](http://theopenphotoproject.org/documentation/faq/ReturnSizes)
1.  generate (optional), (i.e. true or false) Tells the API to generate the sizes from `returnSizes` instead of returning a _create_ URL. [Docs for this parameter](http://theopenphotoproject.org/documentation/faq/ReturnSizes)

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-cli"></a>
#### Command Line (using [openphoto-php][openphoto-php])

    ./openphoto -p -h current.openphoto.me -e /photo/b/nextprevious.json

<a name="example-php"></a>
#### PHP (using [openphoto-php][openphoto-php])

    $client = new OpenPhotoOAuth($host, $consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);
    $response = $client->get("/photo/b/nextprevious.json");

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope](http://theopenphotoproject.org/documentation/api/Envelope).

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, An array of arrays of [Photo][Photo] objects

<a name="sample"></a>
#### Sample

    {
      "message" : "Next\/previous for photo bq",
      "code" : 200,
      "result" : {
        "previous" : [
          {
            "tags" : [

            ],
            "id" : "bo",
            "appId" : "openphoto-frontend",
            "pathBase" : "\/base\/201109\/1317155744-DSC_9243.JPG",
            "dateUploadedMonth" : "09",
            "dateTakenMonth" : "08",
            "exifCameraMake" : "NIKON CORPORATION",
            "dateTaken" : "1313454314",
            "title" : "",
            "height" : "2000",
            "description" : "",
            "dateTakenYear" : "2011",
            "longitude" : "",
            "dateUploadedDay" : "27",
            "host" : "opmecurrent.s3.amazonaws.com",
            "hash" : "7b923cbbe4f7aa81be144b1420a99711ad57106b",
            "status" : "1",
            "width" : "3008",
            "dateTakenDay" : "15",
            "permission" : "1",
            "pathOriginal" : "\/original\/201109\/1317155744-DSC_9243.JPG",
            "size" : "2502",
            "dateUploadedYear" : "2011",
            "views" : "0",
            "latitude" : "",
            "dateUploaded" : "1317155745",
            "exifCameraModel" : "NIKON D70s",
            "Name" : "bo",
            "exifFocalLength" : "35",
            "exifExposureTime" : "10\/600",
            "exifISOSpeed" : "",
            "license" : "",
            "exifFNumber" : "3.8"
          },
          // optionally another
        ],
        "next" : [
          {
            "tags" : [

            ],
            "id" : "63",
            "appId" : "current.openphoto.me",
            "pathBase" : "\/base\/201108\/1313010849-opmeTbrBki.jpg",
            "dateUploadedMonth" : "08",
            "dateTakenMonth" : "08",
            "exifCameraMake" : "",
            "dateTaken" : "1313010850",
            "title" : "Gulf Shores, AL",
            "height" : "1936",
            "description" : "",
            "creativeCommons" : "BY-NC",
            "dateTakenYear" : "2011",
            "dateUploadedDay" : "10",
            "longitude" : "-87.7008193",
            "host" : "opmecurrent.s3.amazonaws.com",
            "hash" : "20d64642f09befc4004c22269e698e6e43475963",
            "status" : "1",
            "width" : "2592",
            "dateTakenDay" : "10",
            "permission" : "1",
            "pathOriginal" : "\/original\/201108\/1313010849-opmeTbrBki.jpg",
            "size" : "1513",
            "dateUploadedYear" : "2011",
            "views" : "0",
            "latitude" : "30.2460361",
            "dateUploaded" : "1313010853",
            "exifCameraModel" : "",
            "Name" : "63"
          },
          // optionally another
        ]
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
[photogeneration]: http://theopenphotoproject.org/documentation/faq/PhotoGeneration
[ReturnSizes]: http://theopenphotoproject.org/documentation/faq/ReturnSizes
[openphoto-php]: https://github.com/photo/openphoto-php

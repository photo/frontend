How photos are generated
=======================

### Overview of how and when versions of photos are generated

By default, when a photo is uploaded there are two versions of that photo which are stored. The original version (`pathOriginal`) and a slightly lower resolution version (`pathBase`).

The API is capable of returning URLs for any size version of every photo. This is acheived by passing in a parameter named `returnSizes` to the [GET Photos](http://theopenphotoproject.org/documentation/api/GetPhotos) and [GET Photo](http://theopenphotoproject.org/documentation/api/GetPhoto) APIs. This ensures that the response for the photo(s) will include a URL for the size(s) you specify. Here is an example.

    curl "http://current.trovebox.com/photo/63.json?returnSizes=123x123"

This call returns the following response.

    {
      "code" : 200,
      "message" : "Photo 63",
      "result" : {
        "appId" : "current.trovebox.com",
        "creativeCommons" : "BY-NC",
        "dateTaken" : "1313010850",
        "dateTakenDay" : "10",
        "dateTakenMonth" : "08",
        "dateTakenYear" : "2011",
        "dateUploaded" : "1313010853",
        "dateUploadedDay" : "10",
        "dateUploadedMonth" : "08",
        "dateUploadedYear" : "2011",
        "description" : "",
        "exifCameraMake" : "",
        "exifCameraModel" : "",
        "hash" : "20d64642f09befc4004c22269e698e6e43475963",
        "height" : "1936",
        "host" : "opmecurrent.s3.amazonaws.com",
        "id" : "63",
        "latitude" : "",
        "longitude" : "",
        "path123x123" : "http://current.trovebox.com/photo/63/create/1a7f0/123x123.jpg",
        "path200x200" : "http://opmecurrent.s3.amazonaws.com/custom/201108/1313010849-opmeTbrBki_200x200.jpg",
        "pathBase" : "/base/201108/1313010849-opmeTbrBki.jpg",
        "pathOriginal" : "/original/201108/1313010849-opmeTbrBki.jpg",
        "permission" : "1",
        "size" : "1513",
        "status" : "1",
        "tags" : [  ],
        "title" : "Gulf Shores, AL",
        "views" : "0",
        "width" : "2592"
      }
    }

The most important keys in the response are `path123x123` and `path200x200`. Either of these URLs will correctly render the photo in the respective size. Notice, however, that the `path123x123` hostname is different from `path200x200`. This is important because this implies that a _123x123_ version of the photo doesn't exist and the API host needs to generate it. The following url will generate the correct version of the photo, store it to the proper file system, saves it to the database and returns it with a content-type of _image/jpeg_.

    http://current.trovebox.com/photo/63/create/1a7f0/123x123.jpg

It's important to realize that the photo isn't generated and stored until this URL is called. This typically happens when the browser tries to display this photo. Once that has happened then the _123x123_ version exists both in the database and file system and calling the same API again returns a different URL for `path123x123`.

    {
      "code" : 200,
      "message" : "Photo 63",
      "result" : {
        "appId" : "current.trovebox.com",
        "creativeCommons" : "BY-NC",
        "dateTaken" : "1313010850",
        "dateTakenDay" : "10",
        "dateTakenMonth" : "08",
        "dateTakenYear" : "2011",
        "dateUploaded" : "1313010853",
        "dateUploadedDay" : "10",
        "dateUploadedMonth" : "08",
        "dateUploadedYear" : "2011",
        "description" : "",
        "exifCameraMake" : "",
        "exifCameraModel" : "",
        "hash" : "20d64642f09befc4004c22269e698e6e43475963",
        "height" : "1936",
        "host" : "opmecurrent.s3.amazonaws.com",
        "id" : "63",
        "latitude" : "",
        "longitude" : "",
        "path123x123" : "http://opmecurrent.s3.amazonaws.com/custom/201108/1313010849-opmeTbrBki_123x123.jpg",
        "path200x200" : "http://opmecurrent.s3.amazonaws.com/custom/201108/1313010849-opmeTbrBki_200x200.jpg",
        "pathBase" : "/base/201108/1313010849-opmeTbrBki.jpg",
        "pathOriginal" : "/original/201108/1313010849-opmeTbrBki.jpg",
        "permission" : "1",
        "size" : "1513",
        "status" : "1",
        "tags" : [  ],
        "title" : "Gulf Shores, AL",
        "views" : "0",
        "width" : "2592"
      }
    }

The URL for `path123x123` now points to a static resource.

You can specify multiple sizes for the `returnSizes` delimited by commas.

    curl "http://current.trovebox.com/photo/63.json?returnSizes=123x123,300x300xBW"

### Understanding options for returnSizes

The `returnSizes` parameter takes values in the form of _WxH[[xA]xB]_ which means it starts with a numeric _width_ and _height_. The most simple form is limited to specifying just a _width_ and _height_. This looks like `200x200` or `125x300`.

Additional options include `CR` and `BW`. `CR` tells the API to crop the photo to be exactly _width_ by _height_. It does a center crop and minimizes the portion of the photo that is cropped. `BW` applies a greyscale filter to the photo. `100x100xCRxBW` means the photo will have a key of `path100x100xCRxBW` and will be exactly _100_ by _100_, cropped and greyscale.

For more information on the `returnSizes` parameter see our [documentation on returnSizes](http://theopenphotoproject.org/documentation/faq/ReturnSizes).

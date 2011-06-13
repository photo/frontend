Documentation
=======================
#### OpenPhoto, a photo service for the masses

Photos are stored in their own database (or table). The structure the OpenPhoto application expects from the adapter is below.

### Schema

    [
      {
        id: (string),
        title: (string),
        description: (string),
        key: (string),
        hash: (string),
        tags: (set),
        size: (int), // in kb
        width: (int),
        height: (int),
        exifOrientation: (int), // degrees
        exifCameraMake: (string),
        exifCameraModel: (string),
        exifExpsureTime: (string),
        exifFNumber: (string),
        exifMaxApertureValue: (string),
        exifMeteringMode: (string),
        exifFlash: (string),
        exifFocalLength: (string),
        gpsAltitude: (int),
        gpsLatitude: (float),
        gpsLongitude: (float),
        views: (int),
        status: (int), // binary
        permission: (int), // bitwise
        creativeCommons: (enum),
        dateTaken: (int), // unix timestamp
        dateUploaded: (int), // unix timestamp
        urlOriginal: (string),
        urlBase: (string),
        ...
        urlWxH: (string), // pseudo key
          // url400x300: (string)
          // url250x250: (string)
          // url800x600: (string)
      },
      ....
    ]

### Scema description

  * id, base 36 version of a base 10 value
  * title, A title for the photo up to 100 chars
  * description, A description for the photo up to 255 chars
  * key, A random sha1 hash
  * hash, The sha1 hash of the original photo
  * tags, A set of tags which is searchable inclusive or exclusively
  * size, Size of the photo rounded to the nearest Kilobyte
  * width, Width of the photo in pixels
  * height, Height of the photo in pixels
  * exifOrientation, Rotation of the camera in degrees
  * exifCameraMake, Camera make, i.e. Canon
  * exifCcameraModel, Camera model, i.e. EOS Rebel
  * exifExpsureTime
  * exifFNumber, F Number i.e f/4.0
  * exifMaxApertureValue
  * exifMeteringMode
  * exifFlash, Indication if the flash fired
  * exifFocalLength
  * gpsAltitude
  * gpsLatitude
  * gpsLongitude
  * views, Number of times the photo was viewed (excludes views by the owner)
  * status, Numeric representation of the status (0=deleted, 1=active)
  * permission, Bitwise representation of photo permissions
  * creativeCommons, Abbreviation of the CC licenses such as BY, BY-SA, BY-ND, etc. (http://creativecommons.org/licenses/)
  * dateTaken, Unix timestamp of when the photo was taken
  * dateUploaded, Unix timestamp of when the photo was uploaded
  * urlOriginal, The URL of the original photo
  * urlBase, The URL of the base version of the photo, used for photo generation
  * urlWxH, A pseudo key which represents any resized version of a photo and it's URL

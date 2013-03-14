Schema for a Photo object
=======================

----------------------------------------

### What's a Photo object for?

The Photo object represents a single photo in a user's photo collection.
This includes EXIF information from the photo, tags and URLs to all versions of the photo.

----------------------------------------

### Schema for a Photo object

    {
      id: (string),
      appId: (string),
      url: (string),
      host: (string),
      title: (string),
      description: (string),
      key: (string),
      hash: (string),
      tags: (set),
      size: (int), // in kb
      width: (int),
      height: (int),
      rotation: (int),
      exifOrientation: (int), // degrees
      exifCameraMake: (string),
      exifCameraModel: (string),
      exifExpsureTime: (string),
      exifFNumber: (string),
      exifMaxApertureValue: (string),
      exifMeteringMode: (string),
      exifFlash: (string),
      exifFocalLength: (string),
      altitude: (int),
      latitude: (float),
      longitude: (float),
      views: (int),
      status: (int), // binary
      permission: (int), // binary
      groups: (set),
      license: (string),
      dateTaken: (int), // unix timestamp
      dateTakenDay: (int)
      dateTakenMonth: (int)
      dateTakenYear: (int)
      dateUploaded: (int), // unix timestamp
      dateUploadedDay: (int)
      dateUploadedMonth: (int)
      dateUploadedYear: (int)
      pathOriginal: (string),
      pathDownload: (string),
      pathBase: (string),
      ...
      pathWxH: (string), // pseudo key
        // path400x300: (string)
        // path250x250: (string)
        // path800x600: (string)
    }

----------------------------------------

### Scema description

  * id, [Base 32](http://en.wikipedia.org/wiki/Base32#base32hex) value of a base 10 auto-incremented value
  * appId, A string identifing the application creating this entry
  * url, Url to view this photo on the user's OpenPhoto site
  * host, Host on which this photo resides
  * title, A title for the photo up to 100 chars
  * description, A description for the photo up to 255 chars
  * key, A random sha1 hash
  * hash, The sha1 hash of the original photo
  * tags, A set of tags which is searchable inclusive or exclusively
  * size, Size of the photo rounded to the nearest Kilobyte
  * width, Width of the photo in pixels
  * height, Height of the photo in pixels
  * rotation, Degress the user has rotated the photo (0, 90, 180, 270)
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
  * permission, binary representation of photo permission (0=not public, 1=public)
  * groups, A set of group IDs which whitelist email addresses for permissions to this photo
  * license, Abbreviation of the CC licenses such as BY, BY-SA, BY-ND, _blank_ (All rights reserved) or free form (http://creativecommons.org/licenses/)
  * dateTaken, Unix timestamp of when the photo was taken
  * dateTakenDay, Day the photo was taken (for searching)
  * dateTakenMonth, Month the photo was taken (for searching)
  * dateTakenYear, Year the photo was taken (for searching)
  * dateUploaded, Unix timestamp of when the photo was uploaded
  * dateUploadedDay, Day the photo was uploaded (for searching)
  * dateUploadedMonth, Month the photo was uploaded (for searching)
  * dateUploadedYear, Year the photo was uploaded (for searching)
  * pathOriginal, A path to the original photo (do not rely on this to access original photos, use photoDownload)
  * pathDownload, The URL of the original photo proxied through the storage service if needed
  * pathBase, The URL of the base version of the photo, used for photo generation
  * pathWxH, A pseudo key which represents any resized version of a photo and it's URL

[User]: http://theopenphotoproject.org/documentation/schemas/User
[Photo]: http://theopenphotoproject.org/documentation/schemas/Photo
[Action]: http://theopenphotoproject.org/documentation/schemas/Action

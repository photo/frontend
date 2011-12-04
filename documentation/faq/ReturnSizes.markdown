Getting specific sizes of a photo
=======================

### Understanding the `returnSizes` parameter and how to use it
For the [GET /photo/view.json](http://theopenphotoproject.org/documentation/api/GetPhoto) and [GET /photos/list.json](http://theopenphotoproject.org/documentation/api/GetPhotos) you can pass in an optional `returnSizes` parameter.
If you need a path to a photo then you'll want to make sure you pass this parameter in else your response won't contain any URLs.

We also have a more detailed look at how [photos are generated](http://theopenphotoproject.org/documentation/faq/PhotoGeneration).

### Specifying the width and height of photos you want
The first decision you need to make is the size or sizes of photos you want in the response.
You'll specify this as the initial part of the `returnSizes` parameter.

If you want a single size for the photo of width `W` and height `H` then you will want to use the following.

    returnSizes=WxH

If you want multiple sizes for the photo of widths `W1` and `W2` and heights `H1` and `H2` then use the following.

    returnSizes=W1xH1,W2xH2

#### Maintaining aspect ratio and cropping
By default the aspect ratio of every photo is maintained.
That means if you request a `200x200` version of a photo that's originally `800x600` then the resulting image will be _200px_ wide and _150px_ tall.


If you want the same photo to be exactly _200px_ by _200px_ then you can add an optional value to `returnSizes`.

    returnSizes=200x200xCR

Adding `xCR` means that the photo will be exactly _200px_ by _200px_ and cropped from the center retaining as much of the photo as possible.

### Accessing the paths in the response
The name of the URL attribute in the response is, by convention, `pathWxH` where `WxH` is the value passed in to returnSizes.

Open Photo API / Photo Upload
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

### The Photo Upload API

    POST /photo/upload HTTP/1.1
    Host: jmathai.openphoto.me
    Content-Type: multipart/form-data; boundary=----------------SOMERANDOMSEPARATOR    
    -----------------------------SOMERANDOMSEPARATOR
    Content-Disposition: form-data; name="tags"
    disneyland,epcotcenter
    -----------------------------SOMERANDOMSEPARATOR
    Content-Disposition: form-data; name="longitude"
    123.456
    -----------------------------SOMERANDOMSEPARATOR
    Content-Disposition: form-data; name="latitude"
    135.246
    -----------------------------SOMERANDOMSEPARATOR
    Content-Disposition: form-data; name="file";
    filename="/path/to/your/photo.jpg"
    Content-Type: image/gif

    {your_binary_content_here}
    -----------------------------SOMERANDOMSEPARATOR--

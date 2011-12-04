OpenPhoto / Glossary
=======================


The OpenPhoto stack consists of various components. Below is a glossary of all the terms you need to know so you can understand the overall system.

1.  **Amazon S3**
    [Amazon S3][s3] stands for Simple Storage Service.
1.  **Amazon SimpleDb**
    [Amazon SimpleDb][simpledb] is a non-relational database system.
1.  **Data Source**
    The Data Source stores all textual data for a given user. Each user's Data Source may be different. The default Data Source is data.openphoto.me as a CNAME to [Amazon SimpleDb][simpledb].
1.  **File System**
    The File System stores all high and low resolution photos for a given user. Each user's File System may be different. The default File System is file.openphoto.me as a CNAME to [Amazon S3][s3].
1.  **Adapter**
    Adapters are middleware that allows the base system to communicate with various Data Sources and File Systems. Adapters exist for [Amazon S3][s3] and [Amazon SimpleDb][simpledb].
1.  **Open Photo API**
    [The Open Photo API][openphotoapi] which this software is built on and also allows others to build applications on top of.


[openphotoapi]: documentation/api
[aws]: http://aws.amazon.com/
[s3]: http://aws.amazon.com/s3/
[simpledb]: http://aws.amazon.com/simpledb/

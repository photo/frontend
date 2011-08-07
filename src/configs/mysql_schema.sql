
CREATE TABLE IF NOT EXISTS  `photos` (
  `id` varchar(255) NOT NULL UNIQUE,
  `host` varchar(255),
  `title` varchar(255),
  `description` text,
  `key` varchar(255),
  `hash` varchar(255),
  
  `size` integer,
  `width` integer,
  `height` integer,
  `exifOrientation` integer,
  `exifCameraMake` varchar(32),
  `exifCameraModel` varchar(32),
  `exifExposureTime` varchar(64),
  `exifFNumber` varchar(6),
  `exifMaxApertureValue` varchar(6),
  `exifMeteringMode` varchar(64),
  `exifFlash` varchar(6),
  `exifFocalLength` varchar(32),
  `gpsAltitude` integer,
  `gpsLatitude` float,
  `gpsLongitude` float,
  `views` integer,
  `satus` integer,
  `permission` integer,
  `creativeCommons` integer,
  `dateTaken` integer,
  `dateTakenDay` integer,
  `dateTakenMonth` integer,
  `dateTakenYear` integer,
  `dateUploaded` integer,
  `dateUploadedDay` integer,
  `dateUploadedMonth` integer,
  `dateUploadedYear` integer,
  `pathOriginal` varchar(1000),
  `pathBase` varchar(1000),

  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `tags` (
  `id` varchar(255) NOT NULL UNIQUE,
  `count` int,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `users` (
  `lastPhotoId` varchar(255),
  `lastActionId` varchar(255)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `actions` (
  `id` varchar(255) NOT NULL UNIQUE,
  `appId` varchar(255),
  `targetId` varchar(255),
  `targetType` varchar(255),
  `email` varchar(255),
  `name` varchar(255),
  `avatar` varchar(255),
  `website` varchar(255),
  `targetUrl` varchar(1000),
  `permalink` varchar(1000),
  `type` varchar(255),
  `value` varchar(255),
  `datePosted` varchar(255),
  `status` integer,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

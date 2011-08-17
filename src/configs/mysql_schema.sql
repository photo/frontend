
CREATE TABLE IF NOT EXISTS `photo` (
  `id` varchar(255) NOT NULL,
  `appId` varchar(255) NOT NULL,
  `host` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `key` varchar(255) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `exifOrientation` int(11) DEFAULT NULL,
  `exifCameraMake` varchar(32) DEFAULT NULL,
  `exifCameraModel` varchar(32) DEFAULT NULL,
  `exifExposureTime` varchar(64) DEFAULT NULL,
  `exifFNumber` varchar(6) DEFAULT NULL,
  `exifMaxApertureValue` varchar(6) DEFAULT NULL,
  `exifMeteringMode` varchar(64) DEFAULT NULL,
  `exifFlash` varchar(6) DEFAULT NULL,
  `exifFocalLength` varchar(32) DEFAULT NULL,
  `gpsAltitude` int(11) DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `views` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `permission` int(11) DEFAULT NULL,
  `creativeCommons` int(11) DEFAULT NULL,
  `dateTaken` int(11) DEFAULT NULL,
  `dateTakenDay` int(11) DEFAULT NULL,
  `dateTakenMonth` int(11) DEFAULT NULL,
  `dateTakenYear` int(11) DEFAULT NULL,
  `dateUploaded` int(11) DEFAULT NULL,
  `dateUploadedDay` int(11) DEFAULT NULL,
  `dateUploadedMonth` int(11) DEFAULT NULL,
  `dateUploadedYear` int(11) DEFAULT NULL,
  `pathOriginal` varchar(1000) DEFAULT NULL,
  `pathBase` varchar(1000) DEFAULT NULL,
  `tags` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `photoVersion` (
    `id` varchar(255),
    `key` varchar(255),
    `path` varchar(1000),
    PRIMARY KEY(`id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tag` (
  `id` varchar(255) NOT NULL UNIQUE,
  `count` int,

  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(255) NOT NULL UNIQUE,
  `lastPhotoId` varchar(255),
  `lastActionId` varchar(255),

  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `action` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

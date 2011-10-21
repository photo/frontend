
CREATE TABLE IF NOT EXISTS `op_action` (
  `id` varchar(255) NOT NULL,
  `appId` varchar(255) DEFAULT NULL,
  `targetId` varchar(255) DEFAULT NULL,
  `targetType` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `targetUrl` varchar(1000) DEFAULT NULL,
  `permalink` varchar(1000) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `datePosted` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_admin`
--

CREATE TABLE IF NOT EXISTS `op_admin` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_credential`
--

CREATE TABLE IF NOT EXISTS `op_credential` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` text,
  `clientSecret` varchar(255) DEFAULT NULL,
  `userToken` varchar(255) DEFAULT NULL,
  `userSecret` varchar(255) DEFAULT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  `verifier` varchar(255) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_group`
--

CREATE TABLE IF NOT EXISTS `op_group` (
  `id` varchar(255) NOT NULL,
  `appId` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `members` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_photo`
--

CREATE TABLE IF NOT EXISTS `op_photo` (
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
  `exif` text,
  `views` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `permission` int(11) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
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
  UNIQUE KEY `id` (`id`),
  FULLTEXT KEY `tags` (`tags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_photoVersion`
--

CREATE TABLE IF NOT EXISTS `op_photoVersion` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `key` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_tag`
--

CREATE TABLE IF NOT EXISTS `op_tag` (
  `id` varchar(255) NOT NULL,
  `count` int(11) DEFAULT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_user`
--

CREATE TABLE IF NOT EXISTS `op_user` (
  `id` varchar(255) NOT NULL,
  `lastPhotoId` varchar(255) DEFAULT NULL,
  `lastActionId` varchar(255) DEFAULT NULL,
  `lastGroupId` varchar(255) NOT NULL,
  `lastWebhookId` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `op_user`
--

INSERT INTO `op_user` (`id`, `lastPhotoId`, `lastActionId`, `lastGroupId`, `lastWebhookId`) VALUES
('1', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `op_webhook`
--

CREATE TABLE IF NOT EXISTS `op_webhook` (
  `id` varchar(255) NOT NULL,
  `appId` varchar(255) DEFAULT NULL,
  `callback` varchar(1000) DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

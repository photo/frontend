--
-- Table structure for table `op_action`
--

CREATE TABLE IF NOT EXISTS `op_action` (
  `id` varchar(6) NOT NULL,
  `owner` varchar(255) NOT NULL,
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
  UNIQUE KEY `id` (`id`,`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_credential`
--

CREATE TABLE IF NOT EXISTS `op_credential` (
  `id` varchar(30) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` text,
  `clientSecret` varchar(255) DEFAULT NULL,
  `userToken` varchar(255) DEFAULT NULL,
  `userSecret` varchar(255) DEFAULT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  `verifier` varchar(255) DEFAULT NULL,
  `type` varchar(100) NOT NULL,
  `status` int(11) DEFAULT '0',
  UNIQUE KEY `id` (`id`,`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_elementGroup`
--

CREATE TABLE IF NOT EXISTS `op_elementGroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner` varchar(255) NOT NULL,
  `type` enum('photo') NOT NULL,
  `element` varchar(6) NOT NULL,
  `group` varchar(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner` (`owner`,`type`,`element`,`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_elementTag`
--

CREATE TABLE IF NOT EXISTS `op_elementTag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner` varchar(255) NOT NULL,
  `type` enum('photo') NOT NULL,
  `element` varchar(6) NOT NULL DEFAULT 'photo',
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`owner`,`type`,`element`,`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tag mapping table for photos (and videos in the future)';

-- --------------------------------------------------------

--
-- Table structure for table `op_group`
--

CREATE TABLE IF NOT EXISTS `op_group` (
  `id` varchar(6) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `appId` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `permission` tinyint(4) NOT NULL COMMENT 'Bitmask of permissions',
  UNIQUE KEY `id` (`id`,`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_groupMember`
--

CREATE TABLE IF NOT EXISTS `op_groupMember` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` varchar(255) NOT NULL,
  `group` varchar(6) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner` (`owner`,`group`,`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_photo`
--

CREATE TABLE IF NOT EXISTS `op_photo` (
  `id` varchar(6) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `appId` varchar(255) NOT NULL,
  `host` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `key` varchar(255) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `extra` text,
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
  `tags` varchar(1000) DEFAULT NULL,
  `groups` varchar(1000) DEFAULT NULL,
  UNIQUE KEY `id` (`id`,`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_photoVersion`
--

CREATE TABLE IF NOT EXISTS `op_photoVersion` (
  `id` varchar(6) NOT NULL DEFAULT '',
  `owner` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(1000) DEFAULT NULL,
  UNIQUE KEY `id` (`id`,`owner`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_tag`
--

CREATE TABLE IF NOT EXISTS `op_tag` (
  `id` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `countPublic` int(11) NOT NULL DEFAULT '0',
  `countPrivate` int(11) NOT NULL DEFAULT '0',
  `extra` text NOT NULL,
  UNIQUE KEY `id` (`id`,`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_user`
--

CREATE TABLE IF NOT EXISTS `op_user` (
  `id` varchar(255) NOT NULL COMMENT 'User''s email address',
  `extra` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `op_webhook`
--

CREATE TABLE IF NOT EXISTS `op_webhook` (
  `id` varchar(6) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `appId` varchar(255) DEFAULT NULL,
  `callback` varchar(1000) DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id` (`id`,`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__miwovideos_categories` (
  `id`          INT(11)    NOT NULL AUTO_INCREMENT,
  `parent`      INT(11) DEFAULT NULL,
  `title`       VARCHAR(255) DEFAULT NULL,
  `alias`       VARCHAR(255) DEFAULT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `thumb`       VARCHAR(255) DEFAULT NULL,
  `introtext`   MEDIUMTEXT,
  `fulltext`    MEDIUMTEXT,
  `ordering`    INT(11) DEFAULT NULL,
  `access`      TINYINT(3) NOT NULL DEFAULT '0',
  `language`    VARCHAR(7) NULL DEFAULT '*',
  `created`     DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`    DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published`   TINYINT(3) NOT NULL DEFAULT '1',
  `type`        VARCHAR(255) DEFAULT NULL,
  `meta_desc`   VARCHAR(1024) DEFAULT NULL,
  `meta_key`    VARCHAR(1024) DEFAULT NULL,
  `meta_author` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET =utf8;

INSERT IGNORE INTO `#__miwovideos_categories` (`id`, `parent`, `title`, `alias`, `description`, `thumb`, `introtext`, `fulltext`, `ordering`, `access`, `language`, `created`, `modified`, `type`, `meta_desc`, `meta_key`, `meta_author`, `published`)
VALUES
  (1, '0', 'Uncategorised', 'uncategorised', NULL, '', '', NULL, '1', '1', '*', NOW(), NOW(), NULL, NULL, NULL, NULL,
   '1'),
  (2, '0', 'Technology', 'technology', NULL, 'technology.jpg',
   'This category is created by MiwoVideos as sample data,you can change or delete it.',
   NULL, '1', '1', '*', NOW(), NOW(), NULL, NULL, NULL, NULL, '1'),
  (3, '0', 'Movies', 'movies', NULL, 'entertainment.jpg',
   'This category is created by MiwoVideos as sample data,you can change or delete it.',
   NULL, '2', '1', '*', NOW(), NOW(), NULL, NULL, NULL, NULL, '1'),
  (4, '0', 'Music', 'music', NULL, 'entertainment.jpg',
   'This category is created by MiwoVideos as sample data,you can change or delete it.',
   NULL, '2', '1', '*', NOW(), NOW(), NULL, NULL, NULL, NULL, '1'),
  (5, '0', 'Sports', 'sports', NULL, 'entertainment.jpg',
   'This category is created by MiwoVideos as sample data,you can change or delete it.',
   NULL, '2', '1', '*', NOW(), NOW(), NULL, NULL, NULL, NULL, '1'),
  (6, '0', 'Games', 'games', NULL, 'entertainment.jpg',
   'This category is created by MiwoVideos as sample data,you can change or delete it.',
   NULL, '2', '1', '*', NOW(), NOW(), NULL, NULL, NULL, NULL, '1');

CREATE TABLE IF NOT EXISTS `#__miwovideos_channels` (
  `id`           INT(11)    NOT NULL AUTO_INCREMENT,
  `user_id`      INT(11) DEFAULT NULL,
  `title`        VARCHAR(255) DEFAULT NULL,
  `alias`        VARCHAR(255) DEFAULT NULL,
  `introtext`    MEDIUMTEXT,
  `fulltext`     MEDIUMTEXT,
  `thumb`        VARCHAR(255) DEFAULT NULL,
  `banner`       VARCHAR(255) DEFAULT NULL,
  `fields`       TEXT       NULL DEFAULT NULL,
  `likes`        INT(11)    NOT NULL DEFAULT '0',
  `dislikes`     INT(11)    NOT NULL DEFAULT '0',
  `hits`         INT(11)    NOT NULL DEFAULT '0',
  `params`       TEXT DEFAULT NULL,
  `ordering`     INT(11) DEFAULT '0',
  `access`       TINYINT(3) NOT NULL DEFAULT '0',
  `language`     VARCHAR(7) NULL DEFAULT '*',
  `created`      DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`     DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `featured`     TINYINT(3) NOT NULL DEFAULT '0',
  `published`    TINYINT(3) NOT NULL DEFAULT '1',
  `default`      TINYINT(3) NOT NULL DEFAULT '0',
  `share_others` TINYINT(3) NOT NULL DEFAULT '0',
  `meta_desc`    VARCHAR(1024) DEFAULT NULL,
  `meta_key`     VARCHAR(1024) DEFAULT NULL,
  `meta_author`  VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_fields` (
  `id`             INT(11)             NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(50) DEFAULT NULL,
  `title`          VARCHAR(255) DEFAULT NULL,
  `description`    VARCHAR(255) DEFAULT NULL,
  `field_type`     VARCHAR(50) DEFAULT NULL,
  `values`         TEXT,
  `default_values` TEXT,
  `display_in`     TINYINT(3) UNSIGNED DEFAULT NULL,
  `rows`           TINYINT(3) UNSIGNED DEFAULT NULL,
  `cols`           TINYINT(3) UNSIGNED DEFAULT NULL,
  `size`           INT(11) DEFAULT NULL,
  `css_class`      VARCHAR(50) DEFAULT NULL,
  `field_mapping`  VARCHAR(100) DEFAULT NULL,
  `ordering`       INT(11) DEFAULT NULL,
  `access`         TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `language`       VARCHAR(7)          NULL DEFAULT '*',
  `published`      TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `field_type` (`field_type`)
)
  DEFAULT CHARSET =utf8;

INSERT IGNORE INTO `#__miwovideos_fields` (`id`, `name`, `title`, `description`, `field_type`, `values`, `default_values`, `display_in`, `rows`, `cols`, `size`, `css_class`, `field_mapping`, `ordering`, `access`, `language`, `published`)
VALUES
  (13, 'miwi_license', 'License', '', 'text', '', '', 2, 0, 0, 25, 'inputbox', '', 13, 1, '*', 1);

CREATE TABLE IF NOT EXISTS `#__miwovideos_files` (
  `id`           INT(11)                 NOT NULL AUTO_INCREMENT,
  `video_id`     INT(11)                 NOT NULL DEFAULT '0',
  `process_type` INT(11)                 NOT NULL DEFAULT '0',
  `ext`          VARCHAR(10)             NOT NULL DEFAULT '',
  `file_size`    INT(11)        UNSIGNED NOT NULL DEFAULT '0',
  `source`       VARCHAR(255)            NOT NULL,
  `channel_id`   INT(11)                 NOT NULL DEFAULT '0',
  `user_id`      INT(11)                 NOT NULL DEFAULT '0',
  `created`      DATETIME                NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`     DATETIME                NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published`    TINYINT(3)              NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_likes` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `channel_id` INT(11)      NULL,
  `user_id`    INT(11)      NULL,
  `item_id`    INT(11)      NULL,
  `item_type`  VARCHAR(250) NULL,
  `type`       TINYINT(3)   NULL,
  `created`    DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  INDEX `channel_id` (`channel_id`),
  INDEX `item_id` (`item_id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_playlists` (
  `id`            INT(11)    NOT NULL AUTO_INCREMENT,
  `channel_id`    INT(11) DEFAULT NULL,
  `user_id`       INT(11) DEFAULT NULL,
  `type`          TINYINT(3) NOT NULL DEFAULT '0',
  `title`         VARCHAR(255) DEFAULT NULL,
  `alias`         VARCHAR(255) DEFAULT NULL,
  `introtext`     MEDIUMTEXT,
  `fulltext`      MEDIUMTEXT,
  `thumb`         VARCHAR(255) DEFAULT NULL,
  `fields`        TEXT       NULL DEFAULT NULL,
  `likes`         INT(11)    NOT NULL DEFAULT '0',
  `dislikes`      INT(11)    NOT NULL DEFAULT '0',
  `hits`          INT(11)    NOT NULL DEFAULT '0',
  `subscriptions` INT(11)    NOT NULL DEFAULT '0',
  `params`        TEXT DEFAULT NULL,
  `ordering`      INT(11) DEFAULT '0',
  `access`        TINYINT(3) NOT NULL DEFAULT '0',
  `language`      VARCHAR(7) NULL DEFAULT '*',
  `created`       DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`      DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `featured`      TINYINT(3) NOT NULL DEFAULT '0',
  `published`     TINYINT(3) NOT NULL DEFAULT '1',
  `share_others`  TINYINT(3) NOT NULL DEFAULT '0',
  `meta_desc`     VARCHAR(1024) DEFAULT NULL,
  `meta_key`      VARCHAR(1024) DEFAULT NULL,
  `meta_author`   VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel_id` (`channel_id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_playlist_videos` (
  `id`          INT(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` INT(11) NULL,
  `video_id`    INT(11) NULL,
  `ordering`    INT(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `playlist_id` (`playlist_id`),
  INDEX `video_id` (`video_id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_report_reasons` (
  `id`          INT(11)             NOT NULL AUTO_INCREMENT,
  `parent`      INT(11) DEFAULT NULL,
  `title`       VARCHAR(255) DEFAULT NULL,
  `alias`       VARCHAR(255) DEFAULT NULL,
  `description` VARCHAR(255)        NOT NULL,
  `access`      TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `language`    VARCHAR(7)          NULL DEFAULT '*',
  `association` TINYINT(3) UNSIGNED DEFAULT NULL,
  `published`   TINYINT(3) UNSIGNED NOT NULL DEFAULT '1',
  `created`     DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`    DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET =utf8;

INSERT IGNORE INTO `#__miwovideos_report_reasons` (`id`, `parent`, `title`, `alias`, `description`, `access`, `language`, `association`, `published`, `created`, `modified`)
VALUES
  (1, NULL, 'Child Abuse', '',
   'Child abuse is the physical, sexual or emotional maltreatment or neglect of a child or children.', 1, '*', 1, 1,
   NOW(), NOW()),
  (2, NULL, 'Sexual content', '',
   'Sexual content is material depicting sexual behavior. The sexual behavior involved may be explicit, implicit sexual behavior such as flirting, or include sexual language and euphemisms.',
   1, '*', 2, 1, NOW(), NOW()),
  (3, NULL, 'Infringes my rights', '', '', 1, '*', 3, 1, NOW(), NOW()),
  (4, NULL, 'Spam or misleading', '', '', 1, '*', 4, 1, NOW(), NOW()),
  (5, NULL, 'Harmful dangerous acts', '', '', 1, '*', 5, 1, NOW(), NOW()),
  (6, NULL, 'Hateful or abusive content', '', '', 1, '*', 6, 1, NOW(), NOW()),
  (7, NULL, 'Violent or repulsive content', '', '', 1, '*', 7, 1, NOW(), NOW());

CREATE TABLE IF NOT EXISTS `#__miwovideos_reports` (
  `id`         INT(11)          NOT NULL AUTO_INCREMENT,
  `channel_id` INT(11)          NULL,
  `user_id`    INT(11)          NULL,
  `item_id`    INT(11)          NULL,
  `item_type`  VARCHAR(250)     NULL,
  `reason_id`  INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `note`       VARCHAR(255)     NOT NULL,
  `created`    DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`   DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_processes` (
  `id`               INT(11)    NOT NULL AUTO_INCREMENT,
  `process_type`     INT(11) DEFAULT NULL,
  `video_id`         INT(11) DEFAULT NULL,
  `status`           INT(11) DEFAULT NULL,
  `attempts`         INT(11) DEFAULT NULL,
  `checked_out`      INT(11) DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  `params`           TEXT DEFAULT NULL,
  `created`          DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`         DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published`        TINYINT(3) NOT NULL DEFAULT '1',
  `created_user_id`  INT(11) DEFAULT NULL,
  `modified_user_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_process_log` (
  `id`              INT(11)          NOT NULL AUTO_INCREMENT,
  `process_id`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `video_id`        INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `input`           VARCHAR(5120)    NOT NULL DEFAULT '',
  `output`          LONGTEXT    	   NOT NULL DEFAULT '',
  `created`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_process_type` (
  `id`        INT(11)    NOT NULL AUTO_INCREMENT,
  `title`     VARCHAR(255) DEFAULT NULL,
  `alias`     VARCHAR(255) DEFAULT NULL,
  `filetype`  VARCHAR(255) DEFAULT NULL,
  `size`      VARCHAR(255) DEFAULT NULL,
  `ordering`  INT(11) DEFAULT NULL,
  `published` TINYINT(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)
  DEFAULT CHARSET =utf8;

INSERT IGNORE INTO `#__miwovideos_process_type` (`id`, `title`, `alias`, `filetype`, `size`, `ordering`, `published`)
VALUES
  (1, 'Thumbnail (75 pixels)', 'thumbnail-75-pixels', 'thumb', '75', '1', '1'),
  (2, 'Thumbnail (100 pixels)', 'thumbnail-100-pixels', 'thumb', '100', '2', '1'),
  (3, 'Thumbnail (240 pixels)', 'thumbnail-240-pixels', 'thumb', '240', '3', '1'),
  (4, 'Thumbnail (500 pixels)', 'thumbnail-500-pixels', 'thumb', '500', '4', '1'),
  (5, 'Thumbnail (640 pixels)', 'thumbnail-640-pixels', 'thumb', '640', '5', '1'),
  (6, 'Thumbnail (1024 pixels)', 'thumbnail-1024-pixels', 'thumb', '1024', '6', '1'),
  (7, 'Mp4 (240p)', 'mp4-240p', 'mp4', '240', '7', '1'),
  (8, 'Mp4 (360p)', 'mp4-360p', 'mp4', '360', '8', '1'),
  (9, 'Mp4 (480p)', 'mp4-480p', 'mp4', '480', '9', '1'),
  (10, 'Mp4 (720p)', 'mp4-720p', 'mp4', '720', '10', '1'),
  (11, 'Mp4 (1080p)', 'mp4-1080p', 'mp4', '1080', '11', '1'),
  (12, 'Webm (240p)', 'webm-240p', 'webm', '240', '12', '1'),
  (13, 'Webm (360p)', 'webm-360p', 'webm', '360', '13', '1'),
  (14, 'Webm (480p)', 'webm-480p', 'webm', '480', '14', '1'),
  (15, 'Webm (720p)', 'webm-720p', 'webm', '720', '15', '1'),
  (16, 'Webm (1080p)', 'webm-1080p', 'webm', '1080', '16', '1'),
  (17, 'Ogg (240p)', 'ogg-240p', 'ogg', '240', '17', '1'),
  (18, 'Ogg (360p)', 'ogg-360p', 'ogg', '360', '18', '1'),
  (19, 'Ogg (480p)', 'ogg-480p', 'ogg', '480', '19', '1'),
  (20, 'Ogg (720p)', 'ogg-720p', 'ogg', '720', '20', '1'),
  (21, 'Ogg (1080p)', 'ogg-1080p', 'ogg', '1080', '21', '1'),
  (22, 'Inject metadata', 'inject-metadata', '', '', '22', '1'),
  (23, 'Move moov atom', 'move-moov-atom', '', '', '23', '1'),
  (24, 'Get duration', 'get-duration', '', '', '24', '1'),
  (25, 'Get title', 'get-title', '', '', '25', '1'),
  (26, 'Flv (240p)', 'flv-240p', 'flv', '240', '26', '1'),
  (27, 'Flv (360p)', 'flv-360p', 'flv', '360', '27', '1'),
  (28, 'Flv (480p)', 'flv-480p', 'flv', '480', '28', '1'),
  (29, 'Flv (720p)', 'flv-720p', 'flv', '720', '29', '1'),
  (30, 'Flv (1080p)', 'flv-1080p', 'flv', '1080', '30', '1'),
  (100, 'HTML5 format', 'html5-format', '', '', '100', '1');

CREATE TABLE IF NOT EXISTS `#__miwovideos_subscriptions` (
  `id`         INT(11)          NOT NULL AUTO_INCREMENT,
  `item_id`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `item_type`  VARCHAR(250)     NOT NULL DEFAULT 'channels',
  `user_id`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `channel_id` INT(11)          NOT NULL DEFAULT '0',
  `created`    DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  INDEX `user_id` (`user_id`),
  INDEX `channel_id` (`channel_id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_videos` (
  `id`          INT(11)             NOT NULL AUTO_INCREMENT,
  `user_id`     INT(11)             NOT NULL DEFAULT '0',
  `channel_id`  INT(11)             NOT NULL DEFAULT '0',
  `product_id`  INT(11)             NOT NULL DEFAULT '0',
  `title`       VARCHAR(255) DEFAULT NULL,
  `alias`       VARCHAR(255) DEFAULT NULL,
  `introtext`   MEDIUMTEXT DEFAULT NULL,
  `fulltext`    MEDIUMTEXT DEFAULT NULL,
  `source`      TEXT        NOT NULL,
  `duration`    VARCHAR(255)        NOT NULL,
  `likes`       INT(11)             NOT NULL DEFAULT '0',
  `dislikes`    INT(11)             NOT NULL DEFAULT '0',
  `hits`        INT(11)             NOT NULL DEFAULT '0',
  `params`      TEXT DEFAULT NULL,
  `access`      TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `language`    VARCHAR(7)          NOT NULL DEFAULT '*',
  `price`       DECIMAL(10, 2) DEFAULT NULL,
  `created`     DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified`    DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
  `featured`    TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `published`   TINYINT(3)          NOT NULL DEFAULT '1',
  `ordering`    INT(11) DEFAULT '0',
  `fields`      TEXT DEFAULT NULL,
  `thumb`       VARCHAR(255) DEFAULT NULL,
  `meta_desc`   VARCHAR(1024) DEFAULT NULL,
  `meta_key`    VARCHAR(1024) DEFAULT NULL,
  `meta_author` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `channel_id` (`channel_id`)
)
  DEFAULT CHARSET =utf8;

CREATE TABLE IF NOT EXISTS `#__miwovideos_video_categories` (
  `id`          INT(11) NOT NULL AUTO_INCREMENT,
  `video_id`    INT(11) NULL,
  `category_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  INDEX `video_id` (`video_id`),
  INDEX `category_id` (`category_id`)
)
  DEFAULT CHARSET =utf8;

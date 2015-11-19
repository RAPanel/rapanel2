/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.0.21-MariaDB-log : Database - oikosural_main
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE = '' */;

/*!40014 SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0 */;
/*!40101 SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0 */;
/*Table structure for table `ra_cart` */

DROP TABLE IF EXISTS `ra_cart`;

CREATE TABLE `ra_cart` (
  `session_id` CHAR(32)         NOT NULL,
  `status`     INT(10) UNSIGNED NOT NULL,
  `item_id`    CHAR(32)         NOT NULL,
  `order_id`   INT(11)                   DEFAULT NULL,
  `data`       BLOB             NOT NULL,
  `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

/*Table structure for table `ra_character` */

DROP TABLE IF EXISTS `ra_character`;

CREATE TABLE `ra_character` (
  `id`    INT(11) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `url`   CHAR(32)
          CHARACTER SET latin1 NOT NULL,
  `type`  ENUM('text', 'number', 'textarea', 'dropdown', 'price', 'boolean', 'date', 'extend', 'url')
          CHARACTER SET latin1 NOT NULL,
  `multi` TINYINT(1) UNSIGNED  NOT NULL,
  `data`  BLOB,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 71
  DEFAULT CHARSET = latin1
  COLLATE = latin1_general_ci;

/*Table structure for table `ra_character_show` */

DROP TABLE IF EXISTS `ra_character_show`;

CREATE TABLE `ra_character_show` (
  `character_id` INT(11) UNSIGNED NOT NULL,
  `module_id`    INT(11) UNSIGNED NOT NULL,
  `page_id`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `filter`       INT(1) UNSIGNED  NOT NULL DEFAULT '0',
  `sort_id`      INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`character_id`, `module_id`, `page_id`, `filter`),
  KEY `module_id` (`module_id`),
  KEY `page_id` (`page_id`),
  CONSTRAINT `ra_character_show_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `ra_character` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `ra_character_show_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `ra_module` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

/*Table structure for table `ra_data_serialize` */

DROP TABLE IF EXISTS `ra_data_serialize`;

CREATE TABLE `ra_data_serialize` (
  `id`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`  CHAR(8)          NOT NULL,
  `value` LONGBLOB,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_index` */

DROP TABLE IF EXISTS `ra_index`;

CREATE TABLE `ra_index` (
  `owner_id` INT(11) UNSIGNED NOT NULL,
  `model`    CHAR(16)         NOT NULL,
  `type`     CHAR(16)         NOT NULL,
  `data_id`  INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`owner_id`, `model`, `type`, `data_id`),
  KEY `data_id` (`data_id`),
  CONSTRAINT `ra_index_ibfk_1` FOREIGN KEY (`data_id`) REFERENCES `ra_index_data` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_index_data` */

DROP TABLE IF EXISTS `ra_index_data`;

CREATE TABLE `ra_index_data` (
  `id`    INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `value` CHAR(64)         NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `value` (`value`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_message` */

DROP TABLE IF EXISTS `ra_message`;

CREATE TABLE `ra_message` (
  `id`       INT(11) NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(32)      DEFAULT NULL,
  `message`  TEXT,
  PRIMARY KEY (`id`),
  KEY `category` (`category`, `message`(64))
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 172
  DEFAULT CHARSET = latin1;

/*Table structure for table `ra_message_translate` */

DROP TABLE IF EXISTS `ra_message_translate`;

CREATE TABLE `ra_message_translate` (
  `id`          INT(11)              NOT NULL DEFAULT '0',
  `language`    VARCHAR(16)
                CHARACTER SET latin1 NOT NULL DEFAULT '',
  `translation` TEXT,
  PRIMARY KEY (`id`, `language`),
  CONSTRAINT `ra_message_translate_ibfk_1` FOREIGN KEY (`id`) REFERENCES `ra_message` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_migration` */

DROP TABLE IF EXISTS `ra_migration`;

CREATE TABLE `ra_migration` (
  `version`    VARCHAR(180) NOT NULL,
  `apply_time` INT(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

/*Table structure for table `ra_module` */

DROP TABLE IF EXISTS `ra_module`;

CREATE TABLE `ra_module` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url`        CHAR(16)         NOT NULL,
  `name`       VARCHAR(64)      NOT NULL,
  `class`      VARCHAR(255)     NOT NULL,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 14
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_module_settings` */

DROP TABLE IF EXISTS `ra_module_settings`;

CREATE TABLE `ra_module_settings` (
  `module_id` INT(11) UNSIGNED     NOT NULL,
  `sort`      INT(1) UNSIGNED      NOT NULL DEFAULT '0',
  `url`       CHAR(16)
              CHARACTER SET latin1 NOT NULL,
  `value`     VARCHAR(1024)
              CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`module_id`, `url`),
  CONSTRAINT `ra_module_settings_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `ra_module` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_order` */

DROP TABLE IF EXISTS `ra_order`;

CREATE TABLE `ra_order` (
  `id`          INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `status_id`   TINYINT(3) UNSIGNED NOT NULL,
  `is_paied`    TINYINT(1) UNSIGNED NOT NULL,
  `session_id`  CHAR(32)            NOT NULL,
  `delivery_id` INT(11) UNSIGNED    NOT NULL,
  `pay_id`      INT(11) UNSIGNED    NOT NULL,
  `data`        BLOB                NOT NULL,
  `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_page` */

DROP TABLE IF EXISTS `ra_page`;

CREATE TABLE `ra_page` (
  `id`          INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `is_category` TINYINT(1) UNSIGNED NOT NULL,
  `status`      TINYINT(1) UNSIGNED NOT NULL,
  `lft`         INT(11) UNSIGNED    NOT NULL,
  `rgt`         INT(11) UNSIGNED    NOT NULL,
  `level`       TINYINT(3) UNSIGNED NOT NULL,
  `parent_id`   INT(11) UNSIGNED             DEFAULT NULL,
  `module_id`   INT(11) UNSIGNED             DEFAULT NULL,
  `user_id`     INT(11)                      DEFAULT NULL,
  `url`         VARCHAR(255)        NOT NULL,
  `name`        VARCHAR(255)        NOT NULL,
  `about`       VARCHAR(255)        NOT NULL,
  `updated_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at`  TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `user_id` (`user_id`),
  KEY `module_id` (`module_id`),
  KEY `url` (`url`),
  CONSTRAINT `ra_page_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `ra_page` (`id`)
    ON DELETE SET NULL,
  CONSTRAINT `ra_page_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `ra_user` (`id`)
    ON DELETE SET NULL,
  CONSTRAINT `ra_page_ibfk_3` FOREIGN KEY (`module_id`) REFERENCES `ra_module` (`id`)
    ON DELETE SET NULL
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 1154
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_page_characters` */

DROP TABLE IF EXISTS `ra_page_characters`;

CREATE TABLE `ra_page_characters` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id`      INT(11) UNSIGNED NOT NULL,
  `character_id` INT(11) UNSIGNED NOT NULL,
  `value`        TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`, `character_id`),
  KEY `value` (`value`(16)),
  KEY `character_id` (`character_id`),
  CONSTRAINT `ra_page_characters_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `ra_page` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `ra_page_characters_ibfk_2` FOREIGN KEY (`character_id`) REFERENCES `ra_character` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 906
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_page_comments` */

DROP TABLE IF EXISTS `ra_page_comments`;

CREATE TABLE `ra_page_comments` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `page_id`    INT(11) UNSIGNED NOT NULL,
  `user_id`    INT(11)          NOT NULL,
  `parent_id`  INT(11) UNSIGNED NOT NULL,
  `rating`     INT(11)          NOT NULL,
  `text`       TEXT             NOT NULL,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ra_page_comments_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `ra_page` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `ra_page_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `ra_user` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_page_counts` */

DROP TABLE IF EXISTS `ra_page_counts`;

CREATE TABLE `ra_page_counts` (
  `page_id`  INT(11) UNSIGNED NOT NULL,
  `views`    INT(11) UNSIGNED NOT NULL,
  `likes`    INT(11) UNSIGNED NOT NULL,
  `comments` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`page_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_page_data` */

DROP TABLE IF EXISTS `ra_page_data`;

CREATE TABLE `ra_page_data` (
  `page_id`     INT(11) UNSIGNED NOT NULL,
  `title`       VARCHAR(255)     NOT NULL,
  `description` VARCHAR(255)     NOT NULL,
  `keywords`    VARCHAR(255)     NOT NULL,
  `content`     TEXT             NOT NULL,
  `tags`        TEXT             NOT NULL,
  PRIMARY KEY (`page_id`),
  KEY `ra_page_data_ibfk_1` (`page_id`),
  CONSTRAINT `ra_page_data_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `ra_page` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_page_price` */

DROP TABLE IF EXISTS `ra_page_price`;

CREATE TABLE `ra_page_price` (
  `id`      INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
  `page_id` INT(10) UNSIGNED      NOT NULL,
  `type_id` INT(10) UNSIGNED      NOT NULL,
  `unit`    VARCHAR(30)           NOT NULL,
  `value`   FLOAT(11, 2) UNSIGNED NOT NULL,
  `count`   INT(10) UNSIGNED      NOT NULL,
  `lastmod` TIMESTAMP             NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `type_id` (`type_id`),
  KEY `count` (`count`),
  KEY `value` (`value`)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 430186
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_page_session_data` */

DROP TABLE IF EXISTS `ra_page_session_data`;

CREATE TABLE `ra_page_session_data` (
  `page_id`    INT(11) UNSIGNED NOT NULL,
  `session`    CHAR(40)         NOT NULL,
  `type`       CHAR(8)          NOT NULL,
  `value`      CHAR(16)                  DEFAULT NULL,
  `last_visit` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_id`, `session`, `type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_photo` */

DROP TABLE IF EXISTS `ra_photo`;

CREATE TABLE `ra_photo` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sort_id`    INT(11) UNSIGNED NOT NULL,
  `owner_id`   INT(11) UNSIGNED NOT NULL,
  `model`      CHAR(32)         NOT NULL,
  `type`       CHAR(8)          NOT NULL,
  `name`       VARCHAR(255)     NOT NULL,
  `width`      INT(11) UNSIGNED NOT NULL,
  `height`     INT(11) UNSIGNED NOT NULL,
  `about`      VARCHAR(255)     NOT NULL,
  `cropParams` VARCHAR(255)     NOT NULL,
  `hash`       CHAR(32)         NOT NULL,
  `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `hash` (`hash`),
  KEY `external_id` (`owner_id`, `model`),
  KEY `sort_id` (`sort_id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 627
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_replaces` */

DROP TABLE IF EXISTS `ra_replaces`;

CREATE TABLE `ra_replaces` (
  `name`       CHAR(32)  NOT NULL,
  `value`      TEXT      NOT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_session` */

DROP TABLE IF EXISTS `ra_session`;

CREATE TABLE `ra_session` (
  `id`     CHAR(40) NOT NULL,
  `expire` INT(11) DEFAULT NULL,
  `data`   BLOB,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = latin1;

/*Table structure for table `ra_settings` */

DROP TABLE IF EXISTS `ra_settings`;

CREATE TABLE `ra_settings` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `path`       CHAR(64)         NOT NULL,
  `inputType`  CHAR(8)                   DEFAULT NULL,
  `name`       CHAR(64)         NOT NULL,
  `value`      TINYTEXT,
  `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 8
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_subscribe` */

DROP TABLE IF EXISTS `ra_subscribe`;

CREATE TABLE `ra_subscribe` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)              DEFAULT NULL,
  `email`      VARCHAR(255)     NOT NULL,
  `data`       BLOB,
  `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_user` */

DROP TABLE IF EXISTS `ra_user`;

CREATE TABLE `ra_user` (
  `id`         INT(11)                 NOT NULL    AUTO_INCREMENT,
  `role_id`    INT(11)                 NOT NULL,
  `status`     SMALLINT(6)             NOT NULL,
  `email`      VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  `new_email`  VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  `username`   VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  `password`   VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  `auth_key`   VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  `api_key`    VARCHAR(255)
               COLLATE utf8_unicode_ci NOT NULL,
  `login_ip`   VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  `login_time` TIMESTAMP               NULL        DEFAULT NULL,
  `create_ip`  VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  `created_at` TIMESTAMP               NULL        DEFAULT NULL,
  `updated_at` TIMESTAMP               NULL        DEFAULT NULL,
  `ban_time`   TIMESTAMP               NULL        DEFAULT NULL,
  `ban_reason` VARCHAR(255)
               COLLATE utf8_unicode_ci             DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ra_user_email` (`email`),
  UNIQUE KEY `ra_user_username` (`username`),
  KEY `ra_user_role_id` (`role_id`),
  CONSTRAINT `ra_user_role_id` FOREIGN KEY (`role_id`) REFERENCES `ra_user_role` (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 9
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/*Table structure for table `ra_user_auth` */

DROP TABLE IF EXISTS `ra_user_auth`;

CREATE TABLE `ra_user_auth` (
  `id`                  INT(11)                 NOT NULL AUTO_INCREMENT,
  `user_id`             INT(11)                 NOT NULL,
  `provider`            VARCHAR(255)
                        COLLATE utf8_unicode_ci NOT NULL,
  `provider_id`         VARCHAR(255)
                        COLLATE utf8_unicode_ci NOT NULL,
  `provider_attributes` TEXT
                        COLLATE utf8_unicode_ci NOT NULL,
  `updated_at`          TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at`          TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ra_user_auth_provider_id` (`provider_id`),
  KEY `ra_user_auth_user_id` (`user_id`),
  CONSTRAINT `ra_user_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `ra_user` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/*Table structure for table `ra_user_key` */

DROP TABLE IF EXISTS `ra_user_key`;

CREATE TABLE `ra_user_key` (
  `id`           INT(11)                 NOT NULL AUTO_INCREMENT,
  `user_id`      INT(11)                 NOT NULL,
  `type`         SMALLINT(6)             NOT NULL,
  `key_value`    VARCHAR(255)
                 COLLATE utf8_unicode_ci NOT NULL,
  `create_time`  TIMESTAMP               NULL     DEFAULT NULL,
  `consume_time` TIMESTAMP               NULL     DEFAULT NULL,
  `expire_time`  TIMESTAMP               NULL     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ra_user_key_key` (`key_value`),
  KEY `ra_user_key_user_id` (`user_id`),
  CONSTRAINT `ra_user_key_user_id` FOREIGN KEY (`user_id`) REFERENCES `ra_user` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/*Table structure for table `ra_user_profile` */

DROP TABLE IF EXISTS `ra_user_profile`;

CREATE TABLE `ra_user_profile` (
  `id`         INT(11)   NOT NULL      AUTO_INCREMENT,
  `user_id`    INT(11)   NOT NULL,
  `updated_at` TIMESTAMP NOT NULL      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL      DEFAULT CURRENT_TIMESTAMP,
  `full_name`  VARCHAR(255)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  `city`       VARCHAR(64)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  `vk`         VARCHAR(64)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  `fb`         VARCHAR(64)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  `ig`         VARCHAR(64)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  `tw`         VARCHAR(64)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  `options`    VARCHAR(64)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ra_profile_user_id` (`user_id`),
  CONSTRAINT `ra_profile_user_id` FOREIGN KEY (`user_id`) REFERENCES `ra_user` (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/*Table structure for table `ra_user_remember` */

DROP TABLE IF EXISTS `ra_user_remember`;

CREATE TABLE `ra_user_remember` (
  `user_id` INT(11) UNSIGNED NOT NULL,
  `key`     CHAR(32)         NOT NULL,
  `value`   CHAR(16)         NOT NULL,
  PRIMARY KEY (`user_id`, `key`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

/*Table structure for table `ra_user_role` */

DROP TABLE IF EXISTS `ra_user_role`;

CREATE TABLE `ra_user_role` (
  `id`         INT(11)                 NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(255)
               COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP               NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `can_admin`  SMALLINT(6)             NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

/*!40101 SET SQL_MODE = @OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES = @OLD_SQL_NOTES */;

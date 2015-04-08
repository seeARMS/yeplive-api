DROP DATABASE yeplive;
CREATE DATABASE `yeplive` DEFAULT CHARSET utf8;
USE `yeplive`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


CREATE TABLE IF NOT EXISTS `yeplive_chat_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `room_id` bigint(20) DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `isUploader` tinyint(2) DEFAULT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1231 ;


CREATE TABLE IF NOT EXISTS `yeplive_chat_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=482 ;


CREATE TABLE IF NOT EXISTS `yeplive_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT '1 - someone follows you; 2 - someone you''re following posts a new video; 3 - someone liked your video; 4 - someone disliked your video; 5 - someone sent you a private message; 6 - Chat Response Notifications; 7 - a new video is matching one of your saved search setting tags;',
  `user_sender_id` int(11) NOT NULL,
  `user_receiver_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT '1',
  `picture_path` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2060 ;


CREATE TABLE IF NOT EXISTS `yeplive_program` (
  `program_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `image_path` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `vod_enable` tinyint(1) DEFAULT NULL,
  `vod_path` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `vote` int(11) DEFAULT NULL,
  `vote_neg` int(11) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf16 DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `duration` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `start_time` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `end_time` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8,
  `connect_count` int(11) DEFAULT '0',
  `isMobile` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`program_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6058 ;



CREATE TABLE IF NOT EXISTS `yeplive_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reported_id` int(11) NOT NULL,
  `reason` varchar(50) NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;


CREATE TABLE IF NOT EXISTS `yeplive_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=266 ;


CREATE TABLE IF NOT EXISTS `yeplive_tags_program` (
  `tag_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  UNIQUE KEY `tag_id` (`tag_id`,`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*
CREATE TABLE IF NOT EXISTS `yeplive_users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_password` varchar(64) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;
*/

CREATE TABLE IF NOT EXISTS `yeplive_users` (
  `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `password` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `created_at` timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `updated_at` timestamp not null default '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

CREATE TABLE IF NOT EXISTS `yeplive_following` (
  `user_id` bigint(20) unsigned NOT NULL,
  `follower_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `updated_at` timestamp not null default '0000-00-00 00:00:00',
  PRIMARY KEY (`user_id`, `follower_id`),
  FOREIGN KEY (`user_id`) REFERENCES yeplive_users(`user_id`),
  FOREIGN KEY (`follower_id`) REFERENCES yeplive_users(`user_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `yeplive_user_pans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `pan_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `INDEX_NAME` (`user_id`,`pan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

CREATE TABLE IF NOT EXISTS `yeplive_user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `chat_response_notifications` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

CREATE TABLE IF NOT EXISTS `yeplive_user_yep` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `group_id` int(5) DEFAULT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `facebook_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_email` varchar(255) DEFAULT NULL,
  `facebook_access_token` varchar(255) DEFAULT NULL,
  `facebook_friends` int(5) DEFAULT NULL,
  `facebook_picture` varchar(255) DEFAULT NULL,
  `twitter_id` varchar(255) DEFAULT NULL,
  `twitter_name` varchar(255) DEFAULT NULL,
  `twitter_oauth_token` varchar(255) DEFAULT NULL,
  `twitter_oauth_token_secret` varchar(255) DEFAULT NULL,
  `twitter_img` varchar(255) DEFAULT NULL,
  `google_name` varchar(255) DEFAULT NULL,
  `google_email` varchar(255) DEFAULT NULL,
  `google_access_token` varchar(255) DEFAULT NULL,
  `google_picture` varchar(255) DEFAULT NULL,
  `yeplive_user_id` varchar(255) DEFAULT NULL,
  `bannedUntil` datetime DEFAULT NULL,
  `bannedPermanently` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=232 ;

CREATE TABLE IF NOT EXISTS `yeplive_votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `vote` tinyint(1) NOT NULL,
  PRIMARY KEY (`vote_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=970 ;

CREATE TABLE IF NOT EXISTS `yeplive_warning_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `program_title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=54 ;

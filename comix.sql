delimiter $$

CREATE DATABASE `angrybookman` /*!40100 DEFAULT CHARACTER SET latin1 */$$

CREATE TABLE `bookmark` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `rank` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `display_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_display_id_idx` (`user_id`,`display_id`),
  KEY `user_id_idx` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1$$


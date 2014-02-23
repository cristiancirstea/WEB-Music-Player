CREATE DATABASE IF NOT EXISTS `playerdb` DEFAULT CHARACTER SET "utf8";
USE `playerdb`;

CREATE TABLE IF NOT EXISTS `melodii` (
  `id_mel` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `artist` TEXT NOT NULL,
  `title` TEXT NOT NULL,
  `album` TEXT NOT NULL,
  `path` TEXT NOT NULL
) DEFAULT CHARACTER SET "utf8" ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `playlist_info` (
  `id_pl` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nume` TEXT NOT NULL
) DEFAULT CHARACTER SET "utf8" ENGINE=InnoDB; 

CREATE TABLE IF NOT EXISTS `playlist` (
  `id_pl` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_mel` INT(11) NOT NULL,
  `playing` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '0 - not playing; 1 - playing 2-paused; 3-selected;',
  `order_id` INT(11) NOT NULL DEFAULT '999' COMMENT '0 - not playing;'
) DEFAULT CHARACTER SET "utf8" ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `playlist_mel` (
  `id_pl` INT(11),
  `id_mel` INT(11),
  `playing` TINYINT(4),
  `order_id` INT(11),
  `artist` TEXT,
  `title` TEXT,
  `album` TEXT,
  `path` TEXT
) DEFAULT CHARACTER SET "utf8" ENGINE=InnoDB;


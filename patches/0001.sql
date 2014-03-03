CREATE DATABASE IF NOT EXISTS `playerDB` DEFAULT CHARACTER SET "utf8";
USE `playerDB`;

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

create  view `playlist_mel` as 
select `p`.`id_pl` as `id_pl`,
`p`.`id_mel` as `id_mel`,
`p`.`playing` as `playing`,
`p`.`order_id` as `order_id`,
`m`.`artist` as `artist`,
`m`.`title` as `title`,
`m`.`album` as `album`,
`m`.`path` as `path` 
from (`playlist` `p` join `melodii` `m`) 
where (`p`.`id_mel` = `m`.`id_mel`);



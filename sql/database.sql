-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Stř 31. říj 2012, 09:22
-- Verze MySQL: 5.1.49
-- Verze PHP: 5.3.3-1ubuntu9.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `elekce`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(10) unsigned NOT NULL,
  `author_id` int(10) unsigned DEFAULT NULL,
  `language_id` char(2) COLLATE utf8_czech_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `added` datetime NOT NULL,
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `perex` varchar(1000) COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - neschváleno, 1 - schváleno, 2 - odstraněno',
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `url` (`url`),
  KEY `feed_id` (`feed_id`),
  KEY `author_id` (`author_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=569 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `article_category`
--

CREATE TABLE IF NOT EXISTS `article_category` (
  `article_id` int(10) unsigned NOT NULL,
  `category_id` tinyint(10) unsigned NOT NULL,
  UNIQUE KEY `article_id_2` (`article_id`,`category_id`),
  KEY `article_id` (`article_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- --------------------------------------------------------

--
-- Struktura tabulky `author`
--

CREATE TABLE IF NOT EXISTS `author` (
  `author_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`author_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `weight` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `feed`
--

CREATE TABLE IF NOT EXISTS `feed` (
  `feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author_id` int(10) unsigned DEFAULT NULL,
  `language_id` char(2) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `feed` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  `imported` datetime DEFAULT NULL,
  PRIMARY KEY (`feed_id`),
  UNIQUE KEY `feed` (`feed`),
  KEY `author_id` (`author_id`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `language_id` char(2) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_5` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`),
  ADD CONSTRAINT `article_ibfk_3` FOREIGN KEY (`feed_id`) REFERENCES `feed` (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `article_ibfk_4` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `article_category`
--
ALTER TABLE `article_category`
  ADD CONSTRAINT `article_category_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`article_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `article_category_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Omezení pro tabulku `feed`
--
ALTER TABLE `feed`
  ADD CONSTRAINT `feed_ibfk_3` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`),
  ADD CONSTRAINT `feed_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON DELETE SET NULL ON UPDATE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


/* 21:10:37  E-lekce.cz */
ALTER TABLE `article` CHANGE `feed_id` `feed_id` INT(10)  UNSIGNED  NULL;

/* Joke support */
CREATE TABLE `joke` (
  `joke_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `text` text,
  `added` datetime NOT NULL,
  PRIMARY KEY (`joke_id`),
  KEY `added` (`added`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


/* Topic - series - support */
CREATE TABLE `topic` (
  `topic_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `perex` text NOT NULL,
  PRIMARY KEY (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `topic_article` (
  `topic_id` int(11) unsigned NOT NULL,
  `article_id` int(11) unsigned NOT NULL,
  `weight` tinyint(4) NOT NULL,
  UNIQUE KEY `topic_id` (`topic_id`,`article_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `topic_article_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`article_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `topic_article_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `topic_category` (
  `topic_id` int(11) unsigned NOT NULL,
  `category_id` tinyint(3) unsigned NOT NULL,
  UNIQUE KEY `topic_id` (`topic_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `topic_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `topic_category_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- phpMyAdmin SQL Dump
-- version 5.0.0
-- https://www.phpmyadmin.net/
--
-- Värd: 127.0.0.1:3306
-- Tid vid skapande: 03 mars 2020 kl 15:23
-- Serverversion: 5.7.27
-- PHP-version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databas: `blog`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `appearance`
--

DROP TABLE IF EXISTS `appearance`;
CREATE TABLE IF NOT EXISTS `appearance` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `BlogID` int(10) UNSIGNED NOT NULL,
  `ElementID` int(10) UNSIGNED NOT NULL,
  `backgroundcolor` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Font` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `FontSize` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Fontcolor` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `appearance`
--

INSERT INTO `appearance` (`ID`, `BlogID`, `ElementID`, `backgroundcolor`, `Font`, `FontSize`, `Fontcolor`, `Updated_at`, `Created_at`) VALUES
(2, 5, 5, 'bg-dark', 'serif', '70', '', '2020-03-02 09:59:18', '2020-02-28 11:54:44'),
(3, 5, 1, 'bg-success', NULL, NULL, NULL, '2020-02-29 20:50:11', '2020-02-28 11:54:44'),
(4, 5, 2, 'bg-danger', 'serif', '30', 'text-primary', '2020-03-02 10:00:38', '2020-02-28 11:54:44'),
(5, 5, 3, 'bg-secondary', NULL, NULL, NULL, '2020-03-02 10:20:01', '2020-02-28 11:54:44'),
(6, 5, 4, 'bg-secondary', 'cursive', '20', 'text-success', '2020-02-29 12:51:34', '2020-02-28 11:54:44'),
(7, 5, 7, '', 'sans-serif', '15', 'text-primary', '2020-02-29 20:51:00', '2020-02-28 11:54:44'),
(8, 5, 6, 'bg-light', 'cursive', '20', 'text-success', '2020-02-29 20:50:48', '2020-02-28 11:54:44'),
(9, 7, 5, 'bg-secondary', NULL, NULL, NULL, '2020-03-01 12:31:02', '2020-03-01 12:06:47'),
(10, 7, 1, 'bg-warning', NULL, NULL, NULL, '2020-03-03 14:59:24', '2020-03-01 12:06:47'),
(11, 7, 2, NULL, NULL, '30', NULL, '2020-03-03 14:59:04', '2020-03-01 12:06:47'),
(12, 7, 3, NULL, NULL, NULL, NULL, '2020-03-01 12:06:47', '2020-03-01 12:06:47'),
(13, 7, 4, NULL, NULL, NULL, NULL, '2020-03-01 12:06:47', '2020-03-01 12:06:47'),
(14, 7, 6, NULL, NULL, NULL, NULL, '2020-03-01 12:06:47', '2020-03-01 12:06:47'),
(15, 7, 7, NULL, NULL, NULL, NULL, '2020-03-01 12:06:47', '2020-03-01 12:06:47');

-- --------------------------------------------------------

--
-- Tabellstruktur `blog`
--

DROP TABLE IF EXISTS `blog`;
CREATE TABLE IF NOT EXISTS `blog` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Name` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `blog`
--

INSERT INTO `blog` (`ID`, `UserID`, `Name`, `Updated_at`, `Created_at`) VALUES
(5, 4, 'blog1', '2020-02-28 11:53:42', '2020-02-28 11:53:42'),
(7, 6, 'blog2', '2020-03-01 12:06:47', '2020-03-01 12:06:47');

-- --------------------------------------------------------

--
-- Tabellstruktur `blogpost`
--

DROP TABLE IF EXISTS `blogpost`;
CREATE TABLE IF NOT EXISTS `blogpost` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `BlogID` int(10) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Heading` text COLLATE utf8mb4_swedish_ci,
  `txtvalue` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `blogpost`
--

INSERT INTO `blogpost` (`ID`, `BlogID`, `UserID`, `Heading`, `txtvalue`, `Updated_at`, `Created_at`) VALUES
(1, 5, 4, 'Lorem ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '2020-03-03 14:55:00', '2020-03-03 14:55:00'),
(2, 5, 4, 'Lorem ipsum', 'Lorem ipsum', '2020-03-03 14:57:02', '2020-03-03 14:57:02'),
(3, 7, 6, 'Lorem ipsum', 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', '2020-03-03 14:59:55', '2020-03-03 14:59:55');

-- --------------------------------------------------------

--
-- Tabellstruktur `blogposthavecomments`
--

DROP TABLE IF EXISTS `blogposthavecomments`;
CREATE TABLE IF NOT EXISTS `blogposthavecomments` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `BlogpostID` int(10) UNSIGNED NOT NULL,
  `CommentID` int(10) UNSIGNED NOT NULL,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `blogposthavecomments`
--

INSERT INTO `blogposthavecomments` (`ID`, `BlogpostID`, `CommentID`, `Updated_at`, `Created_at`) VALUES
(1, 1, 1, '2020-03-03 14:57:30', '2020-03-03 14:57:30'),
(2, 2, 2, '2020-03-03 14:58:00', '2020-03-03 14:58:00'),
(3, 3, 3, '2020-03-03 15:00:03', '2020-03-03 15:00:03'),
(4, 1, 4, '2020-03-03 15:03:39', '2020-03-03 15:03:39'),
(5, 3, 5, '2020-03-03 15:05:49', '2020-03-03 15:05:49');

-- --------------------------------------------------------

--
-- Tabellstruktur `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL,
  `txtvalue` text COLLATE utf8mb4_swedish_ci,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `comments`
--

INSERT INTO `comments` (`ID`, `UserID`, `txtvalue`, `Updated_at`, `Created_at`) VALUES
(1, 4, 'Latin', '2020-03-03 14:57:30', '2020-03-03 14:57:30'),
(2, 4, 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum', '2020-03-03 14:58:00', '2020-03-03 14:58:00'),
(3, 6, 'Latin', '2020-03-03 15:00:03', '2020-03-03 15:00:03'),
(4, 6, 'b2 var här', '2020-03-03 15:03:39', '2020-03-03 15:03:39'),
(5, 4, 'b1 var här', '2020-03-03 15:05:49', '2020-03-03 15:05:49');

-- --------------------------------------------------------

--
-- Tabellstruktur `element`
--

DROP TABLE IF EXISTS `element`;
CREATE TABLE IF NOT EXISTS `element` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Elename` varchar(50) COLLATE utf8mb4_swedish_ci NOT NULL,
  `OrderBy` tinyint(4) NOT NULL,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `element`
--

INSERT INTO `element` (`ID`, `Elename`, `OrderBy`, `Updated_at`, `Created_at`) VALUES
(1, 'bakgrundsfärg för blognamn', 2, '2020-02-28 12:45:13', '2020-02-27 15:58:57'),
(2, 'blog namn', 3, '2020-02-28 12:45:25', '2020-02-27 15:59:24'),
(3, 'bakgrundsfärg för text', 4, '2020-02-28 12:45:34', '2020-02-27 15:58:57'),
(4, 'text under blognamn', 5, '2020-02-28 12:45:43', '2020-02-27 15:58:57'),
(5, 'bakgrundsfärg', 1, '2020-02-28 12:45:50', '2020-02-27 15:58:57'),
(6, 'blogRubrik', 6, '2020-02-28 12:45:50', '2020-02-27 15:58:57'),
(7, 'blogtext', 7, '2020-02-29 15:24:34', '2020-02-27 15:58:57');

-- --------------------------------------------------------

--
-- Tabellstruktur `recoverypass`
--

DROP TABLE IF EXISTS `recoverypass`;
CREATE TABLE IF NOT EXISTS `recoverypass` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED DEFAULT NULL,
  `Token` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `ExpireDate` datetime DEFAULT NULL,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur `text`
--

DROP TABLE IF EXISTS `text`;
CREATE TABLE IF NOT EXISTS `text` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `BlogID` int(10) UNSIGNED NOT NULL,
  `txtvalue` text COLLATE utf8mb4_swedish_ci,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `text`
--

INSERT INTO `text` (`ID`, `BlogID`, `txtvalue`, `Updated_at`, `Created_at`) VALUES
(1, 5, 'Lorem ipsum dolor sit amet', '2020-03-02 10:16:27', '2020-02-29 12:26:07'),
(2, 7, '', '2020-03-03 14:58:46', '2020-02-29 12:26:07'),
(3, 8, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sagittis, turpis ut pretium ultrices, est purus egestas urna, id finibus erat mauris ac est. Nunc sed ligula iaculis, mollis nunc sit amet, cursus nisi.', '2020-03-03 14:48:30', '2020-03-03 14:48:30');

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Username` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Pass` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `IsBlocked` tinyint(1) DEFAULT '0',
  `NumberOfLoginAttempt` tinyint(1) DEFAULT '0',
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `users`
--

INSERT INTO `users` (`ID`, `Username`, `Pass`, `IsBlocked`, `NumberOfLoginAttempt`, `Updated_at`, `Created_at`) VALUES
(4, 'b1@vadman.nu', '$argon2i$v=19$m=131072,t=8,p=3$VWkyY2FZamlzZXo4OExELw$yxLe+e+prip+9Soy/wN7brXSl5ME8BkTQTgNANg0DiY', 0, 0, '2020-03-03 13:33:01', '2020-02-28 11:53:42'),
(6, 'b2@vadman.nu', '$argon2i$v=19$m=131072,t=8,p=3$UElTLkdTRkxObEFNVTJSbg$FYkSfnj+GCa2aZwF4vs/fMqqpApBtRI1jEVgr2Ake7w', 0, 0, '2020-03-01 12:06:47', '2020-03-01 12:06:47');

-- --------------------------------------------------------

--
-- Tabellstruktur `usersettings`
--

DROP TABLE IF EXISTS `usersettings`;
CREATE TABLE IF NOT EXISTS `usersettings` (
  `UserID` int(10) UNSIGNED NOT NULL,
  `Firstname` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Surname` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `TFA` tinyint(1) DEFAULT '0',
  `Email` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Phone` varchar(191) COLLATE utf8mb4_swedish_ci DEFAULT NULL,
  `Is_Deleted` tinyint(1) DEFAULT '0',
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_swedish_ci;

--
-- Dumpning av Data i tabell `usersettings`
--

INSERT INTO `usersettings` (`UserID`, `Firstname`, `Surname`, `TFA`, `Email`, `Phone`, `Is_Deleted`, `Updated_at`, `Created_at`) VALUES
(4, NULL, NULL, 0, NULL, NULL, 0, '2020-02-28 11:53:42', '2020-02-28 11:53:42'),
(6, NULL, NULL, 0, NULL, NULL, 0, '2020-03-01 12:06:47', '2020-03-01 12:06:47');

-- --------------------------------------------------------

--
-- Tabellstruktur `usertoken`
--

DROP TABLE IF EXISTS `usertoken`;
CREATE TABLE IF NOT EXISTS `usertoken` (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED DEFAULT NULL,
  `Token` varchar(191) DEFAULT NULL,
  `Created` datetime DEFAULT CURRENT_TIMESTAMP,
  `Updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

--
-- Dumpning av Data i tabell `usertoken`
--

INSERT INTO `usertoken` (`ID`, `UserID`, `Token`, `Created`, `Updated`) VALUES
(6, 4, '4fbeb4c06ccc2a9c3515748d326db3676e59917c', '2020-02-28 15:20:11', '2020-03-03 16:05:10'),
(7, 6, '61f159e9ed46aac847910ef4291753a4178db769', '2020-03-01 13:06:47', '2020-03-03 15:58:39');

--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `usersettings`
--
ALTER TABLE `usersettings`
  ADD CONSTRAINT `usersettings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`);

--
-- Restriktioner för tabell `usertoken`
--
ALTER TABLE `usertoken`
  ADD CONSTRAINT `usertoken_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


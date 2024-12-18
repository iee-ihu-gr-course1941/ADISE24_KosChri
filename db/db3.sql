-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               11.6.2-MariaDB - Arch Linux
-- Server OS:                    Linux
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for projectDB
DROP DATABASE IF EXISTS `projectDB`;
CREATE DATABASE IF NOT EXISTS `projectDB` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `projectDB`;

-- Dumping structure for table projectDB.board
DROP TABLE IF EXISTS `board`;
CREATE TABLE IF NOT EXISTS `board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `x_position` int(11) NOT NULL,
  `y_position` int(11) NOT NULL,
  `tile_shape` enum('circle','star','square','clover','diamond','sun') NOT NULL,
  `tile_color` enum('red','blue','green','yellow','purple','orange') NOT NULL,
  `placed_by_player_id` int(11) NOT NULL,
  `placed_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  KEY `placed_by_player_id` (`placed_by_player_id`),
  CONSTRAINT `board_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  CONSTRAINT `board_ibfk_2` FOREIGN KEY (`placed_by_player_id`) REFERENCES `players` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table projectDB.board: ~0 rows (approximately)
DELETE FROM `board`;

-- Dumping structure for table projectDB.games
DROP TABLE IF EXISTS `games`;
CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `game_start_time` timestamp NULL DEFAULT current_timestamp(),
  `game_end_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `player1_id` (`player1_id`),
  KEY `player2_id` (`player2_id`),
  CONSTRAINT `games_ibfk_1` FOREIGN KEY (`player1_id`) REFERENCES `players` (`id`),
  CONSTRAINT `games_ibfk_2` FOREIGN KEY (`player2_id`) REFERENCES `players` (`id`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`player1_id` <> `player2_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table projectDB.games: ~1 rows (approximately)
DELETE FROM `games`;
INSERT INTO `games` (`id`, `player1_id`, `player2_id`, `game_start_time`, `game_end_time`) VALUES
	(1, 2, 1, '2024-12-18 16:56:35', NULL);

-- Dumping structure for table projectDB.game_status
DROP TABLE IF EXISTS `game_status`;
CREATE TABLE IF NOT EXISTS `game_status` (
  `game_id` int(11) NOT NULL,
  `status` enum('not active','initialized','started','ended','aborted') NOT NULL DEFAULT 'not active',
  `current_player_id` int(11) DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `last_change` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`game_id`),
  KEY `current_player_id` (`current_player_id`),
  KEY `winner_id` (`winner_id`),
  CONSTRAINT `game_status_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  CONSTRAINT `game_status_ibfk_2` FOREIGN KEY (`current_player_id`) REFERENCES `players` (`id`) ON DELETE SET NULL,
  CONSTRAINT `game_status_ibfk_3` FOREIGN KEY (`winner_id`) REFERENCES `players` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table projectDB.game_status: ~1 rows (approximately)
DELETE FROM `game_status`;
INSERT INTO `game_status` (`game_id`, `status`, `current_player_id`, `winner_id`, `last_change`) VALUES
	(1, 'initialized', 1, 2, '2024-12-18 16:57:05');

-- Dumping structure for table projectDB.players
DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `token` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table projectDB.players: ~2 rows (approximately)
DELETE FROM `players`;
INSERT INTO `players` (`id`, `username`, `token`) VALUES
	(1, 'fqw', NULL),
	(2, 'fadsf', NULL);

-- Dumping structure for table projectDB.player_tiles
DROP TABLE IF EXISTS `player_tiles`;
CREATE TABLE IF NOT EXISTS `player_tiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `tile_shape` enum('circle','star','square','clover','diamond','sun') NOT NULL,
  `tile_color` enum('red','blue','green','yellow','purple','orange') NOT NULL,
  `drawn_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  KEY `player_id` (`player_id`),
  CONSTRAINT `player_tiles_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  CONSTRAINT `player_tiles_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table projectDB.player_tiles: ~0 rows (approximately)
DELETE FROM `player_tiles`;

-- Dumping structure for table projectDB.tile_bag
DROP TABLE IF EXISTS `tile_bag`;
CREATE TABLE IF NOT EXISTS `tile_bag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `tile_shape` enum('circle','star','square','clover','diamond','sun') NOT NULL,
  `tile_color` enum('red','blue','green','yellow','purple','orange') NOT NULL,
  `is_drawn` tinyint(1) DEFAULT 0,
  `drawn_by_player_id` int(11) DEFAULT NULL,
  `drawn_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game_id` (`game_id`),
  KEY `drawn_by_player_id` (`drawn_by_player_id`),
  CONSTRAINT `tile_bag_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  CONSTRAINT `tile_bag_ibfk_2` FOREIGN KEY (`drawn_by_player_id`) REFERENCES `players` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table projectDB.tile_bag: ~0 rows (approximately)
DELETE FROM `tile_bag`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

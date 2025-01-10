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
CREATE DATABASE IF NOT EXISTS `projectDB` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `projectDB`;

-- Dumping structure for table projectDB.board
CREATE TABLE IF NOT EXISTS `board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `row` int(11) NOT NULL,
  `col` int(11) NOT NULL,
  `tile_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `row` (`row`,`col`),
  KEY `fk_player_id` (`player_id`),
  CONSTRAINT `fk_player_id` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.board: ~0 rows (approximately)

-- Dumping structure for table projectDB.game_status
CREATE TABLE IF NOT EXISTS `game_status` (
  `status` enum('initialized','started','ended','not active') NOT NULL DEFAULT 'not active',
  `p_turn` enum('p1','p2','none') DEFAULT 'none',
  `result` enum('p1','p2','none') DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.game_status: ~1 rows (approximately)
INSERT INTO `game_status` (`status`, `p_turn`, `result`) VALUES
	('not active', 'none', 'none');

-- Dumping structure for table projectDB.players
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `score` int(11) DEFAULT 0,
  `token` varchar(32) NOT NULL,
  `tilesPlacedThisTurn` int(11) DEFAULT 0,
  `last_action` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tilesDiscardedThisTurn` int(11) DEFAULT 0,
  `player_slot` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_player_slot` (`player_slot`),
  CONSTRAINT `check_player_slot` CHECK (`player_slot` in ('p1','p2'))
) ENGINE=InnoDB AUTO_INCREMENT=969 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.players: ~0 rows (approximately)

-- Dumping structure for table projectDB.player_hands
CREATE TABLE IF NOT EXISTS `player_hands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL,
  `tile_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `player_id` (`player_id`),
  KEY `fk_tile_id` (`tile_id`),
  CONSTRAINT `fk_tile_id` FOREIGN KEY (`tile_id`) REFERENCES `tiles` (`id`),
  CONSTRAINT `player_hands_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.player_hands: ~0 rows (approximately)

-- Dumping structure for table projectDB.tiles
CREATE TABLE IF NOT EXISTS `tiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color` varchar(20) NOT NULL,
  `shape` varchar(20) NOT NULL,
  `inside_bag` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4513 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.tiles: ~0 rows (approximately)

-- Dumping structure for trigger projectDB.limit_two_players
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER limit_two_players
BEFORE INSERT ON players
FOR EACH ROW
BEGIN
    DECLARE player_count INT;

    -- Get the current number of players in the table
    SELECT COUNT(*) INTO player_count FROM players;

    -- If there are already 2 players, stop the insert
    IF player_count >= 2 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot add more than two players.';
    END IF;

    -- If the player_count is less than 2, the insert will proceed
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

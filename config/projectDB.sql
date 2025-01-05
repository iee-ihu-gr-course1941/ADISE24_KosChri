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
) ENGINE=InnoDB AUTO_INCREMENT=3602 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.board: ~225 rows (approximately)
INSERT INTO `board` (`id`, `row`, `col`, `tile_id`, `player_id`) VALUES
	(3377, 1, 1, NULL, NULL),
	(3378, 1, 2, NULL, NULL),
	(3379, 1, 3, NULL, NULL),
	(3380, 1, 4, NULL, NULL),
	(3381, 1, 5, NULL, NULL),
	(3382, 1, 6, NULL, NULL),
	(3383, 1, 7, NULL, NULL),
	(3384, 1, 8, NULL, NULL),
	(3385, 1, 9, NULL, NULL),
	(3386, 1, 10, NULL, NULL),
	(3387, 1, 11, NULL, NULL),
	(3388, 1, 12, NULL, NULL),
	(3389, 1, 13, NULL, NULL),
	(3390, 1, 14, NULL, NULL),
	(3391, 1, 15, NULL, NULL),
	(3392, 2, 1, NULL, NULL),
	(3393, 2, 2, NULL, NULL),
	(3394, 2, 3, NULL, NULL),
	(3395, 2, 4, NULL, NULL),
	(3396, 2, 5, NULL, NULL),
	(3397, 2, 6, NULL, NULL),
	(3398, 2, 7, NULL, NULL),
	(3399, 2, 8, NULL, NULL),
	(3400, 2, 9, NULL, NULL),
	(3401, 2, 10, NULL, NULL),
	(3402, 2, 11, NULL, NULL),
	(3403, 2, 12, NULL, NULL),
	(3404, 2, 13, NULL, NULL),
	(3405, 2, 14, NULL, NULL),
	(3406, 2, 15, NULL, NULL),
	(3407, 3, 1, NULL, NULL),
	(3408, 3, 2, NULL, NULL),
	(3409, 3, 3, NULL, NULL),
	(3410, 3, 4, NULL, NULL),
	(3411, 3, 5, NULL, NULL),
	(3412, 3, 6, NULL, NULL),
	(3413, 3, 7, NULL, NULL),
	(3414, 3, 8, NULL, NULL),
	(3415, 3, 9, NULL, NULL),
	(3416, 3, 10, NULL, NULL),
	(3417, 3, 11, NULL, NULL),
	(3418, 3, 12, NULL, NULL),
	(3419, 3, 13, NULL, NULL),
	(3420, 3, 14, NULL, NULL),
	(3421, 3, 15, NULL, NULL),
	(3422, 4, 1, NULL, NULL),
	(3423, 4, 2, NULL, NULL),
	(3424, 4, 3, NULL, NULL),
	(3425, 4, 4, NULL, NULL),
	(3426, 4, 5, NULL, NULL),
	(3427, 4, 6, NULL, NULL),
	(3428, 4, 7, NULL, NULL),
	(3429, 4, 8, NULL, NULL),
	(3430, 4, 9, NULL, NULL),
	(3431, 4, 10, NULL, NULL),
	(3432, 4, 11, NULL, NULL),
	(3433, 4, 12, NULL, NULL),
	(3434, 4, 13, NULL, NULL),
	(3435, 4, 14, NULL, NULL),
	(3436, 4, 15, NULL, NULL),
	(3437, 5, 1, NULL, NULL),
	(3438, 5, 2, NULL, NULL),
	(3439, 5, 3, NULL, NULL),
	(3440, 5, 4, NULL, NULL),
	(3441, 5, 5, NULL, NULL),
	(3442, 5, 6, NULL, NULL),
	(3443, 5, 7, NULL, NULL),
	(3444, 5, 8, NULL, NULL),
	(3445, 5, 9, NULL, NULL),
	(3446, 5, 10, NULL, NULL),
	(3447, 5, 11, NULL, NULL),
	(3448, 5, 12, NULL, NULL),
	(3449, 5, 13, NULL, NULL),
	(3450, 5, 14, NULL, NULL),
	(3451, 5, 15, NULL, NULL),
	(3452, 6, 1, NULL, NULL),
	(3453, 6, 2, NULL, NULL),
	(3454, 6, 3, NULL, NULL),
	(3455, 6, 4, NULL, NULL),
	(3456, 6, 5, NULL, NULL),
	(3457, 6, 6, NULL, NULL),
	(3458, 6, 7, NULL, NULL),
	(3459, 6, 8, NULL, NULL),
	(3460, 6, 9, NULL, NULL),
	(3461, 6, 10, NULL, NULL),
	(3462, 6, 11, NULL, NULL),
	(3463, 6, 12, NULL, NULL),
	(3464, 6, 13, NULL, NULL),
	(3465, 6, 14, NULL, NULL),
	(3466, 6, 15, NULL, NULL),
	(3467, 7, 1, NULL, NULL),
	(3468, 7, 2, NULL, NULL),
	(3469, 7, 3, NULL, NULL),
	(3470, 7, 4, NULL, NULL),
	(3471, 7, 5, NULL, NULL),
	(3472, 7, 6, NULL, NULL),
	(3473, 7, 7, 4232, 938),
	(3474, 7, 8, NULL, NULL),
	(3475, 7, 9, NULL, NULL),
	(3476, 7, 10, NULL, NULL),
	(3477, 7, 11, NULL, NULL),
	(3478, 7, 12, NULL, NULL),
	(3479, 7, 13, NULL, NULL),
	(3480, 7, 14, NULL, NULL),
	(3481, 7, 15, NULL, NULL),
	(3482, 8, 1, NULL, NULL),
	(3483, 8, 2, NULL, NULL),
	(3484, 8, 3, NULL, NULL),
	(3485, 8, 4, NULL, NULL),
	(3486, 8, 5, NULL, NULL),
	(3487, 8, 6, NULL, NULL),
	(3488, 8, 7, NULL, NULL),
	(3489, 8, 8, NULL, NULL),
	(3490, 8, 9, NULL, NULL),
	(3491, 8, 10, NULL, NULL),
	(3492, 8, 11, NULL, NULL),
	(3493, 8, 12, NULL, NULL),
	(3494, 8, 13, NULL, NULL),
	(3495, 8, 14, NULL, NULL),
	(3496, 8, 15, NULL, NULL),
	(3497, 9, 1, NULL, NULL),
	(3498, 9, 2, NULL, NULL),
	(3499, 9, 3, NULL, NULL),
	(3500, 9, 4, NULL, NULL),
	(3501, 9, 5, NULL, NULL),
	(3502, 9, 6, NULL, NULL),
	(3503, 9, 7, NULL, NULL),
	(3504, 9, 8, NULL, NULL),
	(3505, 9, 9, NULL, NULL),
	(3506, 9, 10, NULL, NULL),
	(3507, 9, 11, NULL, NULL),
	(3508, 9, 12, NULL, NULL),
	(3509, 9, 13, NULL, NULL),
	(3510, 9, 14, NULL, NULL),
	(3511, 9, 15, NULL, NULL),
	(3512, 10, 1, NULL, NULL),
	(3513, 10, 2, NULL, NULL),
	(3514, 10, 3, NULL, NULL),
	(3515, 10, 4, NULL, NULL),
	(3516, 10, 5, NULL, NULL),
	(3517, 10, 6, NULL, NULL),
	(3518, 10, 7, NULL, NULL),
	(3519, 10, 8, NULL, NULL),
	(3520, 10, 9, NULL, NULL),
	(3521, 10, 10, NULL, NULL),
	(3522, 10, 11, NULL, NULL),
	(3523, 10, 12, NULL, NULL),
	(3524, 10, 13, NULL, NULL),
	(3525, 10, 14, NULL, NULL),
	(3526, 10, 15, NULL, NULL),
	(3527, 11, 1, NULL, NULL),
	(3528, 11, 2, NULL, NULL),
	(3529, 11, 3, NULL, NULL),
	(3530, 11, 4, NULL, NULL),
	(3531, 11, 5, NULL, NULL),
	(3532, 11, 6, NULL, NULL),
	(3533, 11, 7, NULL, NULL),
	(3534, 11, 8, NULL, NULL),
	(3535, 11, 9, NULL, NULL),
	(3536, 11, 10, NULL, NULL),
	(3537, 11, 11, NULL, NULL),
	(3538, 11, 12, NULL, NULL),
	(3539, 11, 13, NULL, NULL),
	(3540, 11, 14, NULL, NULL),
	(3541, 11, 15, NULL, NULL),
	(3542, 12, 1, NULL, NULL),
	(3543, 12, 2, NULL, NULL),
	(3544, 12, 3, NULL, NULL),
	(3545, 12, 4, NULL, NULL),
	(3546, 12, 5, NULL, NULL),
	(3547, 12, 6, NULL, NULL),
	(3548, 12, 7, NULL, NULL),
	(3549, 12, 8, NULL, NULL),
	(3550, 12, 9, NULL, NULL),
	(3551, 12, 10, NULL, NULL),
	(3552, 12, 11, NULL, NULL),
	(3553, 12, 12, NULL, NULL),
	(3554, 12, 13, NULL, NULL),
	(3555, 12, 14, NULL, NULL),
	(3556, 12, 15, NULL, NULL),
	(3557, 13, 1, NULL, NULL),
	(3558, 13, 2, NULL, NULL),
	(3559, 13, 3, NULL, NULL),
	(3560, 13, 4, NULL, NULL),
	(3561, 13, 5, NULL, NULL),
	(3562, 13, 6, NULL, NULL),
	(3563, 13, 7, NULL, NULL),
	(3564, 13, 8, NULL, NULL),
	(3565, 13, 9, NULL, NULL),
	(3566, 13, 10, NULL, NULL),
	(3567, 13, 11, NULL, NULL),
	(3568, 13, 12, NULL, NULL),
	(3569, 13, 13, NULL, NULL),
	(3570, 13, 14, NULL, NULL),
	(3571, 13, 15, NULL, NULL),
	(3572, 14, 1, NULL, NULL),
	(3573, 14, 2, NULL, NULL),
	(3574, 14, 3, NULL, NULL),
	(3575, 14, 4, NULL, NULL),
	(3576, 14, 5, NULL, NULL),
	(3577, 14, 6, NULL, NULL),
	(3578, 14, 7, NULL, NULL),
	(3579, 14, 8, NULL, NULL),
	(3580, 14, 9, NULL, NULL),
	(3581, 14, 10, NULL, NULL),
	(3582, 14, 11, NULL, NULL),
	(3583, 14, 12, NULL, NULL),
	(3584, 14, 13, NULL, NULL),
	(3585, 14, 14, NULL, NULL),
	(3586, 14, 15, NULL, NULL),
	(3587, 15, 1, NULL, NULL),
	(3588, 15, 2, NULL, NULL),
	(3589, 15, 3, NULL, NULL),
	(3590, 15, 4, NULL, NULL),
	(3591, 15, 5, NULL, NULL),
	(3592, 15, 6, NULL, NULL),
	(3593, 15, 7, NULL, NULL),
	(3594, 15, 8, NULL, NULL),
	(3595, 15, 9, NULL, NULL),
	(3596, 15, 10, NULL, NULL),
	(3597, 15, 11, NULL, NULL),
	(3598, 15, 12, NULL, NULL),
	(3599, 15, 13, NULL, NULL),
	(3600, 15, 14, NULL, NULL),
	(3601, 15, 15, NULL, NULL);

-- Dumping structure for table projectDB.game_status
CREATE TABLE IF NOT EXISTS `game_status` (
  `status` enum('not active','initialized','started','ended','aborded') NOT NULL DEFAULT 'not active',
  `p_turn` enum('p1','p2') DEFAULT NULL,
  `result` enum('p1','p2','Draw') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.game_status: ~1 rows (approximately)
INSERT INTO `game_status` (`status`, `p_turn`, `result`) VALUES
	('started', 'p2', NULL);

-- Dumping structure for table projectDB.players
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `score` int(11) DEFAULT 0,
  `token` varchar(32) NOT NULL,
  `tilesPlacedThisTurn` int(11) DEFAULT 0,
  `last_action` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tilesDiscardedThisTurn` int(11) DEFAULT 0,
  `player_slot` enum('p1','p2') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `player_slot` (`player_slot`)
) ENGINE=InnoDB AUTO_INCREMENT=941 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.players: ~2 rows (approximately)
INSERT INTO `players` (`id`, `name`, `score`, `token`, `tilesPlacedThisTurn`, `last_action`, `tilesDiscardedThisTurn`, `player_slot`) VALUES
	(938, 'kostis', 0, '3bc259047a5aa1d475181037b728685d', 1, '2025-01-04 21:01:59', 0, 'p1'),
	(940, 'christos', 0, 'd06033acd1dd606d75206e9b761b9faa', 0, '2025-01-04 20:16:55', 1, 'p2');

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
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.player_hands: ~0 rows (approximately)

-- Dumping structure for table projectDB.tiles
CREATE TABLE IF NOT EXISTS `tiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `color` varchar(20) NOT NULL,
  `shape` varchar(20) NOT NULL,
  `inside_bag` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table projectDB.tiles: ~32 rows (approximately)
INSERT INTO `tiles` (`id`, `color`, `shape`, `inside_bag`) VALUES
	(4209, 'red', 'circle', 1),
	(4210, 'red', 'circle', 1),
	(4211, 'red', 'square', 1),
	(4212, 'red', 'square', 1),
	(4213, 'red', 'triangle', 1),
	(4214, 'red', 'triangle', 1),
	(4215, 'red', 'star', 1),
	(4216, 'red', 'star', 1),
	(4217, 'blue', 'circle', 1),
	(4218, 'blue', 'circle', 1),
	(4219, 'blue', 'square', 1),
	(4220, 'blue', 'square', 1),
	(4221, 'blue', 'triangle', 1),
	(4222, 'blue', 'triangle', 1),
	(4223, 'blue', 'star', 1),
	(4224, 'blue', 'star', 1),
	(4225, 'green', 'circle', 1),
	(4226, 'green', 'circle', 1),
	(4227, 'green', 'square', 1),
	(4228, 'green', 'square', 1),
	(4229, 'green', 'triangle', 1),
	(4230, 'green', 'triangle', 1),
	(4231, 'green', 'star', 1),
	(4232, 'green', 'star', 0),
	(4233, 'yellow', 'circle', 1),
	(4234, 'yellow', 'circle', 1),
	(4235, 'yellow', 'square', 1),
	(4236, 'yellow', 'square', 1),
	(4237, 'yellow', 'triangle', 1),
	(4238, 'yellow', 'triangle', 1),
	(4239, 'yellow', 'star', 1),
	(4240, 'yellow', 'star', 1);

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


CREATE TABLE IF NOT EXISTS `reviews` (
	`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`movie_id` INT(11) NOT NULL,
	`name` varchar(55) NOT NULL,
	`email` varchar(65) NOT NULL,
	`mesaj` text
);
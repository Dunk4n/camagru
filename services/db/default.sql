CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(100) UNIQUE NOT NULL,
    `email` varchar(200) /*UNIQUE*/ NOT NULL,
    `verified` tinyint(1) NOT NULL,
    `emailForMessage` tinyint(1) DEFAULT 1,
    `token` varchar(100) NOT NULL,
    `password` varchar(256) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `userId` int(11) NOT NULL,
    `fileName` varchar(255) NOT NULL,
    `uploadedOn` datetime NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (userId) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `comments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `imageId` int(11) NOT NULL,
    `userId` int(11) NOT NULL,
    `content` varchar(255) NOT NULL,
    `uploadedOn` datetime NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (imageId) REFERENCES images(id),
    FOREIGN KEY (userId) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `likes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `imageId` int(11) NOT NULL,
    `userId` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (imageId) REFERENCES images(id),
    FOREIGN KEY (userId) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*INSERT INTO `accounts` (`id`, `username`, `password`, `email`) VALUES (1, 'test', '$2y$10$SfhYIDtn.iOuCW7zfoFLuuZHX6lja4lF4XA4JqNmpiH/.P3zB8JCa', 'test@test.com');*/

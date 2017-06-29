CREATE TABLE IF NOT EXISTS `zoo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE latin1_general_ci NOT NULL,
  `cnt` smallint(6) NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=6 ;

INSERT INTO `zoo` (`id`, `name`, `cnt`, `updated`) VALUES
(1, 'dog', 1, NULL),
(2, 'cat', 2, NULL),
(3, 'tiger', 3, NULL),
(4, 'lion', 4, NULL),
(5, 'wolf', 5, NULL);

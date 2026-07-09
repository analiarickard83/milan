SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `parameters` (
  `id` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `value` text COLLATE latin1_spanish_ci NOT NULL,
  `type` varchar(10) COLLATE latin1_spanish_ci NOT NULL DEFAULT 'text',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

INSERT INTO `parameters` (`id`, `value`, `type`, `created`, `modified`) VALUES
('tema', 'simplex', 'text', '2015-10-07 12:55:52', '2016-02-19 21:43:26');

CREATE TABLE IF NOT EXISTS `registers` (
  `id` int(11) NOT NULL,
  `plant_nro` int(11) DEFAULT NULL,
  `oncca_nro` int(11) DEFAULT NULL,
  `troop_nro` int(11) DEFAULT NULL,
  `dopro_nro` int(11) DEFAULT NULL,
  `dte_nro` int(11) DEFAULT NULL,
  `romaneo_oncca_nro` int(11) DEFAULT NULL,
  `owner` varchar(250) COLLATE latin1_spanish_ci DEFAULT NULL,
  `cuit_nro` varchar(13) COLLATE latin1_spanish_ci DEFAULT NULL,
  `renspa_nro` int(11) DEFAULT NULL,
  `plant` varchar(100) COLLATE latin1_spanish_ci DEFAULT NULL,
  `department` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `locality` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `province` varchar(25) COLLATE latin1_spanish_ci DEFAULT NULL,
  `consignee` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `consignee_oncca_nro` int(11) DEFAULT NULL,
  `species` set('caprino','porcino','ovino','conejo','aves','bovino') COLLATE latin1_spanish_ci NOT NULL,
  `enter_kg` decimal(9,2) DEFAULT NULL,
  `category` varchar(20) COLLATE latin1_spanish_ci DEFAULT NULL,
  `confiscated_organs` int(11) DEFAULT NULL,
  `confiscated_why` text COLLATE latin1_spanish_ci,
  `organs_names` text COLLATE latin1_spanish_ci,
  `organs_patology` text COLLATE latin1_spanish_ci,
  `end_kg` decimal(9,2) DEFAULT NULL,
  `meat_target` varchar(250) COLLATE latin1_spanish_ci DEFAULT NULL,
  `veterinary` varchar(50) COLLATE latin1_spanish_ci DEFAULT NULL,
  `notes` text COLLATE latin1_spanish_ci,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `lastname` varchar(25) COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `role` set('U','A') COLLATE latin1_spanish_ci NOT NULL DEFAULT 'U',
  `email` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `image` varchar(50) COLLATE latin1_spanish_ci NOT NULL,
  `token` varchar(200) COLLATE latin1_spanish_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci;

INSERT INTO `users` (`id`, `name`, `lastname`, `password`, `role`, `email`, `image`, `token`, `created`, `modified`) VALUES
(1, 'Admin', 'Admin', '$2a$10$aE2lIkvumb/mgqJWHhuS5eivR6QEAU3VWBYsdLV8qvmh2DpRdJw76', 'A', 'admin@inicial.com', '', '', '2016-02-19 21:38:34', '2016-02-19 21:38:34');


ALTER TABLE `parameters`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `registers`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `registers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

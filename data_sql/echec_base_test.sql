-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 30 Janvier 2016 à 22:08
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `echec`
--

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

CREATE TABLE IF NOT EXISTS `joueur` (
  `login` varchar(32) NOT NULL,
  `mdp` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `joueur`
--

INSERT INTO `joueur` (`login`, `mdp`, `email`) VALUES
('c', 'e0323a9039add2978bf5b49550572c7c', 'c@c.fr'),
('b', '21ad0bd836b90d08f4cf640b4c298e7c', 'b@b.fr'),
('a', '4124bc0a9335c27f086f24ba207a4912', 'a@a.fr');

-- --------------------------------------------------------

--
-- Structure de la table `parties`
--

CREATE TABLE IF NOT EXISTS `parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(142) NOT NULL,
  `tour` int(4) NOT NULL,
  `trait` int(1) NOT NULL,
  `j1` varchar(142) NOT NULL,
  `j2` varchar(142) NOT NULL,
  `roques_j1` text NOT NULL,
  `roques_j2` text NOT NULL,
  `etat_du_jeu` text NOT NULL,
  `etat_partie` varchar(142) DEFAULT NULL,
  `histo_j1` text NOT NULL,
  `histo_j2` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `parties`
--

INSERT INTO `parties` (`id`, `nom`, `tour`, `trait`, `j1`, `j2`, `roques_j1`, `roques_j2`, `etat_du_jeu`, `etat_partie`, `histo_j1`, `histo_j2`) VALUES
(1, 'partie1', 0, 0, 'b', 'a', '', '', '', 'en cours', '', ''),
(2, 'camp_du_drap_dor', 0, 0, 'c', 'b', '', '', '', 'termine', '', ''),
(3, 'mapartie', 0, 0, 'c', '', '', '', '', 'proposition', '', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

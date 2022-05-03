-- phpMyAdmin SQL Dump
-- version 5.0.4deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : mysql.info.unicaen.fr:3306
-- Généré le : Dim 14 nov. 2021 à 14:46
-- Version du serveur :  10.5.11-MariaDB-1
-- Version de PHP : 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `21910887_bd`
--

-- --------------------------------------------------------

--
-- Structure de la table `cheese`
--

DROP TABLE IF EXISTS `cheese`;

CREATE TABLE `cheese` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `creator` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `cheese`
--

INSERT INTO `cheese` (`id`, `name`, `region`, `year`, `creator`, `image`) VALUES
(1, 'Camembert', 'Normandie', 1761, 'admin', '1.jpg'),
(2, 'Livarot', 'Normandie', 1850, 'admin', NULL),
(3, 'Brie', 'Seine et Marne', 999, 'admin', NULL),
(4, 'Emmetal', 'Suisse', 2021, 'admin', NULL),
(5, 'Saint-Nectaire', 'Saint-Nectaire', 2021, 'admin', NULL),
(6, 'Roquefort', 'Roquefort-Sur-Soulzon', 2022, 'admin', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `cheese`
--
ALTER TABLE `cheese`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `cheese`
--
ALTER TABLE `cheese`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

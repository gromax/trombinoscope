<?php
include "./config.php";
echo "Connexion à la base...";
include "./conx/connexion.php";
echo " Réussi !<br/>";

$createEventsTable = $connexion->prepare("CREATE TABLE IF NOT EXISTS `".$prefixeDB."evenements` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`NOM` text CHARACTER SET utf8mb4 NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");
$createParticipationsTable = $connexion->prepare("CREATE TABLE IF NOT EXISTS `".$prefixeDB."participations` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`IDP` int(11) NOT NULL,
	`IDE` int(11) NOT NULL,
	`T` text NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");
$createPersonnesTable = $connexion->prepare("CREATE TABLE IF NOT EXISTS `".$prefixeDB."personnes` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`NOM` text NOT NULL,
	`PRENOM` text NOT NULL,
	`VILLE` text NOT NULL,
	`HOBBY` text NOT NULL,
	`IDREGION` int(11) NOT NULL,
	`VL` tinyint(1) NOT NULL,
	`EP` tinyint(1) NOT NULL,
	`DIVERS` text NOT NULL,
	`SUG` tinyint(1) NOT NULL,
	`DATE` date NOT NULL,
	`HEURE` time NOT NULL,
	`PHOTO` text NOT NULL,
	`IDA` int(11) NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");

$createUsersTable = $connexion->prepare("CREATE TABLE IF NOT EXISTS `".$prefixeDB."users` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`PSEUDO` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`NOMPRENOM` text NOT NULL,
	`PWD` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`EMAIL` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`RANK` tinyint(4) NOT NULL,
	`DATE` date NOT NULL,
	`HEURE` time NOT NULL,
	PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;");

$insertSuperAdmin = $connexion->prepare("INSERT INTO `".$prefixeDB."users` (`ID`, `PSEUDO`, `PWD`, `RANK`) VALUES
(1, 'root', '".md5(PWD_SEED)."', ".RANG_SUPER_ADMIN.");");

try {
	echo "Création de la table users...";
	$createUsersTable->execute();
	echo "Réussi !<br/>";
	echo "Création de la table personnes...";
	$createPersonnesTable->execute();
	echo "Réussi !<br/>";
	echo "Création de la table evenements...";
	$createEventsTable->execute();
	echo "Réussi !<br/>";
	echo "Création de la table participations...";
	$createParticipationsTable->execute();
	echo "Réussi !<br/>";
	echo "Insertion de l'utilisateur root...";
	$insertSuperAdmin->execute();
	echo "Réussi !<br/>";
} catch( Exception $e ) {
	die ("Erreur : ".$e->getMessage());
}
die("Table créée !");
?>
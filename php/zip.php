<?php
session_start();
include "./config.php";

function strtoupperFr($string) {
	$string = strtoupper($string);
	$string = str_replace(
		array('é', 'è', 'ê', 'ë', 'à', 'â', 'î', 'ï', 'ô', 'ù', 'û', 'ç'),
		array('E', 'E', 'E', 'E', 'A', 'A', 'I', 'I', 'O', 'U', 'U', 'C'),
		$string
	);
	return $string;
}


if (isset($_SESSION['RANKtrombi'])) {
	$RANK=$_SESSION['RANKtrombi'];
} else {
	$RANK=0; echo "pwet";
}

if ($RANK<RANG_PRIVILEGED_USER) {
	die("Vous n'êtes pas autorisé à obtenir un zip");
} else {
	include "./conx/connexion.php";
	$selectPersonnes = $connexion->prepare('SELECT NOM, PRENOM, VILLE, HOBBY, PHOTO FROM '.$prefixeDB.'personnes WHERE PHOTO!="" AND SUG=0 ORDER BY NOM, PRENOM ASC;');
	
	try {
		$selectPersonnes->execute();
	} catch( Exception $e ){
		die('Erreur mySQL : '.$e->getMessage().'"})');
	}

	if (file_exists("../tmp/archive.zip")) unlink("../tmp/archive.zip");
	$zip = new ZipArchive();
	$zip->open('../tmp/archive.zip', ZipArchive::CREATE);
	while( $personne = $selectPersonnes->fetch(PDO::FETCH_ASSOC) ) {
		$zip->addFile("../img/".$personne['PHOTO'].".jpg");
		$zip->renameName("../img/".$personne['PHOTO'].".jpg", strtoupperFr($personne['NOM']."_".$personne['PRENOM']).".jpg");
	}
	$zip->close();
	echo "<a href='../tmp/archive.zip'>Cliquez pour obtenir le zip</a>";
}





?>
<?php
	// delPersonne.php
	// Suppression d'une personne dans la table personnes

	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');

	if (isset($_POST['id'])) $id=$_POST['id']; else $id=NULL;
	
	// Pour supprimer une personne il faut être de rang 7
	// Ou de rang 2 s'il s'agit d'une de ses propres suggestions
	if ( ($id!=NULL) && author("delPerson",array('ID'=>$id))){
		require_once('./conx/connexion.php');

		// Préparation des requêtes
		$selectPersonne = $connexion->prepare('SELECT PHOTO FROM '.$prefixeDB.'personnes WHERE ID=:id ;'); // Récupération du nom de la photo pour effacement
		$deleteParticipations = $connexion->prepare('DELETE FROM '.$prefixeDB.'participations WHERE IDP=:idp;');
		$deletePersonne = $connexion->prepare('DELETE FROM '.$prefixeDB.'personnes WHERE ID=:idp;');

		try {
			// envoie des requêtes
			
			// Suppression de l'image
			$selectPersonne->execute(array('id'=>$id ));
			while( $personne = $selectPersonne->fetch(PDO::FETCH_ASSOC) ) {
				if( file_exists("../img/".$personne['PHOTO'].".jpg")) unlink("../img/".$personne['PHOTO'].".jpg") ;
			}
			$selectPersonne->closeCursor();
			$deleteParticipations->execute(array('idp'=>$id));
			$deletePersonne->execute(array('idp'=>$id));
		} catch( Exception $e ){
			die('({state:"failed",error:"Del personne : '.$e->getMessage().'"})');
		}
		die ('({state:"success"})');
	}
	die('({state:"failed",error:"your rank is too low"})');	
?>
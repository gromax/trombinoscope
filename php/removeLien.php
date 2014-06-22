<?php
	// Vérification de l'état de connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	// Il faut un rang d'au moins 7 pour modifier la base
	if ($_SESSION['RANKtrombi']>=7){
		// Il faut spécifier une personne et un évènement à délier
		if(isset($_POST['IDP']) && isset($_POST['IDE'])) {
			require_once('./conx/connexion.php');
			$IDP=$_POST['IDP'];
			$IDE=$_POST['IDE'];

			// Préparation de la requète
			$delete = $connexion->prepare('DELETE FROM participations WHERE IDP=:idp AND IDE=:ide;');
			try {
				// envoie de la requète
				$delete->execute(array('idp'=>$IDP,'ide'=>$IDE));
				die ('({state:"success"})');
			} catch( Exception $e ){
				die('({state:"failed",error:"Link remove : '.$e->getMessage().'"})');
			}
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');
	
?>
<?php
	// Vérification de l'état de connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	// Il faut spécifier une personne et un évènement à délier
	if(isset($_POST['IDP']) && isset($_POST['IDE'])) {
		$IDP=$_POST['IDP'];
		$IDE=$_POST['IDE'];
		// Il faut un rang d'au moins 7 pour modifier la base
		// Ou user propriétaire de la personne
		// Ou anonyme en cours de session
		if (author("removeLink",array('IDP'=>$IDP))){
			require_once('./conx/connexion.php');

			// Dans le cas d'une modification par user ou waiting_user, on veut vérifier
			// que le compte est bien propriétaire de l'item à modifier
			if ((RANK==RANG_WAITING_USER)||(RANK==RANG_USER)) {
				$select=$connexion->prepare('SELECT COUNT(*) FROM '.$prefixeDB.'personnes WHERE ID=:id AND IDA=:idA;');
				$select->execute(array('id'=>$IDP, 'idA'=>$_SESSION['IDtrombi']));
				if ($select->fetchColumn()=='0') die('({state:"failed",error:"You are not owner of this item"})');
			}

			// Préparation de la requète
			$delete = $connexion->prepare('DELETE FROM '.$prefixeDB.'participations WHERE IDP=:idp AND IDE=:ide;');
			try {
				// envoie de la requète
				$delete->execute(array('idp'=>$IDP,'ide'=>$IDE));
				die ('({state:"success"})');
			} catch( Exception $e ){
				die('({state:"failed",error:"Link remove : '.$e->getMessage().'"})');
			}
		}
		die('({state:"failed",error:"your rank is too low"})');
	}
	die('({state:"failed",error:"missing parameters"})');
	
?>
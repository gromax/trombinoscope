<?php
	// addLien.php
	// Ajout d'un lien entre un évènement et une personne
	
	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	// Vérification de la présence de l'id personne et l'id évènement
	if(isset($_POST['IDP']) && isset($_POST['IDE'])) {
		$IDP=$_POST['IDP'];
		$IDE=$_POST['IDE'];
		// Droit de modifier la table à partir du rang 7 ou pour un user propriétaire, ou pour un contributeur en cours de session
		if (author("addLink",array('IDP'=>$IDP))){
			require_once('./conx/connexion.php');

			// Dans le cas d'une modification par user ou waiting_user, on veut vérifier
			// que le compte est bien propriétaire de l'item à modifier
			if ((RANK==RANG_WAITING_USER)||(RANK==RANG_USER)||(RANK==RANG_PRIVILEGED_USER)) {
				$select=$connexion->prepare('SELECT COUNT(*) FROM '.$prefixeDB.'personnes WHERE ID=:id AND IDA=:idA;');
				$select->execute(array('id'=>$IDP, 'idA'=>$_SESSION['IDtrombi']));
				if ($select->fetchColumn()=='0') die('({state:"failed",error:"You are not owner of this item"})');
			}

			// Préparation de la requète
			$insertPrepa = $connexion->prepare('INSERT INTO '.$prefixeDB.'participations (IDP, IDE) VALUES (:idp , :ide);');
			try {
				// envoie de la requète
				$insert = $insertPrepa->execute(array('idp'=>$IDP,'ide'=>$IDE));
				die ('({state:"success"})');
			} catch( Exception $e ){
				die('({state:"failed",error:"Link add : '.$e->getMessage().'"})');
			}
		}
		die('({state:"failed",error:"your rank is too low"})');
	}
	die('({state:"failed",error:"missing parameters"})');
	
?>
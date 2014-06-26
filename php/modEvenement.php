<?php
	// modEvenement.php
	// Ajout ou suppression d'un évènement
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	if (author("modEvent",null)){
		if(isset($_POST['nom'])) {
			require_once('./conx/connexion.php');
			$id=$_POST['id'];
			$nom=$_POST['nom'];
			
			if ($id>0) {
				$query=$connexion->prepare('UPDATE '.$prefixeDB.'evenements SET NOM=:nom WHERE ID=:id;');
				try {
					$query->execute(array('nom'=>$nom, 'id'=>$id));
				} catch( Exception $e ){
					die('({state:"failed",error:"mod event : '.$e->getMessage().'"})');
				}
				die ('({state:"success"})');
			} else {
				$query=$connexion->prepare('INSERT INTO '.$prefixeDB.'evenements (NOM) VALUES (:nom);');
				try {
					$query->execute(array('nom'=>$nom));
					$id=$connexion->lastInsertId();
				} catch( Exception $e ){
					die('({state:"failed",error:"add event : '.$e->getMessage().'"})');
				}
				die ('({state:"success", insertedID:'.$id.'})');
			}
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');
?>
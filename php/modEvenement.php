<?php
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	if ($_SESSION['RANKtrombi']>=7){
		if(isset($_POST['nom'])) {
			require_once('./conx/connexion.php');
			$id=$_POST['id'];
			$nom=$_POST['nom'];
			
			if ($id>0) {
				$query=$connexion->prepare('UPDATE '.$prefixeDB.'evenements SET NOM=:nom WHERE ID=:id;');
				try {
					$query->execute(array('nom'=>$nom, 'id'=>$id));
					$id=$connexion->lastInsertId();
				} catch( Exception $e ){
					die('({state:"failed",error:"mod personne : '.$e->getMessage().'"})');
				}
				die ('({state:"success", insertedID:'.$id.'})');
			} else {
				$query=$connexion->prepare('INSERT INTO '.$prefixeDB.'evenements (NOM) VALUES (:nom);');
				try {
					$query->execute(array('nom'=>$nom, 'id'=>$id));
				} catch( Exception $e ){
					die('({state:"failed",error:"add personne : '.$e->getMessage().'"})');
				}
				die ('({state:"success"})');
			}
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');
?>
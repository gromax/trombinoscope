<?php
	// addModUser.php
	// Ajout ou suppression d'un utilisateur
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	if (author("addModUser",null)){
		if(isset($_POST['pseudo']) && isset($_POST['pwd']) && isset($_POST['id'])) {
			require_once('./conx/connexion.php');
			$id=$_POST['id'];
			$pseudo=$_POST['pseudo'];
			$pwd=$_POST['pwd'];
			if (isset($_POST['rank'])) $rank=$_POST['rank']; else $rank=RANG_ANONYME_CONTRIBUTOR;
			
			if ($id>0) {
				$query=$connexion->prepare('UPDATE '.$prefixeDB.'users SET PSEUDO=:pseudo, PWD=:pwd, RANK=:rank WHERE ID=:id;');
				try {
					$query->execute(array('pseudo'=>$pseudo, 'pwd'=>$pwd, 'rank'=>$rank, 'id'=>$id));
				} catch( Exception $e ){
					die('({state:"failed",error:"mod user : '.$e->getMessage().'"})');
				}
				die ('({state:"success"})');
			} else {
				$query=$connexion->prepare('INSERT INTO '.$prefixeDB.'users (PSEUDO, PWD, RANK) VALUES (:pseudo, :pwd, :rank);');
				try {
					$query->execute(array('pseudo'=>$pseudo, 'rank'=>$rank, 'pwd'=>$pwd));
					$id=$connexion->lastInsertId();
				} catch( Exception $e ){
					die('({state:"failed",error:"add user : '.$e->getMessage().'"})');
				}
				die ('({state:"success", insertedID:'.$id.'})');
			}
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');
?>
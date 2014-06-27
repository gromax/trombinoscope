<?php
	// addModUser.php
	// Ajout ou suppression d'un utilisateur
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	if(isset($_POST['pseudo']) && isset($_POST['email']) && isset($_POST['pwd']) && isset($_POST['id'])) {
		$id=$_POST['id'];
		$pseudo=$_POST['pseudo'];
		$email=$_POST['email'];
		$pwd=$_POST['pwd'];

		if (author("addModUser",array('ID'=>$id))){
			require_once('./conx/connexion.php');
			
			if ((RANK>=RANG_ADMIN) && isset($_POST['rank']) ){
				$rank=$_POST['rank'];
			} else {
 				$rank=RANG_WAITING_USER;
			}

			if ($id>0) {
				// En modification, si le pwd correspond à un mdp vide, alors il n'est pas changé
				$params=array('pseudo'=>$pseudo, 'email'=>$email, 'rank'=>$rank, 'id'=>$id);
				if ($pwd==md5(PWD_SEED)) {
					$query=$connexion->prepare('UPDATE '.$prefixeDB.'users SET PSEUDO=:pseudo, EMAIL=:email, RANK=:rank WHERE ID=:id;');
				} else {
					$params['pwd']=$pwd;
					$query=$connexion->prepare('UPDATE '.$prefixeDB.'users SET PSEUDO=:pseudo, EMAIL=:email, PWD=:pwd, RANK=:rank WHERE ID=:id;');
				}

				try {
					$query->execute($params);
				} catch( Exception $e ){
					die('({state:"failed",error:"mod user : '.$e->getMessage().'"})');
				}
				die ('({state:"success"})');
			} else {
				$query=$connexion->prepare('INSERT INTO '.$prefixeDB.'users (PSEUDO, EMAIL, PWD, RANK) VALUES (:pseudo, :email, :pwd, :rank);');
				try {
					$query->execute(array('pseudo'=>$pseudo, 'email'=>$email, 'rank'=>$rank, 'pwd'=>$pwd));
					$id=$connexion->lastInsertId();
				} catch( Exception $e ){
					die('({state:"failed",error:"add user : '.$e->getMessage().'"})');
				}
				die ('({state:"success", insertedID:'.$id.'})');
			}
		}
		die('({state:"failed",error:"your rank is too low"})');
	}
	die('({state:"failed",error:"missing parameters"})');
?>
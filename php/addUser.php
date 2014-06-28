<?php
	// addUser.php
	// Ajout d'un utilisateur
	include './authcheck.php';
	
	// Pas besoin d'être connecté pour continuer
	
	// Vérifier que les champs requis sont bien fournis
	if (!( isset($_POST['pseudo']) && isset($_POST['pwd']) && isset($_POST['email']) && isset($_POST['nomPrenom']) ) ) {
		die('({state:"failed",error:"Échec de la création de compte.",debug:"(1)"})');
	}

	$pseudo=$_POST['pseudo'];
	$email=$_POST['email'];
	$pwd=$_POST['pwd'];
	$nomPrenom=$_POST['nomPrenom'];

	// Vérification de l'autorisation
	if (RANK<RANG_ADMIN) {
		// Pour pouvoir continuer, il faut donner le mdp de création de compte
		if (! (isset($_POST['key']) && ( MD5(PWD_SEED.CREATE_USER_KEY) == $_POST['key'] ) ) ) {
			die('({state:"failed",error:"Le clé de création est invalide."})');
		}
	}

	require_once('./conx/connexion.php');

	// Vérification du PSEUDO
	if (strlen($pseudo)<PSEUDO_MIN_LENGTH) { die('({state:"failed",error:"Pseudo trop court ('.PSEUDO_MIN_LENGTH.' min)"})'); }

	$pseudoExist=$connexion->prepare('SELECT COUNT(*) FROM '.$prefixeDB.'users WHERE PSEUDO=:pseudo;');
	$pseudoExist->execute(array('pseudo'=>$pseudo));
	if (intval($pseudoExist->fetchColumn())>0) die('({state:"failed",error:"Ce pseudo est déjà utilisé."})');

	// Remarque : Le mdp étant crypté, sa vérification doit être fait en amont de la requête

	if ((RANK>=RANG_ADMIN) && isset($_POST['rank']) ){
		$rank=$_POST['rank'];
	} else {
 		$rank=RANG_WAITING_USER;
	}

	// Ajout de l'utilisateur
	$addUser=$connexion->prepare('INSERT INTO '.$prefixeDB.'users (PSEUDO, EMAIL, PWD, NOMPRENOM, RANK) VALUES (:pseudo, :email, :pwd, :nomPrenom, :rank);');
	try {
		$addUser->execute(array('pseudo'=>$pseudo, 'email'=>$email, 'rank'=>$rank, 'nomPrenom'=>$nomPrenom, 'pwd'=>$pwd));
		$id=$connexion->lastInsertId();
	} catch( Exception $e ){
		die('({state:"failed",error:"Échec de la création de compte.", DB:"'.$e->getMessage().'"})');
	}
	die ('({state:"success", insertedID:'.$id.'})');
?>
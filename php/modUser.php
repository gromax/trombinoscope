<?php
	// addUser.php
	// Ajout d'un utilisateur
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	// Vérifier que les champs requis sont bien fournis
	if (!isset($_POST['idU'])) {
		die('({state:"failed",error:"Échec de la modification de compte."})');
	}
	$id=$_POST['idU'];


	require_once('./conx/connexion.php');

	// Vérification de l'autorisation
	if ((RANK==RANG_USER)||(RANK==RANG_WAITING_USER)||(RANK==RANG_PRIVILEGED_USER)) {
		if ($id!=$_SESSION['IDtrombi']) die('({state:"failed",error:"Vous ne pouvez modifier que votre compte."})');
	} elseif(RANK<RANG_ADMIN) {
		die('({state:"failed",error:"Vous n&apos;êtes pas autorisé à faire ce changement."})');
	}

	$modif=array();
	$params=array('id'=>$id);

	// Seul un admin peut changer un pseudo
	if ((RANK>=RANG_ADMIN) && isset($_POST['pseudo'])) {
		$pseudo=$_POST['pseudo'];
		// Vérification du PSEUDO
		if (strlen($pseudo)<PSEUDO_MIN_LENGTH) { die('({state:"failed",error:"Pseudo trop court ('.PSEUDO_MIN_LENGTH.' min)"})'); }

		$pseudoExist=$connexion->prepare('SELECT COUNT(*) FROM '.$prefixeDB.'users WHERE PSEUDO=:pseudo AND ID!=:id;');
		$pseudoExist->execute(array('pseudo'=>$pseudo, 'id'=>$id));
		if (intval($pseudoExist->fetchColumn())>0) die('({state:"failed",error:"Ce pseudo est déjà utilisé."})');

		array_push($modif, "PSEUDO=:pseudo");
		$params['pseudo']=$pseudo;
	}

	// Remarque : Le mdp étant crypté, sa vérification doit être fait en amont de la requête

	if (isset($_POST['pwd'])) {
		array_push($modif, "PWD=:pwd");
		$params['pwd']=$_POST['pwd'];
	}

	if (isset($_POST['email'])) {
		array_push($modif, "EMAIL=:email");
		$params['email']=$_POST['email'];
	}

	if (isset($_POST['nomPrenom'])) {
		array_push($modif, "NOMPRENOM=:nomPrenom");
		$params['nomPrenom']=$_POST['nomPrenom'];
	}

	// Un administrateur peut promouvoir à un rang inférieur au sien
	if ((RANK>=RANG_ADMIN) && isset($_POST['rank']) ) {
		$newRank=$_POST['rank'];
		if (RANK>$newRank) {
			array_push($modif, "RANK=:rank");
			$params['rank']=$newRank;
		}
	}	

	if (count($modif)>0) {
		$modUser=$connexion->prepare('UPDATE '.$prefixeDB.'users SET '.join(",",$modif).' WHERE ID=:id;');
		try {
			$modUser->execute($params);
		} catch( Exception $e ){
			die('({state:"failed",error:"Échec de la modification de compte.",DB:"'.$e->getMessage().'"})');
		}

		// S'il s'agissait de l'utilisateur courant, il faut changer les paramètres de session
		if ($id==$_SESSION['IDtrombi']) {
			if(isset($params['nomPrenom'])) $_SESSION['NOMPRENOMtrombi']=$params['nomPrenom'];
			if(isset($params['email'])) $_SESSION['EMAILtrombi']=$params['email'];
			if(isset($params['pseudo'])) $_SESSION['PSEUDOtrombi']=$params['pseudo'];
		}

		die ('({state:"success"})');
	}
	die('({state:"failed",error:"Rien à modifier !"})');
?>
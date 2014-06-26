<?php
	// getUsersList.php
	// Renvoie la liste des utilisateurs
	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');

	// Il faut être administrateur
	if (author("getUsersList",null)){
		require_once('./conx/connexion.php');
		$select = $connexion->prepare('SELECT ID, PSEUDO, RANK FROM '.$prefixeDB.'users ORDER BY RANK DESC, PSEUDO ASC;');
		$jsonData='';
		$select->execute();
		while( $user = $select->fetch(PDO::FETCH_ASSOC) ) { // on récupère la liste des membres
			if ($jsonData!='') $jsonData=$jsonData.',';
			$jsonData=$jsonData.json_encode($user, JSON_FORCE_OBJECT);
		}
		$select->closeCursor(); // on ferme le curseur des résultats
		die ('({state:"success",liste:['.$jsonData.']})');
	}
	die('({state:"failed",error:"your rank is too low"})');	
?>
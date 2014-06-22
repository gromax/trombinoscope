<?php
	// personneAvecPhoto.php
	// Renvoie la liste des personnes avec une photo
	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');

	// Il faut être de rang 2 ou plus
	if ($_SESSION['RANKtrombi']>=2){
		require_once('./conx/connexion.php');
		$select = $connexion->prepare('SELECT ID, NOM, PRENOM, VILLE, DATE FROM personnes WHERE SUG=0 AND PHOTO !="" ORDER BY NOM,PRENOM ASC;');
		$jsonData='';
		$select->execute();
		while( $personne = $select->fetch(PDO::FETCH_ASSOC) ) { // on récupère la liste des membres
			if ($jsonData!='') $jsonData=$jsonData.',';
			$jsonData=$jsonData.json_encode($personne, JSON_FORCE_OBJECT);
		}
		$select->closeCursor(); // on ferme le curseur des résultats
		die ('({state:"success",liste:['.$jsonData.']})');
	}
	die('({state:"failed",error:"your rank is too low"})');	
?>
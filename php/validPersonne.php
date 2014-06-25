<?php
	// modPersonne.php
	// Modification d'une personne dans la table personnes
	
	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');

	// Avec le rang 7 on peut modifier n'importe qui
	if (author("validNewPerson",null)){
		if (isset($_POST['ID'])) {
			$id=$_POST['ID'];
			require_once('./conx/connexion.php');
			$validPersonne = $connexion->prepare('UPDATE '.$prefixeDB.'personnes SET SUG=0 WHERE ID=:id');
			try {
				$validPersonne->execute(array('id'=>$id));
			} catch( Exception $e ){
				die('({state:"failed",error:"valid personne : '.$e->getMessage().'"})');
			}
			die ('({state:"success"})');
		}
		die('({state:"failed",error:"missing parameters"})');	
	}
	die('({state:"failed",error:"your rank is too low"})');
?>
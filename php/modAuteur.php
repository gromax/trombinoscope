<?php
	// modEvenement.php
	// Ajout ou suppression d'un évènement
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	if (RANK>=RANG_ADMIN){
		if(isset($_POST['idP']) && isset($_POST['idNA'])) {
			require_once('./conx/connexion.php');
			$idP=$_POST['idP'];
			$idNA=$_POST['idNA'];
			
			$modAuteur=$connexion->prepare('UPDATE '.$prefixeDB.'personnes SET IDA=:idNA WHERE ID=:idP;');
			try {
				$modAuteur->execute(array('idNA'=>$idNA, 'idP'=>$idP));
			} catch( Exception $e ){
				die('({state:"failed",error:"mod event : '.$e->getMessage().'"})');
			}
			die ('({state:"success"})');
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');
?>
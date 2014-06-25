<?php
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	if (author("delEvent",null)){
		if(isset($_POST['idE'])) {
			require_once('./conx/connexion.php');
			$idE=$_POST['idE'];
			
			$deleteParticipations = $connexion->prepare('DELETE FROM '.$prefixeDB.'participations WHERE IDE=:ide;');
			$deleteEvenement = $connexion->prepare('DELETE FROM '.$prefixeDB.'evenements WHERE ID=:ide;');

			try {
				$deleteParticipations->execute(array('ide'=>$idE));
				$deleteEvenement->execute(array('ide'=>$idE));
			} catch( Exception $e ) {
				die('({state:"failed",error:"Del evenement : '.$e->getMessage().'"})');
			}
			die ('({state:"success"})');
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');	
?>
<?php
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	if ($_SESSION['RANKtrombi']>=6){
		if(isset($_POST['pwd'])) {
			require_once('./conx/connexion.php');
			$id=$_SESSION['IDtrombi'];
			$pwd=$_POST['pwd'];
			$modif = $connexion->prepare('UPDATE '.$prefixeDB.'users SET PWD=:pwd WHERE ID=:id;');
			try {
				$modif->execute(array('pwd'=>$pwd, 'id'=>$id));
			} catch( Exception $e ){
				die('({state:"failed",error:"mod compte perso : '.$e->getMessage().'"})');
			}			
			die ('({state:"success"})');
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');
	
?>
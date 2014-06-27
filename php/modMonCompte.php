<?php
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	if (author("modMyAccount",null)){
		if(isset($_POST['pwd']) && isset($_POST['email'])) {
			require_once('./conx/connexion.php');
			$id=$_SESSION['IDtrombi'];
			$pwd=$_POST['pwd'];
			$email=$_POST['email'];
			$modif = $connexion->prepare('UPDATE '.$prefixeDB.'users SET PWD=:pwd, EMAIL=:email WHERE ID=:id;');
			try {
				$modif->execute(array('pwd'=>$pwd, 'id'=>$id, 'email'=>$email));
			} catch( Exception $e ){
				die('({state:"failed",error:"mod compte perso : '.$e->getMessage().'"})');
			}			
			die ('({state:"success"})');
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');
	
?>
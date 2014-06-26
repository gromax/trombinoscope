<?php
	// delUser.php
	// Suppression d'un utilisateur
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	if (author("delUser",null)){
		if(isset($_POST['id'])) {
			require_once('./conx/connexion.php');
			$id=$_POST['id'];
			
			$deleteUser = $connexion->prepare('DELETE FROM '.$prefixeDB.'users WHERE ID=:id;');

			try {
				$deleteUser->execute(array('id'=>$id));
			} catch( Exception $e ) {
				die('({state:"failed",error:"Del user : '.$e->getMessage().'"})');
			}
			die ('({state:"success"})');
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');	
?>
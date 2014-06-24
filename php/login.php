<?php
	session_start();

	if((isset($_POST['pwd'])) && (isset($_POST['login']))) {
		require_once('./conx/connexion.php');
		$select = $connexion->prepare('SELECT ID, PSEUDO, RANK FROM '.$prefixeDB.'users WHERE PWD=:pwd AND PSEUDO=:login;');
		$select->execute(array('pwd'=>$_POST['pwd'],'login'=>$_POST['login']));
		if ($personne = $select->fetch(PDO::FETCH_ASSOC)){
			$_SESSION['IDtrombi']=$personne['ID'];
			$_SESSION['PSEUDOtrombi']=$personne['PSEUDO'];
			$_SESSION['RANKtrombi']=$personne['RANK'];
			$_SESSION['last_access']=time();
			$_SESSION['ipaddr']=$_SERVER['REMOTE_ADDR'];
			$_SESSION['mySugs']=array();
			die('('.json_encode($personne, JSON_FORCE_OBJECT).')'); 
		}
	}
	die('({"ID":-1,"PSEUDO":"","RANK":0})'); 
?>
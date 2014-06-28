<?php
	session_start();

	if((isset($_POST['pwd'])) && (isset($_POST['login']))) {
		require_once('./conx/connexion.php');
		$select = $connexion->prepare('SELECT ID, PSEUDO, NOMPRENOM, EMAIL, RANK FROM '.$prefixeDB.'users WHERE PWD=:pwd AND PSEUDO=:login;');
		$select->execute(array('pwd'=>$_POST['pwd'],'login'=>$_POST['login']));
		if ($personne = $select->fetch(PDO::FETCH_ASSOC)){
			
			$_SESSION['IDtrombi']=$personne['ID'];
			$_SESSION['PSEUDOtrombi']=$personne['PSEUDO'];
			$_SESSION['EMAILtrombi']=$personne['EMAIL'];
			$_SESSION['NOMPRENOMtrombi']=$personne['NOMPRENOM'];
			$_SESSION['RANKtrombi']=$personne['RANK'];
			$_SESSION['last_access']=time();
			$_SESSION['ipaddr']=$_SERVER['REMOTE_ADDR'];
			$_SESSION['mySugs']=array();

			// On envoie une requête pour indiquer l'heure de la dernière connexion
			$lastConnect = $connexion->prepare('UPDATE '.$prefixeDB.'users SET DATE=:date, HEURE=:heure WHERE ID=:id;');
			$lastConnect->execute(array('id'=>$_SESSION['IDtrombi'], 'date'=>date('Y-m-d'), 'heure'=>date('H:i:s')));

			die('('.json_encode($personne, JSON_FORCE_OBJECT).')'); 
		}
	}
	die('({"ID":NULL,"PSEUDO":"","RANK":0})'); 
?>
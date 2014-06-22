﻿<?php
	// addPersonne.php
	// ajout d'une personne dans la table personnes
	
	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');
	
	// Avec le rang 7 on ajoute une personne avec un statut normal (SUG=0)
	// Avec le rang 2 on ajoute une personne avec le statut sugestion (SUG=1)
	if ($_SESSION['RANKtrombi']>=2){
		// Il faut au minimum un nom et un prénom
		if(isset($_POST['NOM']) && isset($_POST['PRENOM'])){
			if (isset($_POST['VILLE'])) $ville=$_POST['VILLE']; else $ville='';
			if (isset($_POST['HOBBY'])) $hobby=$_POST['HOBBY']; else $hobby='';
			if (isset($_POST['IDREGION'])) $idr=$_POST['IDREGION']; else $idr=-1;
			if (isset($_POST['VL'])) $vl=$_POST['VL']; else $vl=0;
			if (isset($_POST['EP'])) $ep=$_POST['EP']; else $ep=0;
			if (isset($_POST['DIVERS'])) $divers=$_POST['DIVERS']; else $divers='';
			
			require_once('./conx/connexion.php');

			if ($_SESSION['RANKtrombi']>=7) $S=0;
			else $S=1;
			
			// Préparation de la reqête
			$addPersonnePrepa = $connexion->prepare('INSERT INTO personnes (NOM, PRENOM, VILLE, HOBBY, IDREGION, VL, EP, SUG, DATE, HEURE, IP, DIVERS) VALUES (:nom, :prenom, :ville, :hobby, :idR, :vl , :ep , :sug , :date, :heure, :ip, :div)'); 
			
			$params=array(
				'nom'=>$_POST['NOM'],
				'prenom'=>$_POST['PRENOM'],
				'ville'=>$ville,
				'hobby'=>$hobby,
				'idR'=>$idr,
				'vl'=>$vl,
				'ep'=>$ep,
				'div'=>$divers,
				'date'=>date('Y-m-d'),
				'heure'=>date('H:i:s'),
				'ip'=>$_SESSION['ipaddr'],
				'sug'=>$S
			);
			
			try {
				$addPersonne = $addPersonnePrepa->execute($params);
				$id=$connexion->lastInsertId();
				if ($S==1) $_SESSION['mySugs'][$id]=true;
				die ('({state:"success", insertedID:'.$id.'})');
			} catch( Exception $e ){
				die('({state:"failed",error:"add personne : '.$e->getMessage().'"})');
			}
		}
		die('({state:"failed",error:"missing parameters"})');
	}
	die('({state:"failed",error:"your rank is too low"})');	
?>
<?php
	include './authcheck.php';
	$user='user:{ID:-1, PSEUDO:""}';
	$personnes='personnes:[]';
	$evenements='evenements:[]';
	$liens='liens:[]';
	
	if (isset($_SESSION['IDtrombi'])) {
		$user='user:{ID:'.$_SESSION['IDtrombi'].', PSEUDO:"'.$_SESSION['PSEUDOtrombi'].'",RANK:'.$_SESSION['RANKtrombi'].'}';

		require_once('./conx/connexion.php');
		if ($_SESSION['RANKtrombi']>=2) { // On ne peut voir la liste de personnes qu'à partir du rang 5
			$wh="";
			if ($_SESSION['RANKtrombi']<7) {
				foreach($_SESSION['mySugs'] as $id=>$val) {
					if($wh!="") $wh=$wh.",";
					$wh=$wh.$id;
				}
			}
			if (($_SESSION['RANKtrombi']>=5) || ($wh!="")) {
				if ($_SESSION['RANKtrombi']>=7) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO FROM '.$prefixeDB.'personnes ORDER BY NOM, PRENOM ASC;');
				elseif ($_SESSION['RANKtrombi']==5) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, 1 AS VL, EP, 0 AS SUG, "" AS DIVERS, PHOTO  FROM '.$prefixeDB.'personnes WHERE VL=1 AND SUG=0 ORDER BY NOM, PRENOM ASC;');
				else $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, 1 AS SUG, DIVERS, PHOTO  FROM '.$prefixeDB.'personnes WHERE ID IN ('.$wh.') AND SUG=1;');
				$selectPersonnes->execute();
				$jsonData='';
				while( $personne = $selectPersonnes->fetch(PDO::FETCH_ASSOC) ) {
					if ($jsonData!='') $jsonData=$jsonData.',';
					$jsonData=$jsonData.json_encode($personne, JSON_FORCE_OBJECT);
				}		
				$personnes='personnes:['.$jsonData.']';
				$selectPersonnes->closeCursor();
			}
		}
		
		if ($_SESSION['RANKtrombi']>=5) {
			$selectEvents = $connexion->prepare('SELECT ID, NOM FROM '.$prefixeDB.'evenements;');
			$selectEvents->execute();
			$jsonData='';
			while( $ev = $selectEvents->fetch(PDO::FETCH_ASSOC) ) {
				if ($jsonData!='') $jsonData=$jsonData.',';
				$jsonData=$jsonData.json_encode($ev, JSON_FORCE_OBJECT);
			}		
			$evenements='evenements:['.$jsonData.']';
			$selectEvents->closeCursor();
			
			$selectParticipations = $connexion->prepare('SELECT ID, IDP, IDE FROM '.$prefixeDB.'participations');
			$selectParticipations->execute();
			$jsonData='';
			while( $link = $selectParticipations->fetch(PDO::FETCH_ASSOC) ) {
				if ($jsonData!='') $jsonData=$jsonData.',';
				$jsonData=$jsonData.json_encode($link, JSON_FORCE_OBJECT);
			}		
			$liens='liens:['.$jsonData.']';
			$selectParticipations->closeCursor();
		}
		
	}
	die('({'.$user.','.$personnes.','.$evenements.','.$liens.'})');
?>
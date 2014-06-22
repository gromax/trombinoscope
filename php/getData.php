<?php
	include './authcheck.php';
	$user='user:{ID:-1, PSEUDO:""}';
	$personnes='personnes:[]';
	$regions='regions:[]';
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
				if ($_SESSION['RANKtrombi']>=7) $selectPrepa1 = $connexion->prepare("SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO FROM personnes ORDER BY NOM, PRENOM ASC;");
				elseif ($_SESSION['RANKtrombi']==5) $selectPrepa1 = $connexion->prepare("SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, 1 AS VL, EP, 0 AS SUG, ' ' AS DIVERS, PHOTO  FROM personnes WHERE VL=1 AND SUG=0 ORDER BY NOM, PRENOM ASC;");
				else $selectPrepa1 = $connexion->prepare("SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, 1 AS SUG, DIVERS, PHOTO  FROM personnes WHERE ID IN (".$wh.") AND SUG=1;");
				$selectPrepa1->execute();
				$jsonData='';
				while( $personne = $selectPrepa1->fetch(PDO::FETCH_ASSOC) ) {
					if ($jsonData!='') $jsonData=$jsonData.',';
					$jsonData=$jsonData.json_encode($personne, JSON_FORCE_OBJECT);
				}		
				$personnes='personnes:['.$jsonData.']';
				$selectPrepa1->closeCursor();
			}
		}
		
		if ($_SESSION['RANKtrombi']>=5) {
			$selectPrepa2 = $connexion->prepare("SELECT ID, NOM FROM evenements;");
			$selectPrepa2->execute();
			$jsonData='';
			while( $ev = $selectPrepa2->fetch(PDO::FETCH_ASSOC) ) {
				if ($jsonData!='') $jsonData=$jsonData.',';
				$jsonData=$jsonData.json_encode($ev, JSON_FORCE_OBJECT);
			}		
			$evenements='evenements:['.$jsonData.']';
			$selectPrepa2->closeCursor();
			
			$selectPrepa3 = $connexion->prepare("SELECT ID, IDP, IDE FROM participations");
			$selectPrepa3->execute();
			$jsonData='';
			while( $link = $selectPrepa3->fetch(PDO::FETCH_ASSOC) ) {
				if ($jsonData!='') $jsonData=$jsonData.',';
				$jsonData=$jsonData.json_encode($link, JSON_FORCE_OBJECT);
			}		
			$liens='liens:['.$jsonData.']';
			$selectPrepa3->closeCursor();
		}
		
	}
	die('({'.$user.','.$personnes.','.$regions.','.$evenements.','.$liens.'})');
?>
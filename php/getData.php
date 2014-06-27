<?php
	include './authcheck.php';
	$user='user:{ID:-1, PSEUDO:""}';
	$personnes='personnes:[]';
	$evenements='evenements:[]';
	$liens='liens:[]';
	
	if (RANK>0) {
		$user='user:{ID:'.$_SESSION['IDtrombi'].', PSEUDO:"'.$_SESSION['PSEUDOtrombi'].'", EMAIL:"'.$_SESSION['EMAILtrombi'].'",RANK:'.$_SESSION['RANKtrombi'].'}';
		require_once('./conx/connexion.php');

		// Les admins voient tout
		// Les utilisateurs voient tout plus les éléments dont ils sont les auteurs
		// Les visiteurs voient toutes les propositions validées plus leur suggestion sur la session présente
		// Les contributeurs anonymes voient seulement les propositions de leur session présente

		// Clause Where pour les suggestions de la session courante
		$wh="";
		if ((RANK==RANG_VISITOR)||(RANK==RANG_ANONYME_CONTRIBUTOR)) {
			foreach($_SESSION['mySugs'] as $id=>$val) {
				if($wh!="") $wh=$wh.",";
				$wh=$wh.$id;
			}
		}

		$visibles=array(); // Enregistre les personnes visibles
		if ((RANK>=RANG_VISITOR) || (RANK==RANG_WAITING_USER) || ($wh!="")) {
			if (RANK>=RANG_ADMIN) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes ORDER BY NOM, PRENOM ASC;');
			elseif (RANK==RANG_USER) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE ( VL=1 AND SUG=0 ) OR IDA='.$_SESSION['IDtrombi'].' ORDER BY NOM, PRENOM ASC;');
			elseif (RANK==RANG_VISITOR) {
				if ($wh!="") {
					$selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, SUG, DIVERS, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE ( VL=1 AND SUG=0 ) OR ID IN ('.$wh.') ORDER BY NOM, PRENOM ASC;');
				} else {
					$selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, SUG, DIVERS, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE ( VL=1 AND SUG=0 ) ORDER BY NOM, PRENOM ASC;');
				}
			} elseif (RANK==RANG_WAITING_USER) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE IDA='.$_SESSION['IDtrombi'].' ORDER BY NOM, PRENOM ASC;');
			else $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, SUG, DIVERS, PHOTO, DATE, HEURE, IDA  FROM '.$prefixeDB.'personnes WHERE ID IN ('.$wh.');');
			

			$selectPersonnes->execute();
			$jsonData='';
			while( $personne = $selectPersonnes->fetch(PDO::FETCH_ASSOC) ) {
				$visibles[$personne['ID']]=true;
				if ($jsonData!='') $jsonData=$jsonData.',';
				$jsonData=$jsonData.json_encode($personne, JSON_FORCE_OBJECT);
			}		
			$personnes='personnes:['.$jsonData.']';
			$selectPersonnes->closeCursor();
		}
		
		$selectEvents = $connexion->prepare('SELECT ID, NOM FROM '.$prefixeDB.'evenements;');
		$selectEvents->execute();
		$jsonData='';
		while( $ev = $selectEvents->fetch(PDO::FETCH_ASSOC) ) {
			if ($jsonData!='') $jsonData=$jsonData.',';
			$jsonData=$jsonData.json_encode($ev, JSON_FORCE_OBJECT);
		}		
		$evenements='evenements:['.$jsonData.']';
		$selectEvents->closeCursor();
		
		if ((RANK>=RANG_VISITOR) || (RANK==RANG_WAITING_USER) || ($wh!="")) {
			$selectParticipations = $connexion->prepare('SELECT ID, IDP, IDE FROM '.$prefixeDB.'participations;');
			$selectParticipations->execute();
			$jsonData='';
			while( $link = $selectParticipations->fetch(PDO::FETCH_ASSOC) ) {
				if(isset($visibles[$link['IDP']])) {
					if ($jsonData!='') $jsonData=$jsonData.',';
					$jsonData=$jsonData.json_encode($link, JSON_FORCE_OBJECT);
				}
			}		
			$liens='liens:['.$jsonData.']';
			$selectParticipations->closeCursor();
		}
	}
	die('({'.$user.','.$personnes.','.$evenements.','.$liens.'})');
?>
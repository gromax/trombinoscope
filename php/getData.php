<?php
	include './authcheck.php';
	$myUser='user:{ID:NULL, PSEUDO:"", EMAIL:"", NOMPRENOM:""}';
	$personnes='personnes:[]';
	$evenements='evenements:[]';
	$liens='liens:[]';
	$users='';
	
	if (RANK>0) {
		$myUser='user:{ID:'.$_SESSION['IDtrombi'].', PSEUDO:"'.$_SESSION['PSEUDOtrombi'].'", NOMPRENOM:"'.$_SESSION['NOMPRENOMtrombi'].'", EMAIL:"'.$_SESSION['EMAILtrombi'].'",RANK:'.$_SESSION['RANKtrombi'].'}';
		require_once('./conx/connexion.php');

		// Les admins voient tout
		// Les utilisateurs voient tout plus les éléments dont ils sont les auteurs
		// Les visiteurs voient toutes les propositions validées

		$visibles=array(); // Enregistre les personnes visibles
		if (RANK>=RANG_ADMIN) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes ORDER BY NOM, PRENOM ASC;');
		elseif (RANK==RANG_PRIVILEGED_USER) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE SUG=0 OR IDA='.$_SESSION['IDtrombi'].' ORDER BY NOM, PRENOM ASC;');
		elseif (RANK==RANG_USER) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE ( VL=1 AND SUG=0 ) OR IDA='.$_SESSION['IDtrombi'].' ORDER BY NOM, PRENOM ASC;');
		elseif (RANK==RANG_VISITOR) $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, SUG, DIVERS, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE ( VL=1 AND SUG=0 ) ORDER BY NOM, PRENOM ASC;');
		else $selectPersonnes = $connexion->prepare('SELECT ID, IDREGION, NOM, PRENOM, VILLE, HOBBY, VL, EP, DIVERS, SUG, DATE, HEURE, PHOTO, IDA FROM '.$prefixeDB.'personnes WHERE IDA='.$_SESSION['IDtrombi'].' ORDER BY NOM, PRENOM ASC;');

		$selectPersonnes->execute();
		$json=array();
		while( $personne = $selectPersonnes->fetch(PDO::FETCH_ASSOC) ) {
			$visibles[$personne['ID']]=true;
			array_push($json,json_encode($personne, JSON_FORCE_OBJECT));
		}		
		$personnes='personnes:['.join(',',$json).']';
		$selectPersonnes->closeCursor();
		
		$selectEvents = $connexion->prepare('SELECT ID, NOM FROM '.$prefixeDB.'evenements;');
		$selectEvents->execute();
		$json=array();
		while( $ev = $selectEvents->fetch(PDO::FETCH_ASSOC) ) {
			array_push($json,json_encode($ev, JSON_FORCE_OBJECT));
		}		
		$evenements='evenements:['.join(',',$json).']';
		$selectEvents->closeCursor();
		
		// Parcours de la table de jointure Personne / Evenement
		$selectParticipations = $connexion->prepare('SELECT ID, IDP, IDE FROM '.$prefixeDB.'participations;');
		$selectParticipations->execute();
		$json=array();
		while( $link = $selectParticipations->fetch(PDO::FETCH_ASSOC) ) {
			if(isset($visibles[$link['IDP']])) {
				array_push($json,json_encode($link, JSON_FORCE_OBJECT));
			}
		}		
		$liens='liens:['.join(',',$json).']';
		$selectParticipations->closeCursor();

		// Liste des utilisateurs pour un admin
		if (RANK>=RANG_ADMIN) {
			$selectUsers = $connexion->prepare('SELECT ID, PSEUDO, EMAIL, NOMPRENOM, DATE, HEURE, RANK FROM '.$prefixeDB.'users ORDER BY RANK DESC, PSEUDO ASC;');
			$json=array();
			$selectUsers->execute();
			while( $user = $selectUsers->fetch(PDO::FETCH_ASSOC) ) { // on récupère la liste des membres
				array_push($json,json_encode($user, JSON_FORCE_OBJECT));
			}
			$selectUsers->closeCursor(); // on ferme le curseur des résultats
			$users=', users:['.join(',',$json).']';
		}


	}
	die('({'.$myUser.','.$personnes.','.$evenements.','.$liens.$users.'})');
?>
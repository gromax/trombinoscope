<?php
	// modPersonne.php
	// Modification d'une personne dans la table personnes
	
	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');

	// Avec le rang 7 on peut modifier n'importe qui
	// Avec le rang 2 on peut modifier une de ses suggestions
	if (isset($_POST['ID'])) {
		$id=$_POST['ID'];
		if ( ($_SESSION['RANKtrombi']>=7) || ( ($_SESSION['RANKtrombi']>=2) && isset($_SESSION['mySugs'][$id]) ) ){
		
			// Il faut au moins une vérification, sans quoi la modification est inutile
			$requete='';
			
			$params=array(
				'id'=>$id,
				'date'=>date('Y-m-d'),
				'heure'=>date('H:i:s'),
				'ip'=>$_SESSION['ipaddr']
			);
			
			if(isset($_POST['NOM'])) {
				$requete=$requete." NOM=:nom,";
				$params['nom']=$_POST['NOM'];
			}
			if(isset($_POST['PRENOM'])) {
				$requete=$requete." PRENOM=:prenom,";
				$params['prenom']=$_POST['PRENOM'];
			}
			if(isset($_POST['IDREGION'])) {
				$requete=$requete." IDREGION=:idr,";
				$params['idr']=$_POST['IDREGION'];
			}
			if(isset($_POST['VL'])) {
				$requete=$requete." VL=:vl,";
				$params['vl']=$_POST['VL'];
			}
			if(isset($_POST['NOM'])) {
				$requete=$requete." NOM=:nom,";
				$params['nom']=$_POST['NOM'];
			}
			if(isset($_POST['EP'])) {
				$requete=$requete." EP=:ep,";
				$params['ep']=$_POST['EP'];
			}
			if(isset($_POST['HOBBY'])) {
				$requete=$requete." HOBBY=:hobby,";
				$params['hobby']=$_POST['HOBBY'];
			}
			if(isset($_POST['DIVERS'])) {
				$requete=$requete." DIVERS=:divers,";
				$params['divers']=$_POST['DIVERS'];
			}
			if ($requete!='') {
				// Il y a donc une modification à faire
				require_once('./conx/connexion.php');
				$modPersonne = $connexion->prepare('UPDATE personnes SET '.$requete.' DATE=:date, HEURE=:heure, IP=:ip WHERE ID=:id');
		
				try {
					$modPersonne->execute($params);
				} catch( Exception $e ){
					die('({state:"failed",error:"add/mod personne : '.$e->getMessage().'"})');
				}
			}
			die ('({state:"success"})');
		}
		die('({state:"failed",error:"your rank is too low"})');
	}
	die('({state:"failed",error:"missing parameters"})');	
?>
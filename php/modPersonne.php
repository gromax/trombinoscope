<?php
	// modPersonne.php
	// Modification d'une personne dans la table personnes
	
	// Vérification de la connexion
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');

	// Avec le rang 7 on peut modifier n'importe qui
	// Avec un rang user on peut modifier une des ses propres propositions
	// Avec le rang 2 on peut modifier une de ses suggestions
	if (isset($_POST['ID'])) {
		$id=$_POST['ID'];
		if (author("modPerson",array('ID'=>$id))){
			require_once('./conx/connexion.php');
			// Dans le cas d'une modification par user ou waiting_user, on veut vérifier
			// que le compte est bien propriétaire de l'item à modifier
			if ((RANK==RANG_WAITING_USER)||(RANK==RANG_USER)) {
				$select=$connexion->prepare('SELECT COUNT(*) FROM '.$prefixeDB.'personnes WHERE ID=:id AND IDA=:idA;');
				$select->execute(array('id'=>$id, 'idA'=>$_SESSION['IDtrombi']));
				if ($select->fetchColumn()=='0') die('({state:"failed",error:"You are not owner of this item"})');
			}
		
			// Il faut au moins une vérification, sans quoi la modification est inutile
			$requete='';
			
			$params=array(
				'id'=>$id,
				'date'=>date('Y-m-d'),
				'heure'=>date('H:i:s'),
			);
			
			if(isset($_POST['NOM'])) {
				$requete=$requete." NOM=:nom,";
				$params['nom']=$_POST['NOM'];
			}
			if(isset($_POST['PRENOM'])) {
				$requete=$requete." PRENOM=:prenom,";
				$params['prenom']=$_POST['PRENOM'];
			}
			if(isset($_POST['VILLE'])) {
				$requete=$requete." VILLE=:ville,";
				$params['ville']=$_POST['VILLE'];
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
				if ((RANK==RANG_USER)||(RANK==RANG_WAITING_USER)) {
					$params['ida']=$_SESSION['IDtrombi'];
					$modPersonne = $connexion->prepare('UPDATE '.$prefixeDB.'personnes SET '.$requete.' DATE=:date, HEURE=:heure WHERE ID=:id AND IDA=:ida');
				} else {
					$modPersonne = $connexion->prepare('UPDATE '.$prefixeDB.'personnes SET '.$requete.' DATE=:date, HEURE=:heure WHERE ID=:id');
				}
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
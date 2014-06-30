<?php
	$imgFolder='../img/';
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die('({state:"failed",error:"logOff"})');

	// Vérification de la présence de l'id
	if (!isset($_POST['id'])) die('({state:"failed",error:"id manquant"})');
	$idP=$_POST['id'];

	// Vérification de l'envoi des dimensions du nouveau cadre
	if(!(isset($_POST['x1']) && isset($_POST['y1']) && isset($_POST['x2']) && isset($_POST['y2'])))
		die('({state:"failed",error:"nouvelles dimensions manquantes"})');
	$x1=intval($_POST['x1']);
	$y1=intval($_POST['y1']);
	$x2=intval($_POST['x2']);
	$y2=intval($_POST['y2']);
	$largeur=$x2-$x1;
	$hauteur=$y2-$y1;

	// Vérification des droits
	if ((RANK>=RANG_ADMIN)||(RANK==RANG_WAITING_USER)||(RANK==RANG_USER)||(RANK==RANG_PRIVILEGED_USER)) {
		require_once('./conx/connexion.php');
		// Il faut charger la personne id pour vérifier la propriété et récupérer l'adresse de l'image
		$selectPersonne=$connexion->prepare('SELECT IDA, PHOTO FROM '.$prefixeDB.'personnes WHERE ID=:id;');
		$selectPersonne->execute(array('id'=>$idP));
		$idA=0;
		$photo='';

		if( $personne = $selectPersonne->fetch(PDO::FETCH_ASSOC) ) {
			$photo=$personne['PHOTO'];
			$idA=$personne['IDA'];
		}

		// S'il n'y a pas de photo, inutile d'aller plus loin
		if (($photo=='') || (!file_exists($imgFolder.$photo.'.jpg')) )  die('({state:"failed",error:"Photo absente"})');

		// Si simple utilisateur, il faut vérifier le droit
		if ((RANK<RANG_ADMIN)&&($idA!=$_SESSION['IDtrombi'])) die('({state:"failed",error:"Vous ne pouvez modifier que vos photos"})');

		// Recadrage de l'image
		$oldImg = ImageCreateFromJpeg($imgFolder . $photo.'.jpg')
			or die('({state:"failed",error:"Erreur lors de l\'ouverture de la photo"})');
		$newImg = imagecreatetruecolor($largeur, $hauteur);
		ImageCopyResampled($newImg, $oldImg, 0, 0, $x1, $y1, $largeur, $hauteur, $largeur, $hauteur); 
		imagejpeg($newImg,$imgFolder . $idP.'.jpg');
		$strMD5=md5_file($imgFolder . $idP.'.jpg').$idP;
		rename($imgFolder . $idP.'.jpg',$imgFolder.$strMD5.'.jpg');

		// Suppression de l'ancien fichier
		unlink($imgFolder.$photo.'.jpg');

		// Update du nom de fichier en bdd
		$updatePersonne=$connexion->prepare('UPDATE '.$prefixeDB.'personnes SET PHOTO=:photo WHERE ID=:id;');
		$updatePersonne->execute(array('photo'=>$strMD5, 'id'=>$idP));

		die('({state:"success", PHOTO:"'.$strMD5.'"})');
	}
	die('({state:"failed",error:"Modification non autorisée"})');






/*	function recopImage($imgSource, $ratio){
		$largeurSource = imagesx($imgSource);
		$hauteurSource = imagesy($imgSource);
		$ratioSource=$hauteurSource/$largeurSource;
		if ($ratioSource!=$ratio){
			if($ratioSource>$ratio) {
				$largeur=$hauteurSource/$ratio;
				$im = ImageCreateTrueColor ($largeur, $hauteurSource);
				ImageFilledRectangle ($im, 0, 0, $largeur, $hauteurSource,  ImageColorAllocate ($im, 255, 255, 255) );
				ImageCopyResampled($im, $imgSource, ($largeur-$largeurSource)/2, 0, 0, 0, $largeurSource, $hauteurSource, $largeurSource, $hauteurSource); 
			} else {
				$hauteur=$largeurSource*$ratio;
				$im = ImageCreateTrueColor ($largeurSource, $hauteur);
				ImageFilledRectangle ($im, 0, 0, $largeurSource, $hauteur,  ImageColorAllocate ($im, 255, 255, 255) );
				ImageCopyResampled($im, $imgSource, 0, ($hauteur-$hauteurSource)/2, 0, 0, $largeurSource, $hauteurSource, $largeurSource, $hauteurSource); 
			}
			return $im; 
		} else return $imgSource;
	}

	$scrB="<script> window.top.window.loadTrigger(eval";
	$scrE=");</script>";
	include './authcheck.php';
	if (!isset($_SESSION['IDtrombi'])) die($srcB.'({state:"failed",error:"logOff"})'.$scrE);
	if( isset($_POST['IDP']) && isset($_FILES['avatar']) )  {
		$id=$_POST['IDP'];
		if ( author("modPerson",array('ID'=>$id)) ){
			require_once('./conx/connexion.php');
			$selectPersonne = $connexion->prepare('SELECT PHOTO FROM '.$prefixeDB.'personnes WHERE ID=:id ;');
			$updatePersonne = $connexion->prepare('UPDATE '.$prefixeDB.'personnes SET PHOTO=:photo WHERE ID=:id ;');
			
			$extensions = array('.jpg','.jpeg');
			$extension = strtolower(strrchr($_FILES['avatar']['name'], '.'));
			$taille_maxi = 600000;
			$taille = filesize($_FILES['avatar']['tmp_name']);
			if(!in_array($extension, $extensions)) {
				die($scrB.'({state:"failed",error:"bad file type"})'.$scrE);
			} elseif($taille>$taille_maxi) {
				die($scrB.'({state:"failed",error:"Fichier trop gros"})'.$scrE);
			} else {
				$fichier = basename($_FILES['avatar']['name']);
				if(move_uploaded_file($_FILES['avatar']['tmp_name'], $imgFolder . $id.'.jpg')) {
					// Effacement de l'ancienne photo
					$selectPersonne->execute(array('id'=>$id ));
					while( $personne = $selectPersonne->fetch(PDO::FETCH_ASSOC) ) {
						if( file_exists("../img/".$personne['PHOTO'].".jpg")) unlink("../img/".$personne['PHOTO'].".jpg") ;
					}
					$selectPersonne->closeCursor();

					$imgS = ImageCreateFromJpeg($imgFolder . $id.'.jpg')
						or die ('<script> window.top.window.loadTrigger(eval({state:"failed", error:"'.$imgFile.'  Erreur lors de la création de l\'image"}));</script>');
					$imgD=recopImage($imgS, 1.333);
					//imagedestroy($imgS);
					imagejpeg($imgD,$imgFolder . $id.'.jpg');
					//imagedestroy($imgD);
					$sourceFile=$imgFolder.$id.'.jpg';
					$strMD5=md5_file($sourceFile).$id;
					rename($sourceFile,$imgFolder.$strMD5.'.jpg');

					$updatePersonne->execute(array('photo'=>$strMD5, 'id'=>$id ));
					die($scrB.'({state:"success",id:'.$id.',PHOTO:"'.$strMD5.'"})'.$scrE);
				} else {
					die($scrB.'({state:"failed",error:"Échec du chargement"})'.$scrE);
				}
			}
		}
		die($scrB.'({state:"failed",error:"Vous n\'êtes pas autorisé à effectuer cette modification"})'.$scrE);
	}
	die($scrB.'({state:"failed",error:"Paramètres manquants"})'.$scrE);
*/
?>
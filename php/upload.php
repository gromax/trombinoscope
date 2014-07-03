<?php
	$imgFolder='../img/';
	function recopImage($imgSource, $ratio){
		$largeurSource = imagesx($imgSource);
		$hauteurSource = imagesy($imgSource);
		$ratioSource=$largeurSource/$hauteurSource;
		if ($ratioSource!=$ratio){
			if($ratioSource<$ratio) {
				$largeur=$hauteurSource*$ratio;
				$im = ImageCreateTrueColor ($largeur, $hauteurSource);
				ImageFilledRectangle ($im, 0, 0, $largeur, $hauteurSource,  ImageColorAllocate ($im, 255, 255, 255) );
				ImageCopyResampled($im, $imgSource, ($largeur-$largeurSource)/2, 0, 0, 0, $largeurSource, $hauteurSource, $largeurSource, $hauteurSource); 
			} else {
				$hauteur=$largeurSource/$ratio;
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
					$imgD=recopImage($imgS, IMAGE_RATIO);
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

?>
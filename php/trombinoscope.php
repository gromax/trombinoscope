<?php
session_start();
include "./config.php";
require('../lib/fpdf/fpdf.php');

class PDF extends FPDF {

function Header(){
	// Police Arial gras 15
	$this->SetFont('Arial','B',15);
	// Titre encadré
	$strLimite = '';
	if (isset($_GET['nomE'])) $strLimite=' de '.utf8_decode($_GET['nomE']);
	if (isset($_GET['nomR'])) $strLimite=$strLimite.' - '.utf8_decode($_GET['nomR']);

	$this->Cell(LARGEUR_PAGE-2*MARGE_GD,ENTETE,'Trombinoscope'.$strLimite.' ('.$this->PageNo().'/{nb})',1,0,'C');    	

	$this->Ln();
}


function myTable($data) {
	$largeurColonne = (LARGEUR_PAGE - 2* MARGE_GD ) / TROMBI_IPL;
	$largeurImage = $largeurColonne - 2*MARGE_PHOTO;
	$hauteurImage = $largeurImage / IMAGE_RATIO;
	$hauteurLigneImage = $hauteurImage + 2*MARGE_PHOTO;
	$hauteurTexte = 6;
	
	$y=1000;
	foreach($data as $row) {
		if ($y>HAUTEUR_PAGE-MARGE_HB-$hauteurLigneImage-2*$hauteurTexte) {
			$this->AddPage();
			$y=MARGE_HB+ENTETE;
		}

		// On commence par placer les photos
		$x=MARGE_GD;
		foreach ($row as $item) {
			if ($item['PHOTO']!='') { $this->Image('../img/'.$item['PHOTO'].'.jpg',$x+MARGE_PHOTO,$y+MARGE_PHOTO,$largeurImage,$hauteurImage); }
			$x=$x+$largeurColonne;
		}
		
		// Cadre autour de l'image
		foreach ($row as $item) {
			$this->Cell($largeurColonne,$hauteurLigneImage,"","LTR"); 	
		}
		$this->Ln();

		// Ligne de noms
		$this->SetFont('Arial','',12);
		foreach ($row as $item) {
			$this->Cell($largeurColonne,$hauteurTexte,utf8_decode($item['PRENOM']),"LR", 0, C); 	
		}
		$this->Ln();

		// Ligne de noms
		foreach ($row as $item) {
			$this->Cell($largeurColonne,$hauteurTexte,utf8_decode($item['NOM']),"LBR", 0, C); 	
		}
		$this->Ln();
		$y=$this->GetY()+MARGE_PHOTO;
    }
}


}

if (isset($_SESSION['RANKtrombi'])) {
	$RANK=$_SESSION['RANKtrombi'];
} else {
	$RANK=0; echo "pwet";
}

if ($RANK<RANG_PRIVILEGED_USER) {
	die("Vous n'êtes pas autorisé à afficher cette page");
} else {
	$params=array();
	//$clauseWhere=array();
	$clauseWhere=array('PHOTO!=""', 'SUG=0');
	if (isset($_GET['fE'])) {
		array_push($clauseWhere,'IDE=:ide');
		$params['ide']=$_GET['fE'];
		if (isset($_GET['nomE'])) $nomE=$_GET['nomE']; else $nomE='Inconnu';
	}

	if (isset($_GET['fR'])) {
		array_push($clauseWhere,'IDREGION=:idr');
		$params['idr']=$_GET['fR'];
		if (isset($_GET['nomR'])) $nomE=$_GET['nomR']; else $nomE='Inconnue';
	}

	if (count($clauseWhere)>0) { $strWhere='WHERE '.join(' AND ',$clauseWhere); } else { $strWhere=''; }

	include "./conx/connexion.php";
	if (isset($_GET['fE'])) {
		$selectPersonnes = $connexion->prepare('SELECT NOM, PRENOM, VILLE, HOBBY, PHOTO FROM '.$prefixeDB.'personnes JOIN '.$prefixeDB.'participations ON '.$prefixeDB.'personnes.ID='.$prefixeDB.'participations.IDP '.$strWhere.' ORDER BY NOM, PRENOM ASC;');
	} else {
		$selectPersonnes = $connexion->prepare('SELECT NOM, PRENOM, VILLE, HOBBY, PHOTO FROM '.$prefixeDB.'personnes '.$strWhere.' ORDER BY NOM, PRENOM ASC;');
	}
	try {
		$selectPersonnes->execute($params);
	} catch( Exception $e ){
		die('Erreur mySQL : '.$e->getMessage().'"})');
	}
		
	$tableTrombinoscope=array();
	$ligneTrombinoscope=array();
	while( $personne = $selectPersonnes->fetch(PDO::FETCH_ASSOC) ) {
		array_push($ligneTrombinoscope, $personne);
		if (count($ligneTrombinoscope)==TROMBI_IPL) {
			array_push($tableTrombinoscope, $ligneTrombinoscope);
			$ligneTrombinoscope=array();
		}
	}
	if (count($ligneTrombinoscope)>0) array_push($tableTrombinoscope, $ligneTrombinoscope);

	$pdf = new PDF('P','mm','A4');
	$pdf->AliasNbPages();
	$pdf->myTable($tableTrombinoscope);
	$pdf->Output();
}








?>
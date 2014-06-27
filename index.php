<?php
	include './php/authcheck.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php echo '<?xml version="1.0"?>'; ?>

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Trombinoscope</title>
		<link media="screen" rel="stylesheet" href='./style.css' type="text/css"/>
		<link href="./lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"><!-- Autocomplete-->
		<script language='javascript' type='text/javascript'> //Définition des constantes
			var RANG_SUPER_ADMIN=<?php echo RANG_SUPER_ADMIN; ?>;
			var RANG_ADMIN=<?php echo RANG_ADMIN; ?>;
			var RANG_USER=<?php echo RANG_USER; ?>;
			var RANG_VISITOR=<?php echo RANG_VISITOR; ?>;
			var RANG_ANONYME_CONTRIBUTOR=<?php echo RANG_ANONYME_CONTRIBUTOR; ?>;
			var RANG_WAITING_USER=<?php echo RANG_WAITING_USER; ?>;
			var PWD_SEED='<?php echo PWD_SEED; ?>';
			var RANK=<?php echo RANK ?>;
			var ranks = [RANG_SUPER_ADMIN, RANG_ADMIN, RANG_USER, RANG_VISITOR, RANG_WAITING_USER, RANG_ANONYME_CONTRIBUTOR];
		</script>
		<script language='javascript' type='text/javascript' src='./js/moteur.js'></script>
		<script src="./lib/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> <!-- Autocomplete-->
		<script src="./lib/bootstrap/js/bootstrap.min.js"></script>
		<script src="./lib/bootstrap.file-input.js"></script>
		<script src="./lib/handlebars.js"></script>

		
<!-- Templates handlebars -->
	<?php include "./templates.php"; ?>
	</head>
	
	<body onload="init();">
		<nav class="navbar navbar-default" role="navigation">			
			<div class="container">
				<div class="navbar-header">
					<button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#" onclick="trombinoscopeButtonClick(); return false;">Trombinoscope</a>
				</div>
				<div class="collapse navbar-collapse">

				<!-- Menu zone de gauche -->
<?php if (RANK>0) { 
	if (RANK>=RANG_ADMIN) { ?>
					<ul class="nav navbar-nav">
						<li class="dropdown">
					  		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Personnes<b class="caret"></b></a>
					  		<ul class="dropdown-menu">
								<li><a href="#" onclick="afficherFormulaireModificationPersonne(-1);">Nouvelle</a></li>
								<li><a href="#" onclick="data.setFilter(true,null); data.applyFilter(true); affichage.setPageActive(null); affichage.liste();">Liste</a></li>
								<li class="divider"></li>
								<li><a href="#" onclick="data.setFilter(true,{filtreS:1}); data.applyFilter(true); affichage.setPageActive(null); affichage.liste();">Inscriptions</a></li>
								<li class="divider"></li>
								<li><a href="#" onclick="data.setFilter(true,null); data.applyFilter(true); affichage.setPageActive(null); affichage.trombinoscope()">Trombinoscope</a></li>
					  		</ul>
						</li>
						<li class="dropdown">
					  		<a href="#" class="dropdown-toggle" data-toggle="dropdown">Évènements<b class="caret"></b></a>
					  		<ul class="dropdown-menu">
								<li><a href="#" onclick="afficherFormModificationEvenement(-1);">Nouveau</a></li>
								<li><a href="#" onclick="afficherListeEvenements();">Liste</a></li>
					  		</ul>
						</li>
						<li><a href="#" onclick="affichage.listeUsers(); return false;">Users</a></li>
				  	</ul>
					 <form class="navbar-form navbar-left" role="search" onsubmit="data.filtrerSelonRecherche($('#inpSearch').val()); affichage.setPageActive(null); affichage.liste(); return false;">
						<div class="form-group">
							<input type="text" id="inpSearch" class="form-control" placeholder="Search">
						</div>
						<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
					 </form>
<?php	} elseif (RANK==RANG_ANONYME_CONTRIBUTOR) { ?>
					<ul class="nav navbar-nav">
						<li><a href="#" onclick="afficherFormulaireModificationPersonne(-1); return false;">Nouvelle photo</a></li>
						<li><a href="#" onclick="data.setFilter(true,null); data.applyFilter(true); affichage.setPageActive(null); affichage.liste();">Mes ajouts</a></li>
						<!--<li><a href="#" onclick="affichage.personnesAvecPhoto();">Personnes dont nous avons déjà une photo</a></li>-->
					</ul>
<?php } elseif ((RANK==RANG_USER)||(RANK==RANG_WAITING_USER)) { ?>
					<ul class="nav navbar-nav">
						<li><a href="#" onclick="afficherFormulaireModificationPersonne(-1); return false;">Nouvelle photo</a></li>
						<li><a href="#" onclick="data.setFilter(true,{filtreMyContribs:true}); data.applyFilter(true); affichage.setPageActive(null); affichage.liste();">Mes photos</a></li>
						<!--<li><a href="#" onclick="affichage.personnesAvecPhoto();">Personnes dont nous avons déjà une photo</a></li>-->
					</ul>
<?php } } ?>

				<!-- Menu zone de droite -->

<?php if(RANK==0) { ?>
					<form role="form" class="navbar-form navbar-right" onsubmit="doValidConx();">
						<div class="form-group">
							<input type="text" class="form-control" id="loginInput" placeholder="Identifiant">
						</div>
						<div class="form-group">
							<input type="password" class="form-control" id="pwdInput" placeholder="Mot de passe">
						</div>
						<button class="btn btn-success" type="submit">Valider</button>
					</form>
<?php } elseif((RANK>=RANG_ADMIN)||(RANK==RANG_USER)||(RANK==RANG_WAITING_USER)) { ?>
					 <ul class="nav navbar-nav navbar-right">
						<li><a href="#" onclick="modifMonCompteForm();"><span class="glyphicon glyphicon-user"></span></a></li>
						<li><a href="#" onclick="deconnexion();"><span class="glyphicon glyphicon-off"></span></a></li>
					</ul>
<?php }	elseif((RANK==RANG_ANONYME_CONTRIBUTOR)||(RANK==RANG_VISITOR)) { ?>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#" onclick="addModUser(-1);">Créer un compte</a></li>
						<li><a href="#" onclick="deconnexion();"><span class="glyphicon glyphicon-off"></span></a></li>
					</ul>
<?php } ?>		
				</div>
			</div>
		</nav>

		<div class="container">
			<div class="row">
				<div id="mainContent" class="col-md-10 col-md-offset-1 col-xs-12">

<?php if (RANK==0) { ?>

					<h4>Qu'est -e que c'est ?</h4>
					C'est un outil permettant de générer un trombinoscope sous format papier et, si vous êtes nombreux à le demander, en ligne. Ce trombinoscope est destiné à Lud'Été 2014, mais rien n'empêche qu'il serve à d'autres événements.
					Bien sûr, il n'y a rien d'obligatoire : vous pouvez venir à Lud'Été sans nous fournir de photo.
					Cet outil n'est pas géré par Mensa et est hébergé sur le serveur personnel de Maxence Klein. Nous stockons un minimum d'informations : nom, prénom, photo, tout le reste est optionnel.
					<h4>Quoi faire ?</h4>
					Connectez-vous avec les identifiants qui vous ont été fournis. Maintenant :
					<ul>
						<li><b>Créez un compte</b> Vous pouvez choisir le mot de passe que vous voulez, il n'apparaît pas en clair dans la base de données. Vous pouvez commencer avec ce nouveau compte. Il vous donnera accès aux autres photos quand il sera validé par les administrateurs.</li>
						<li>Ajoutez une nouvelle photo <i>en haut</i></li>
						<li>Renseignez les différents champs. On demande surtout nom et prénom.</li>
						<li>Une case à cocher vous permet d'indiquer si vous êtes d'accord pour que votre photo soit visible sur un trombinoscope en ligne (avec mot de passe bien sûr)</li>
						<li>Une case à cocher vous permet d'indiquer si vous souhaitez que votre photo soit supprimée une fois l'événement terminé.</li>
						<li>Le champ "Divers" sert à indiquer toute information que vous voulez donner, comme de dire si vous allez animer quelque chose, ou rappeler les années où vous êtes venus à Lud'Été (Serge aimerait bien le savoir...)</li>
						<li>Quand vous avez rempli tous les champs, validez.</li>
						<li>Si tout se passe bien, vous pouvez maintenant mettre une photo. Choisissez-en une pas trop grosse (<500ko) et de format jpg ou jpeg. Préférez un format 4/3 vertical.</li>
						<li>Si vous avez plusieurs personnes à ajouter, vous pouvez en créer de nouvelles : Une personne peut ajouter les photos pour tous ses invités (N'ajoutez pas Casimir ou les Pieds nickelés, je crois qu'ils sont déjà pris ailleurs !)</li>
						<li>Vous pouvez toujours modifier les personnes que vous avez ajoutées, ou même les supprimer.</li>
						<li><b>Attention ! si vous n'avez pas créé de compte</b>, dès que vous vous déconnectez, vous ne pouvez plus rien modifier : C'est le principe d'une boîte à lettres. Si vous avez un problème, pas de panique, vous pouvez toujours recommencer. Si nous avons des doublons, nous ferons le tri, et sinon, envoyez-nous un mail !</li>
					</ul>
<?php } ?>
				</div>
			</div>
		</div>
		<div class="cadreAlerte" id="alertes"></div>		
	</body>
</html>

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
		<link href="./lib/cropper/cropper.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"><!-- Autocomplete-->
		<script language='javascript' type='text/javascript'> //Définition des constantes
			var RANG_SUPER_ADMIN=<?php echo RANG_SUPER_ADMIN; ?>;
			var RANG_ADMIN=<?php echo RANG_ADMIN; ?>;
			var RANG_PRIVILEGED_USER=<?php echo RANG_PRIVILEGED_USER; ?>;
			var RANG_USER=<?php echo RANG_USER; ?>;
			var RANG_VISITOR=<?php echo RANG_VISITOR; ?>;
			var RANG_WAITING_USER=<?php echo RANG_WAITING_USER; ?>;
			var PWD_SEED='<?php echo PWD_SEED; ?>';
			var RANK=<?php echo RANK ?>;
			var ranks = [RANG_SUPER_ADMIN, RANG_ADMIN, RANG_PRIVILEGED_USER, RANG_USER, RANG_VISITOR, RANG_WAITING_USER];
		</script>
		<script language='javascript' type='text/javascript' src='./js/moteur.js'></script>
		<script src="./lib/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> <!-- Autocomplete-->
		<script src="./lib/bootstrap/js/bootstrap.min.js"></script>
		<script src="./lib/bootstrap.file-input.js"></script>
		<script src="./lib/handlebars.js"></script>
		<script src="./lib/cropper/cropper.min.js"></script>

		
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
								<li><a href="#" onclick="data.setFilter(true,null); data.applyFilter(); affichage.setPageActive(null); affichage.liste();">Liste</a></li>
								<li class="divider"></li>
								<li><a href="#" onclick="data.setFilter(true,{filtreS:1}); data.applyFilter(); affichage.setPageActive(null); affichage.liste();">Inscriptions</a></li>
								<li class="divider"></li>
								<li><a href="#" onclick="data.setFilter(true,null); data.applyFilter(); affichage.setPageActive(null); affichage.trombinoscope()">Trombinoscope</a></li>
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
<?php } elseif ((RANK==RANG_PRIVILEGED_USER)||(RANK==RANG_USER)||(RANK==RANG_WAITING_USER)) { ?>
					<ul class="nav navbar-nav">
						<li><a href="#" onclick="afficherFormulaireModificationPersonne(-1); return false;">Nouvelle photo</a></li>
						<li><a href="#" onclick="data.setFilter(true,{filtreContribsOf:data.user.ID}); data.applyFilter(); affichage.setPageActive(null); affichage.liste();">Mes photos</a></li>
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
<?php } elseif((RANK>=RANG_ADMIN)||(RANK==RANG_PRIVILEGED_USER)||(RANK==RANG_USER)||(RANK==RANG_WAITING_USER)) { ?>
					 <ul class="nav navbar-nav navbar-right">
						<li><a href="#" onclick="goHome();"><span class="glyphicon glyphicon-home"></span></a></li>
						<li><a href="#" onclick="modifMonCompteForm();"><span class="glyphicon glyphicon-user"></span></a></li>
						<li><a href="#" onclick="deconnexion();"><span class="glyphicon glyphicon-off"></span></a></li>
					</ul>
<?php }	elseif(RANK==RANG_VISITOR) { ?>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#" onclick="goHome();"><span class="glyphicon glyphicon-home"></span></a></li>
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

					<div class="jumbotron">
						<h1>Trombinoscope</h1>
						<p>Il s'agit d'un outil permettant de générer un trombinoscope. Si vous avez un compte, connectez-vous, sinon créez-en un !</p>
						<p><a class="btn btn-primary btn-lg" role="button" onclick="addModUser(-1);">Créer un compte</a></p>
					</div>

					<h4>Informations</h4>
					<p>Cet outil n'est pas géré par Mensa et est hébergé sur le serveur personnel de <a href='mailto:gromaxx@gmail.com'>Maxence Klein</a>. Nous stockons un minimum d'informations : nom, prénom, photo, tout le reste est optionnel.</p>
					<p>Ce site <b>n&apos;est pas compatible</b> avec internet explorer et nécessite l&apos;activation de <b>javascript</b>.</p>
<?php } ?>
				</div>
			</div>
		</div>
		<div class="cadreAlerte" id="alertes"></div>		
	</body>
</html>

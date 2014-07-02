<!-- Templates handlebar de l'interface administrative -->

<!-- modification de mon compte -->
<script id="mod-monCompte-template" type="text/x-handlebars-template">
	<h1 class='text-info'>Modifier mon compte : {{PSEUDO}}</h1>
	<p>Vous êtes un <a href='#' name='userType'><b>{{userType}}</b></a></p>
	<div class="alert alert-info alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		Laissez le mot de passe vide pour ne pas le changer.
	</div>
	<form class='form-horizontal' role='form' id='modMonCompte'>
		<div class='form-group'><label class='col-sm-2 control-label' for='inputNomPrenom'>Prénom et nom</label><div class='col-sm-6'><input type='text' class='form-control' id='inputNomPrenom' placeholder='Entrez un nom et un prénom' value='{{NOMPRENOM}}'></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='inputEmail'>Email</label><div class='col-sm-6'><input type='text' class='form-control' id='inputEmail' placeholder='Entrez un email' value='{{EMAIL}}'></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='pwd1'>Nouveau mot de passe</label><div class='col-sm-4'><input type='password' class='form-control' id='pwd1' placeholder='Mot de passe' value=''></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='pwd2'>Confirmation du mot de passe</label><div class='col-sm-4'><input type='password' class='form-control' id='pwd2' placeholder='Confirmez' value=''></div></div>
		<div class='form-group'><div class='col-sm-offset-2 col-sm-4'><button type='submit' class='btn btn-primary btn-sm'>Valider</button></div></div>
	</form>
</script>

<!-- ajout / modif d'un utilisateur -->
<script id="addMod-users-template" type="text/x-handlebars-template">
	{{#if addU}}
		<h1 class='text-info'>Ajouter un compte</h1>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<ul>
				<li>Au moins 6 caractères pour le <b>pseudo</b>. Attention, la casse (majuscule / minuscule) est prise en compte !</li>
				<li>L&apos;<b>email</b> n&apos;est pas obligatoire.</li>
				<li>Le <b>mot de passe</b> n&apos;apparaît pas en clair dans la base de donnée.</li>
				<li>Merci de préciser <b>prénom et nom</b> pour permettre de vous identifier.<br/>
				 <i>surtout pour ceux dont le pseudo et l&apos;email ne donnent pas d&apos;indice !</i></li>
				<li>La <b>clé</b> permettant la création a dû vous être fourni.</li>
			</ul>
		</div>
	{{else}}
		<h1 class='text-info'>Modifier un compte</h1>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			Laissez le mot de passe vide pour ne pas le changer.
		</div>
	{{/if}}
	<form class='form-horizontal' role='form' id='addModUser'>
		<div class='form-group'><label class='col-sm-2 control-label' for='inputPseudo'>Pseudo</label><div class='col-sm-6'><input type='text' class='form-control' id='inputPseudo' placeholder='Entrez un pseudo' value='{{PSEUDO}}'></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='inputNomPrenom'>Prénom et nom</label><div class='col-sm-6'><input type='text' class='form-control' id='inputNomPrenom' placeholder='Entrez un nom et un prénom' value='{{NOMPRENOM}}'></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='inputEmail'>Email</label><div class='col-sm-6'><input type='text' class='form-control' id='inputEmail' placeholder='Entrez un email' value='{{EMAIL}}'></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='pwd1'>Mot de passe</label><div class='col-sm-4'><input type='password' class='form-control' id='pwd1' placeholder='Mot de passe' value=''></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='pwd2'>Confirmation du mot de passe</label><div class='col-sm-4'><input type='password' class='form-control' id='pwd2' placeholder='Confirmez' value=''></div></div>
		{{#if ranks}}
			<div class='form-group'><label class='col-sm-2 control-label' for='selectRank'>Rang</label><div class='col-sm-8'><select class='form-control' id='selectRank'>
				{{#each ranks}}
					{{#if this.sel}}
						<option value='{{this.RANK}}' selected>{{this.rankName}}</option>
					{{else}}
						<option value='{{this.RANK}}'>{{this.rankName}}</option>
					{{/if}}
				{{/each}}
			</select></div></div>
		{{/if}}
		{{#if needKey}}
			<div class='form-group'><label class='col-sm-2 control-label' for='key'>Clé</label><div class='col-sm-4'><input type='password' class='form-control' id='key' placeholder='Clé de création de compte' value=''></div></div>
		{{/if}}
		<div class='form-group'><div class='col-sm-offset-2 col-sm-4'><button type='submit' class='btn btn-primary btn-sm'>Valider</button></div></div>
		<input id='userID' value={{this.ID}} type='hidden'>
	</form>
</script>

<!-- Ajout / Modification d'évènement -->
<script id="modAdd-event-template" type="text/x-handlebars-template">
		{{#if mod}}
		<h1 class='text-info'>Modification de l'évènement <i>{{evNom}}</i></h1>
		{{else}}
		<h1 class='text-info'>Ajout d'un évènement</i></h1>
		{{/if}}
		<form class='form-horizontal' role='form' id='modifEvenement'>
			<div class='form-group'><label class='col-sm-3 control-label' for='inputNom'>Nom</label><div class='col-sm-6'><input type='text' class='form-control' id='inputNom' placeholder='Entrez un nom' value='{{nom}}'></div></div>
			<div class='form-group'><div class='col-sm-offset-3 col-sm-4'><button type='submit' class='btn btn-primary btn-sm'>Valider</button></div></div>
		</form>
</script>

<!-- Liste des évènements -->
<script id="liste-event-template" type="text/x-handlebars-template">
	<h1 class='text-info'>Liste des évènements</h1>
	{{#if evenements}}
		<table class='table table-bordered table-striped'><tr><th width=16></th><th>Nom</th></tr>
		{{#each evenements}}
			<tr>
			<td><a href='#' idE={{this.ID}} name='edit-{{this.ID}}'><span class='glyphicon glyphicon-pencil'></span></a></td>
			<td><a href='#' idE={{this.ID}} name='trombi-{{this.ID}}'>{{this.NOM}}</a></td>
			</tr>
		{{/each}}
		</table>
		{{else}}
		<i>Aucun élèment dans la base</i>
	{{/if}}
</script>

<!-- Liste des personnes pour admin -->
<script id="listeAdmin-personne-template" type="text/x-handlebars-template">
	<div class='row'>
		<div class='col-md-6 col-xs-6'>
			{{#if pages}}
			<ul style='margin-top:0;' class='pagination'>
				{{#if premierePage}}
				<li class='disabled'><a href='#'>&laquo;</a></li>
				{{else}}
				<li><a id='pagePrecedente' href='#'>&laquo;</a></li>
				{{/if}}
				{{#each pages}}
					{{#if this.active}}
						<li class='active'><a href='#'>{{this.index}}<span class='sr-only'>(current)</span></a></li>
					{{else}}
						<li><a href='#' name='page-{{this.index}}' index={{this.index}}>{{this.index}}</a></li>
					{{/if}}
				{{/each}}
				{{#if dernierePage}}
				<li class='disabled'><a href='#'>&raquo;</a></li>
				{{else}}
				<li><a href='#' id='pageSuivante'>&raquo;</a></li>
				{{/if}}				
			</ul>
			{{/if}}
		</div>
		<div class='col-md-6 col-xs-6'>
			<div class='btn-group'>
			{{#if validationButton}}
				<button id='validationButton' type='button' class='btn btn-primary'>Valider</button>
			{{/if}}
			</div>
		</div>
	</div>

	
	<table class='table table-bordered table-striped'>
	<tr><th width=16></th><th width=16></th><th width=16></th><th>Nom</th><th>Prénom</th><th>Ville</th><th>Région</th><th>Propriétaire</th><th width=16><span class='glyphicon glyphicon-camera'></span></th></tr>
	{{#each personnes}}
		<tr id='tr{{this.ID}}' class='{{this.className}}'>
			<td><input type='checkbox' idP={{this.ID}}></td>
		{{#if this.writable}}
			<td><a href='#' name='affEdit-{{this.ID}}' idP={{this.ID}} ><span class='glyphicon glyphicon-pencil'></span></a></td>
			<td><a href='#' name='del-{{this.ID}}' idP={{this.ID}}><span class='glyphicon glyphicon-trash'></span></a></td>
		{{else}}
			<td><a href='#' name='affEdit-{{this.ID}}' idP={{this.ID}} ><span class='glyphicon glyphicon-eye-open'></span></a></td>
			<td></td>
		{{/if}}
		<td>{{this.NOM}}</td>
		<td>{{this.PRENOM}}</td>
		<td>{{this.VILLE}}</td>
		<td><a href='#' name='region-{{this.ID}}' idR={{this.IDREGION}}>{{this.nomRegion}}</a></td>
		<td><a href='#' name='prop' idA={{this.IDA}}>{{this.proprietaire}}</a></td>
		{{#if this.PHOTO}}
			<td><span class='glyphicon glyphicon-ok-sign'></span></td>
		{{else}}
			<td><span class='glyphicon glyphicon-question-sign'></span></td>
		{{/if}}
		</tr>
	{{/each}}
	</table>
</script>

<!-- Liste des personnes pour user -->
<script id="listeUser-personne-template" type="text/x-handlebars-template">
	<div class='row'>
		<div class='col-md-6 col-xs-6'>
			{{#if pages}}
			<ul style='margin-top:0;' class='pagination'>
				{{#if premierePage}}
				<li class='disabled'><a href='#'>&laquo;</a></li>
				{{else}}
				<li><a id='pagePrecedente' href='#'>&laquo;</a></li>
				{{/if}}
				{{#each pages}}
					{{#if this.active}}
						<li class='active'><a href='#'>{{this.index}}<span class='sr-only'>(current)</span></a></li>
					{{else}}
						<li><a href='#' name='page-{{this.index}}' index={{this.index}}>{{this.index}}</a></li>
					{{/if}}
				{{/each}}
				{{#if dernierePage}}
				<li class='disabled'><a href='#'>&raquo;</a></li>
				{{else}}
				<li><a href='#' id='pageSuivante'>&raquo;</a></li>
				{{/if}}				
			</ul>
			{{/if}}
		</div>
		<div class='col-md-6 col-xs-6'>
		</div>
	</div>

	
	<table class='table table-bordered table-striped'>
	<tr><th width=16></th><th width=16></th><th>Nom</th><th>Prénom</th><th>Ville</th><th>Région</th><th width=16><span class='glyphicon glyphicon-camera'></span></th></tr>
	{{#each personnes}}
		{{#if this.SUG}}
			<tr id='tr{{this.ID}}' class='danger'>
		{{else}}
			<tr id='tr{{this.ID}}'>
		{{/if}}
		{{#if this.writable}}
			<td><a href='#' name='affEdit-{{this.ID}}' idP={{this.ID}} ><span class='glyphicon glyphicon-pencil'></span></a></td>
			<td><a href='#' name='del-{{this.ID}}' idP={{this.ID}}><span class='glyphicon glyphicon-trash'></span></a></td>
		{{else}}
			<td><a href='#' name='affEdit-{{this.ID}}' idP={{this.ID}} ><span class='glyphicon glyphicon-eye-open'></span></a></td>
			<td></td>
		{{/if}}
		<td>{{this.NOM}}</td>
		<td>{{this.PRENOM}}</td>
		<td>{{this.VILLE}}</td>
		<td><a href='#' name='region-{{this.ID}}' idR={{this.IDREGION}}>{{this.nomRegion}}</a></td>
		{{#if this.PHOTO}}
			<td><span class='glyphicon glyphicon-ok-sign'></span></td>
		{{else}}
			<td><span class='glyphicon glyphicon-question-sign'></span></td>
		{{/if}}
		</tr>
	{{/each}}
	</table>
</script>



<!-- trombinoscope -->
<script id="trombinoscope-personne-template" type="text/x-handlebars-template">
	<div class='row'>
		<div class='col-md-6 col-xs-6'>
			{{#if pages}}
			<ul style='margin-top:0;' class='pagination'>
				{{#if premierePage}}
				<li class='disabled'><a href='#'>&laquo;</a></li>
				{{else}}
				<li><a id='pagePrecedente' href='#'>&laquo;</a></li>
				{{/if}}
				{{#each pages}}
					{{#if this.active}}
						<li class='active'><a href='#'>{{this.index}}<span class='sr-only'>(current)</span></a></li>
					{{else}}
						<li><a href='#' name='page-{{this.index}}' index={{this.index}}>{{this.index}}</a></li>
					{{/if}}
				{{/each}}
				{{#if dernierePage}}
				<li class='disabled'><a href='#'>&raquo;</a></li>
				{{else}}
				<li><a href='#' id='pageSuivante'>&raquo;</a></li>
				{{/if}}				
			</ul>
			{{/if}}
		</div>
		<div class='col-md-6 col-xs-6'>
			<div class='btn-group'>

			</div>
		</div>
	</div>

	
	<div class="table-responsive">
	<table class='table-bordered table-striped'>
		{{#each lignes}}
			<tr>
				{{#each this.personnes}}
					{{#if this.PHOTO}}
						<td><a href='#' class='thumbnail' style='margin-bottom:0px;' name='photo-{{this.ID}}' idP={{this.ID}}><img src='./img/{{this.PHOTO}}.jpg' class='imgTrombi'></a></td>
					{{else}}
						<td><a href='#' class='thumbnail' style='margin-bottom:0px;' name='photo-{{this.ID}}' idP={{this.ID}}><img src='./img/inconnu.png' class='imgTrombi'></a></td>
					{{/if}}
				{{/each}}
			</tr>
			<tr>
				{{#each this.personnes}}
					<td class='tdTrombi {{this.className}}'>{{this.PRENOM}}<br />{{this.NOM}}</td>
				{{/each}}
			</tr>			
		{{/each}}
	</table>
	</div>
</script>

<!-- Affichage d'une personne -->
<script id="affichage-personne-template" type="text/x-handlebars-template">
	<div class='row'>
		<div class='col-md-4 col-xs-4'>
			<div class='btn-group'>
				<button id='precButton' idP={{ID}} type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-chevron-left'></span> Précédente</button>
				<button id='retourButton' type='button' idP={{ID}} class='btn btn-default btn-sm'>Retour</button>
				<button id='nextButton' idP={{ID}} type='button' class='btn btn-default btn-sm'>Suivante <span class='glyphicon glyphicon-chevron-right'></span></button>
			</div>
		</div>
		<div id='barreDeFiltre' class='col-md-6 col-xs-6'></div>
	</div>

	<div class='col-md-3 col-xs-3'>
		{{#if PHOTO}}
			<img id='photo' src='./img/{{PHOTO}}.jpg' class='upImg'>
		{{else}}
			<img id='photo' src='./img/inconnu.png' class='upImg'>
		{{/if}}
	</div>

	<div class='col-md-6 col-xs-6'>
	
		<h3>{{PRENOM}} {{NOM}}</h3>
		<dl class="dl-horizontal">
		  <dt>Ville</dt><dd>{{VILLE}}</dd>
		  <dt>Région</dt><dd><a href='#' name='region' idR={{IDR}}>{{REGION}}</a></dd>
		  <dt>Hobbys</dt><dd>{{HOBBY}}</dd>
		</dl>
	</div>

	<div class='col-md-3 col-xs-3'>
		{{#if evenements}}
			<div class='list-group'>
			{{#each evenements}}
				{{#if this.actif}}
					<a href='#' name='evenement-{{this.IDE}}' idP="{{../../../ID}}" idE={{this.IDE}} actif=1 class='list-group-item list-group-item-success'>{{this.NOM}}</a>
				{{else}}
					<a href='#' name='evenement-{{this.IDE}}' idP="{{../../../ID}}" idE={{this.IDE}} actif=0 class='list-group-item'>{{this.NOM}}</a>
				{{/if}}
			{{/each}}
			</div>
		{{/if}}
	</div>
</script>

<!-- Formulaire d'ajout de personne -->
<script id="ajout-personne-template" type="text/x-handlebars-template">
	<h1 class='text-info'>Ajout d&apos;une personne</h1>
	<div class="alert alert-info alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		Commencez par compléter les différents champs puis validez. Vous pourrez ajouter une photo ensuite.
		<ul>
			<li>Vous pouvez précisez si vous souhaitez que votre photo apparaisse sur un trombinoscope en ligne.</li>
			<li>Si vous le souhaitez (case à cocher), vos photos et informations seront éffacées après l&apos;événement.</li>
		</ul>
	</div>

	<form class='form-horizontal' role='form' id='personneModif' idP={{ID}}>
		<div class='col-md-6 col-xs-6'>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputNom'>Nom</label><div class='col-sm-8'><input type='text' class='form-control' id='inputNom' placeholder='Entrez un nom' value=''></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputPrenom'>Prénom</label><div class='col-sm-8'><input type='text' class='form-control' id='inputPrenom' placeholder='Entrez le prénom' value=''></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputHobbys'>Hobbys</label><div class='col-sm-8'><input type='text' class='form-control' id='inputHobbys' placeholder='Entrez des hobbys' value=''></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputVille'>Ville</label><div class='col-sm-8'><input type='text' class='form-control' id='inputVille' placeholder='Entrez la ville' value=''></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='selectRegion'>Région</label><div class='col-sm-8'><select class='form-control' id='selectRegion'>
				{{#each regions}}
					<option value='{{this.IDR}}'>{{this.NOM}}</option>
				{{/each}}
			</select></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputDivers'>Divers</label><div class='col-sm-8'><input type='text' class='form-control' id='inputDivers' placeholder='Commentaires divers' value=''></div></div>
		</div>
		<div class='col-md-6 col-xs-6'>
			<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><div class='checkbox'><label><input id='inputVL' type='checkbox'> Visible en ligne</label></div></div></div>
			<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><div class='checkbox'><label><input id='inputEP' type='checkbox'> Effacer mes données après l&apos;événement</label></div></div></div>
			<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><button type='submit' class='btn btn-primary btn-sm'>Valider</button></div></div>
		</div>
	</form>
</script>

<!-- Formulaire de modification de personne -->
<script id="modification-personne-template" type="text/x-handlebars-template">
	{{#if PHOTO}}
	{{else}}
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			Vous pouvez maintenant ajouter une photo. Choisissez-en une pas trop grosse (<500ko), de format jpg ou jpeg. Préférez un format 4/3 vertical.
			Vous pouvez également indiquer les événements auxquels vous aller participer (avez participé).
		</div>
	{{/if}}
	<div class="row">
		<div class='col-md-4 col-xs-4'>
			<div class='btn-group'>
				<button id='precButton' idP={{ID}} type='button' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-chevron-left'></span> Précédente</button>
				<div class='btn-group'>
					<button type='button' class='btn btn-sm btn-default dropdown-toggle' data-toggle='dropdown'>Menu</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href='#' id='nouveauButton'>Nouvelle photo</span></a></li>
						{{#if SUG}}
							<li><a href='#' id='validButton' idP={{ID}}>Validation photo</span></a></li>
						{{/if}}
						<li><a href='#' id='delButton' idP={{ID}}>Supprimer photo</span></a></li>
						<li><a href='#' id='retourButton' idP={{ID}}>Retourner à la liste</a></li>
					</ul>
				</div>
				<button id='nextButton' idP={{ID}} type='button' class='btn btn-default btn-sm'>Suivante <span class='glyphicon glyphicon-chevron-right'></span></button>
			</div>
		</div>
		<div id='barreDeFiltre' class='col-md-6 col-xs-6'>
			{{#if SUG}}
				<label class="label label-danger">Photo non validée</span>			
			{{/if}}
		</div>
	</div>

	<div class='col-md-3 col-xs-3'>
		{{#if PHOTO}}
			<img id='photo' src='./img/{{PHOTO}}.jpg' class='upImg'>
		{{else}}
			<img id='photo' src='./img/inconnu.png' class='upImg'>
		{{/if}}
		<center><form method='POST' action='./php/upload.php' enctype='multipart/form-data' target='loadFrame'>
			<img id='ajaxFlag' class='invisible' src='./ajax.gif'>
			<input type='file' title='Modifier' id='btnFile' class='btn-primary' onchange='javascript:submit();' name='avatar'>
			<input type='hidden' name='MAX_FILE_SIZE' value='500000'>
			<button type='button' id='cropImageBtn' idP='{{ID}}' class='btn btn-primary' data-toggle="modal" data-target="#bootstrap-modal">Recadrer</button>
			<input type='hidden' name='IDP' value='{{ID}}'>
		</form></center>
		<iframe class='frameLoad' src='#' name='loadFrame' id='loadFrame'></iframe>
	</div>
	
	<div class='col-md-6 col-xs-6'>
		
	
		<form class='form-horizontal' role='form' id='personneModif' idP={{ID}}>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputNom'>Nom</label><div class='col-sm-8'><input type='text' class='form-control' id='inputNom' placeholder='Entrez un nom' value='{{NOM}}'></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputPrenom'>Prénom</label><div class='col-sm-8'><input type='text' class='form-control' id='inputPrenom' placeholder='Entrez le prénom' value='{{PRENOM}}'></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputVille'>Ville</label><div class='col-sm-8'><input type='text' class='form-control' id='inputVille' placeholder='Entrez la ville' value='{{VILLE}}'></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputHobbys'>Hobbys</label><div class='col-sm-8'><input type='text' class='form-control' id='inputHobbys' placeholder='Entrez des hobbys' value='{{HOBBY}}'></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='selectRegion'>Région</label><div class='col-sm-8'><select class='form-control' id='selectRegion'>
				{{#each regions}}
					{{#if this.sel}}
						<option value='{{this.IDR}}' selected>{{this.NOM}}</option>
					{{else}}
						<option value='{{this.IDR}}'>{{this.NOM}}</option>
					{{/if}}
				{{/each}}
			</select></div></div>
			<div class='form-group'><label class='col-sm-4 control-label' for='inputDivers'>Divers</label><div class='col-sm-8'><input type='text' class='form-control' id='inputDivers' placeholder='Commentaires divers' value='{{DIVERS}}'></div></div>
			{{#if VL}}
				<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><div class='checkbox'><label><input id='inputVL' type='checkbox' checked> Visible en ligne</label></div></div></div>
			{{else}}
				<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><div class='checkbox'><label><input id='inputVL' type='checkbox'> Visible en ligne</label></div></div></div>
			{{/if}}
			{{#if EP}}
				<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><div class='checkbox'><label><input id='inputEP' type='checkbox' checked> Effacer mes données après l'évènement</label></div></div></div>
			{{else}}
				<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><div class='checkbox'><label><input id='inputEP' type='checkbox'> Effacer mes données après l'évènement</label></div></div></div>
			{{/if}}	
			<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><button type='submit' class='btn btn-primary btn-sm'>Valider</button></div></div>
		</form>
	</div>

	<div class='col-md-3 col-xs-3'>
		{{#if evenements}}
			<div class='list-group'>
			{{#each evenements}}
				{{#if this.actif}}
					<a href='#' name='evenement-{{this.IDE}}' idP="{{../../../ID}}" idE={{this.IDE}} actif=1 class='list-group-item list-group-item-success'>{{this.NOM}}</a>
				{{else}}
					<a href='#' name='evenement-{{this.IDE}}' idP="{{../../../ID}}" idE={{this.IDE}} actif=0 class='list-group-item'>{{this.NOM}}</a>
				{{/if}}
			{{/each}}
			</div>
		{{/if}}
	</div>


	{{#if PHOTO}}
		<div class="modal fade" id="bootstrap-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
						<h4 class="modal-title" id="myModalLabel">Recadrer votre photo</h4>
					</div>
					<div class="modal-body">
						<div class="bootstrap-modal-cropper"><img id='photoCrop' src="./img/{{PHOTO}}.jpg"></div>
					</div>
					<div class="modal-footer">
						<img class='invisible' id='ajaxFlagModal' src='./ajax.gif'>
						<button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
						<button id='validCropBtn' idP="{{ID}}" type="button" class="btn btn-primary">Valider</button>
					</div>
				</div>
			</div>
		</div>
	{{/if}}
</script>

<!-- Liste des utilisateurs -->
<script id="liste-users-template" type="text/x-handlebars-template">
	<h1 class='text-info'>Liste des utilisateurs</h1>
	{{#if pages}}
	<div>
		<ul class='pagination'>
			{{#each pages}}
				{{#if this.active}}
					<li class='active'><a href='#'>{{this.index}}<span class='sr-only'>(current)</span></a></li>
				{{else}}
					<li><a href='#' name='page-{{this.index}}' index={{this.index}}>{{this.index}}</a></li>
				{{/if}}
			{{/each}}
		</ul>
	</div>
	{{/if}}

	<table class='table table-bordered table-striped'>
	<thead><tr>
		<th width=16></th>
		<th width=16><a href='#' name='addU'><span class='glyphicon glyphicon-plus'></span></a>
		<th width=16></th></th><th>PSEUDO</th><th>Nom/Prénom</th><th>EMAIL</th><th>RANG</th><th>Connexion</th>
	</tr></thead>
	<tbody>
	{{#each users}}
		<tr>
		<td>
		{{#if this.editable}}
			<a href='#' name='edit-{{this.ID}}' idU={{this.ID}} ><span class='glyphicon glyphicon-pencil'></span></a>
		{{/if}}
		</td>
		<td>
		{{#if this.deletable}}
			<a href='#' name='del-{{this.ID}}' idU={{this.ID}} ><span class='glyphicon glyphicon-trash'></span></a>
		{{/if}}
		</td>
		<td><a href='#' name='photos' idU={{this.ID}} ><span class='glyphicon glyphicon-camera'></span></a></td>
		<td>{{this.PSEUDO}}</td>
		<td>{{this.NOMPRENOM}}</td>
		<td>{{this.EMAIL}}</td>
		<td>{{this.rankName}}</td>
		<td>le {{this.shortDate}} à {{this.HEURE}}</td>
		</tr>
	{{/each}}
	</tbody>
	</table>
</script>

<!-- Formulaire pour les filtres -->
<script id="filtres-template" type="text/x-handlebars-template">
	{{#each items}}
		<span name='bf-{{this.f}}' class='label {{this.className}}'>{{this.text}} <a href='#' name='filtre' f='{{this.f}}'><span class='glyphicon glyphicon-remove-sign'></span></a></span>
	{{/each}}
</script>

<!-- Panneau d'accueil pour un utilisateur en attente -->
<script id="accueil-waiting-user" type="text/x-handlebars-template">
	<div class="jumbotron">
		<h1>Bienvenue !</h1>
		<p>Vous êtes un <b>utillisateur en attente de validation</b>.</p>
		<p>Vous pouvez maintenant ajouter des personnes avec leurs photos. Une seule personne peut inscrire toute sa famille, tous ses invités.</p>
		<p>
			<a name='addPerson' class="btn btn-primary btn-lg" role="button">Ajouter une photo</a>
			<a name='mesPhotos' class="btn btn-primary btn-lg" role="button">Voir mes photos</a>
			<a name='monCompte' class="btn btn-primary btn-lg" role="button">Modifier mon compte <span class="glyphicon glyphicon-user"></span></a>
		</p>
	</div>
	<a href='#' name='btnInfo'>fonctionnement du site</a>
</script>

<!-- Panneau d'accueil pour un utilisateur (privilégié ou pas) -->
<script id="accueil-user" type="text/x-handlebars-template">
	<div class="jumbotron">
		<h1>Bienvenue !</h1>
		<p>Vous pouvez maintenant consulter le trombinoscope ou	ajouter des personnes avec leurs photos. Une seule personne peut inscrire toute sa famille, tous ses invités.</p>
		<p>
			<a name='trombi' class="btn btn-primary btn-lg" role="button">Voir le trombinoscope</a>
			<a name='addPerson' class="btn btn-primary btn-lg" role="button">Ajouter une photo</a>
			<a name='mesPhotos' class="btn btn-primary btn-lg" role="button">Voir mes photos</a>
			<a name='monCompte' class="btn btn-primary btn-lg" role="button">Modifier mon compte <span class="glyphicon glyphicon-user"></span></a>
		</p>
	</div>
	<a href='#' name='btnInfo'>fonctionnement du site</a>
</script>

<!-- Panneau d'accueil pour un admin -->
<script id="accueil-admin-template" type="text/x-handlebars-template">
	<div class="jumbotron">
		<h1>Bienvenue !</h1>
		<p>Vous êtes connecté(e) avec un compte <b>Administrateur</b>.</p>
		{{#if photosEnAttente}}
			<p><a name='photosEnAttenteBtn' class="btn btn-warning btn-lg" role="button"><span class="badge">{{photosEnAttente}}</span> photo(s) en attente de validation</a></p>
		{{/if}}
		{{#if usersEnAttente}}
			<p><a name='usersEnAttenteBtn' class="btn btn-warning btn-lg" role="button"><span class="badge">{{usersEnAttente}}</span> utilisateur(s) en attente de validation</a></p>
		{{/if}}
		<p>
			<a name='listePhotosBtn' class="btn btn-primary btn-lg" role="button">Liste des photos</a>
			<a name='addPerson' class="btn btn-primary btn-lg" role="button">Ajouter une photo</a>
			<a name='monCompte' class="btn btn-primary btn-lg" role="button">Modifier mon compte <span class="glyphicon glyphicon-user"></span></a>
		</p>
	</div>
	<a href='#' name='btnInfo'>fonctionnement du site</a>
</script>

<!-- Panneau d'information sur la politique du site -->
<script id="information" type="text/x-handlebars-template">
	<h1>Gestion des informations</h1>
	<h4>Les comptes utilisateurs</h4>
	<ul>
		<li>Un nouvel utilisateur est invité à choisir un <b>pseudo</b> et un <b>mot de passe</b>.</li>
		<li>Ils peuvent préciser leur nom et leur prénom pour faciliter leur identification.</li>
		<li>Ils peuvent préciser un email pour faciliter le dialogue.</li>
		<li>Les administrateurs sont seuls à pouvoir voir toutes ces informations, mais ils ne peuvent pas connaître le mot de passe des utilisateurs qui sont cryptés en base de données<br/>
		<i>Attention tout de même : le type de cryptage utilisé (MD5) n&apos;est pas inviolable !</i></li>
		<li>Un utilisateur peut changer ses données et son mot de passe, mais pas son pseudo. Il peut toutefois demander à un administrateur de le faire pour lui.</li>
		<li>Un utlilsateur ne peut pas éffacer son compte, mais il peut demander à un administeur de le faire pour lui.</li>
		<li>Quand un utilisateur se connecte, on enregistre en base de données l&apos;heure de sa dernière connexion.</li>
	</ul>
	<h4>Les photos et informations sur les personnes</h4>
	<ul>
		<li>Lorsqu&apos;un utilisateur créée une nouvelle photo, on demande au minimum un <b>nom</b> et un <b>prénom</b>.</li>
		<li>L&apos;utilisateur est libre de préciser en plus une <b>ville</b>, une <b>région</b>, des <b>hobbys</b> et toutes informations <b>diverses</b>.</li>
		<li>Le trombinoscope ayant comme première vocation d&apos;être sur papier, les utilisateurs sont libres de demander que leur(s) photo(s) n&apos;apparaisse(nt) pas en ligne.</li>
		<li>Pour chacune de ses photos, un utilisateur peut préciser s&apos;il souhaite qu&apos;elle soit éffacée après le déroulement de l&apos;évènement pour lequel il l&apos;a créée. Les photos et informations concernées seront éffacées par un administrateur après l&apos;évènement.</li>
		<li>Un utlisateur est libre de modifier toute information sur les éléments qu&apos;il a créés. Il peut aussi tout supprimer. Aucun archive n&apos;est conservé, une suppression est donc définitive.</li>
		<li>Lors d&apos;un nouvel ajout, une photo est soumise à une modération a priori. La photo doit être validée par un administrateur.</li>
		<li>Quand une photo est validée, toute modification a un effet immédiat, sans modération.</li>
	</ul>
	<h4>Les différents types d&apos;utilisateurs.</h4>
	<ul>
		<li>Le <b>super-administrateur</b> a les mêmes droits qu&apos;administeur, avec en plus la possibilité de promouvoir des administrateurs. Ce compte est unique.</li>
		<li>Un <b>administrateur</b> voit toutes les photos, validées ou pas. Il a le droit de les modifier ainsi que les informations jointes. Il peut créer tout type d&apos;utilisateur et de les modifier.</li>
		<li>Un <b>utlisateur privilégié</b> a les mêmes droits qu&apos;un utilisateur ordinaire mais il peut voir toutes les photos, validées ou pas. Ce compte est destiné à des organisateurs qui ont besoin de récupérer des photos.</li>
		<li>Un <b>utilisateur</b> peut ajouter des photos, les modifier, les effacer. Il voit ses propres photos, validées ou pas, et il voit des autres les photos validées et visibles en ligne.</li>
		<li>Un <b>visiteur</b> peut seulement voir les photos : celles qui sont validées et visibles en ligne. Il ne peut pas en créer.</li>
		<li>Un <b>utilisateur en attente</b> est un utlisateur qui attend d&apos;être validé par un administrateur. Il peut créer des photos, les modifier et les supprimer, mais il ne peut voir que ses propres photos.</li>
	</ul>
</script>
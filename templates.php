<!-- Templates handlebar de l'interface administrative -->

<!-- modification de mon compte -->
<script id="mod-monCompte-template" type="text/x-handlebars-template">
	<h1 class='text-info'>Modifier mon compte</h1>
	<form class='form-horizontal' role='form' id='modMonCompte'>
		<div class='form-group'><label class='col-sm-2 control-label' for='pwd1'>Nouveau mot de passe</label><div class='col-sm-4'><input type='password' class='form-control' id='pwd1' placeholder='Mot de passe' value=''></div></div>
		<div class='form-group'><label class='col-sm-2 control-label' for='pwd2'>Confirmation du mot de passe</label><div class='col-sm-4'><input type='password' class='form-control' id='pwd2' placeholder='Confirmez' value=''></div></div>
		<div class='form-group'><div class='col-sm-offset-2 col-sm-4'><button type='submit' class='btn btn-primary btn-sm'>Valider</button></div></div>
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

<!-- Liste des personnes -->
<script id="liste-personne-template" type="text/x-handlebars-template">
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
			{{#if fusionButton}}
				<button id='fusionButton' type='button' class='btn btn-primary'>Fusionner</button>
			{{/if}}
			{{#if validationButton}}
				<button id='validationButton' type='button' class='btn btn-primary'>Valider</button>
			{{/if}}
			</div>
		</div>
	</div>

	
	<table class='table table-bordered table-striped'>
	<tr><th width=16></th><th width=16></th><th width=16></th><th>Nom</th><th>Prénom</th><th>Ville</th><th>Région</th><th width=16><span class='glyphicon glyphicon-camera'></span></th></tr>
	{{#each personnes}}
		{{#if this.SUG}}
			<tr id='tr{{this.ID}}' class='danger'>
		{{else}}
			<tr id='tr{{this.ID}}'>
		{{/if}}
		<td><input type='checkbox' idP={{this.ID}}></td>
		<td><a href='#' name='edit-{{this.ID}}' idP={{this.ID}} ><span class='glyphicon glyphicon-pencil'></span></a></td>
		<td><a href='#' name='del-{{this.ID}}' idP={{this.ID}}><span class='glyphicon glyphicon-trash'></span></a></td>
		<td>{{this.NOM}}</td>
		<td>{{this.PRENOM}}</td>
		<td>{{this.VILLE}}</td>
		<td><a href=#' name='region-{{this.ID}}' idR={{this.IDREGION}}>{{this.nomRegion}}</a></td>
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

	
	
	<table class='table-bordered table-striped'>
		{{#each lignes}}
			<tr>
				{{#each this.personnes}}
					{{#if this.PHOTO}}
						<td><a href='#' class='thumbnail' name='photo-{{this.ID}}' idP={{this.ID}}><img src='./img/{{this.PHOTO}}.jpg' width='150' height='200'></a></td>
					{{else}}
						<td><a href='#' class='thumbnail' name='photo-{{this.ID}}' idP={{this.ID}}><img src='./img/inconnu.png' width='150' height='200'></a></td>
					{{/if}}
				{{/each}}
			</tr>
			<tr>
				{{#each this.personnes}}
					<td class='tdTrombi'>{{this.PRENOM}}<br />{{this.NOM}}</td>
				{{/each}}
			</tr>			
		{{/each}}
	</table>
</script>

<!-- Formulaire de modification de personne -->
<script id="modification-personne-template" type="text/x-handlebars-template">
	<!-- zone de photo -->
	<div class='col-md-3 col-xs-3'>
		{{#if affPhoto}}
			{{#if PHOTO}}
				<img id='photo' src='./img/{{PHOTO}}.jpg' width='150' height='200'>
			{{else}}
				<img id='photo' src='./img/inconnu.png' width='150' height='200'>
			{{/if}}
			<center><form method='POST' action='./php/upload.php' enctype='multipart/form-data' target='loadFrame'>
				<input type='file' title='Modifier' class='btn-primary' onchange='javascript:submit();' name='avatar'>
				<input type='hidden' name='MAX_FILE_SIZE' value='100000'>
				
				<input type='hidden' name='idPersonne' value='{{ID}}'>
			</form></center>
			<iframe class='frameLoad' src='#' name='loadFrame' id='loadFrame'></iframe>
		{{/if}}
	</div>
	
	<!-- zone d'édition -->
	<div class='col-md-6 col-xs-6'>
		<!-- Boutons de navigation, ajout, suppression -->
		{{#if affCommandes}}
			<div class='btn-group'>
				<button id='precButton' idP={{ID}} type='button' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-chevron-left'></span> Précédente</button>
				<button id='nouveauButton' type='button' class='btn btn-success btn-sm'>Nouvelle <span class='glyphicon glyphicon-plus'></span></button>
				<button id='delButton' type='button' idP={{ID}} class='btn btn-danger btn-sm'>Supprimer <span class='glyphicon glyphicon-trash'></span></button>
				<button id='retourButton' type='button' idP={{ID}} class='btn btn-info btn-sm'>Retour <span class='glyphicon glyphicon-eject'></span></button>
				<button id='nextButton' idP={{ID}} type='button' class='btn btn-primary btn-sm'>Suivante <span class='glyphicon glyphicon-chevron-right'></span></button>
			</div>
		{{else}}
			<h1 class='text-info'>Ajout d'une personne</h1>
		{{/if}}
	
		<!-- Formulaire de modification -->
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
			<div class='form-group'><div class='col-sm-offset-4 col-sm-8'><button type='submit' class='btn btn-primary btn-sm'>Valider</button></div></div>";
		</form>
	</div>

	<!-- zone de participations -->
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

<!-- Liste des personnes ayant déjà fourni leur photo -->
<script id="listeAvecPhoto-personne-template" type="text/x-handlebars-template">
	<h1 class='text-info'>Liste des personnes ayant déjà fourni une photo</h1>
	<p>Inscrivez vous tout de même pour nous dire que vous venez en 2014, vous pourrez dans le même temps changer votre photo. Si vous souhaitez qu'on efface votre photo, envoyez-nous un mail !</p>
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
	<tr><th>Nom</th><th>Prénom</th><th>Ville</th><th>Mis à jour</th></tr>
	{{#each personnes}}
		<tr>
		<td>{{this.NOM}}</td>
		<td>{{this.PRENOM}}</td>
		<td>{{this.VILLE}}</td>
		<td>{{this.DATE}}</td>
		</tr>
	{{/each}}
	</table>
</script>

<!-- Formulaire de fusion pour les attributs : NOM, PRENOM, VILLE, REGION, HOBBY, DIVERS -->
<script id="attribute-fusion-template" type="text/x-handlebars-template">
	<h1>Fusion d'éléments</h1>
	<h4>Choisissez le bon attribut : {{NOM}}</h4>
	<form role='form' id='chooseGoodAttribute'>
		{{#each items}}
			<div class='col-sm-6'>
				<span class="glyphicon glyphicon-calendar"></span>{{this.DATE}}
				<div class='input-group'>
					<span class='input-group-addon'><input type='radio' name='optionRadio' id='rad-{{this.ID}}' value={{this.ID}}></span>
					{{#if this.IDR}}
						<input type='text' value='{{this.VALEUR}}' class='form-control' readonly>
						<input id='chk-{{this.ID}}' type='hidden' value='{{this.IDR}}'></div></div>
					{{else}}
						<input id='chk-{{this.ID}}' type='text' value='{{this.VALEUR}}' class='form-control'>
					{{/if}}
				</div>
			</div>
		{{/each}}
		<div class='btn-group'>
		<button type='submit' class='btn btn-primary'>Suivant</button>
		<button type='button' id='cancelButton' class='btn btn-danger'>Annuler</button>
		</div>
	</form>
</script>

<!-- Formulaire de fusion pour l'attribut PHOTO -->
<script id="photo-fusion-template" type="text/x-handlebars-template">
	<h1>Fusion d'éléments</h1>
	<h4>Choisissez la bonne photo</h4>
	{{#each items}}
		<div class='col-sm-4'>
		<a href='#' class='thumbnail' name='photo-{{this.ID}}' idP={{this.ID}}><img src='./img/{{this.PHOTO}}.jpg' width='150' height='200'></a>
		</div>
	{{/each}}
	<button type='button' id='cancelButton' class='btn btn-danger'>Annuler</button>
</script>

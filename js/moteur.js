var alCount=0; // Compteur d'alerte

//-------------------------
//      Initialisation
//-------------------------

function init(){
	if (RANK>0){ // Cas connecté
		data.load();
		data.applyFilter(true);
		if (RANK>=RANG_VISITOR) {
			if (data.evenements.length>0) {
				data.lastEventID=data.evenements[data.evenements.length-1].ID;
				data.setFilter(true,{filtreE:data.lastEventID});
			} else data.setFilter(true,null);
			data.applyFilter(true);
			affichage.setPageActive(null);
			affichage.trombinoscope();
		} else {
			if (RANK==RANG_ANONYME_CONTRIBUTOR) { addModUser(-1); }
			else { // C'est donc un utilisateur en attente
				if (data.personnes.length>0) {
					data.setFilter(true,null);
					data.applyFilter(true);
					affichage.trombinoscope();
				} else {
					afficherFormulaireModificationPersonne(-1);
				}
			}
		}
	}
}

function rankName(rank){
	if (rank==RANG_SUPER_ADMIN) { return "Super administrateur"; }
	else {
		if (rank==RANG_ADMIN) { return "Administrateur"; }
		else {
			if (rank==RANG_USER) { return "Utilisateur"; }
			else {
				if (rank==RANG_VISITOR) { return "Visiteur"; }
				else {
					if (rank==RANG_WAITING_USER) { return "Utilisateur en attente"; }
				}
			}
		}
	}
	return "Contributeur anonyme";
}

//----------------------------
// Interface
//-----------------------------
// Réponse à un click sur le bouton "Trombinoscope" de la bannière
function trombinoscopeButtonClick(){
	if (RANK>=RANG_VISITOR) {
		affichage.setPageActive(null);
		if (data.lastEventID!=null) {
			data.setFilter(true,{filtreE:data.lastEventID});
			data.applyFilter(true);
		}
		affichage.trombinoscope();
	} else {
		if ((RANK==RANG_ANONYME_CONTRIBUTOR)||(RANK=RANG_WAITING_USER)) {
			// Permet de voir ses propres photos
			if (data.personnes.length>0) {
				data.setFilter(true,null);
				data.applyFilter(true);
				affichage.trombinoscope();
			}
		}
	}
}

// Demande de modification ou ajout d'un utilisateur
function addModUser(id){
	var i;
	var user=null;
	var context={ID:id};
	if (id==data.user.ID) { modifMonCompteForm(); }
	else {
		if (id!=-1) { user=data.getUserById(id); }
		else {context.addU=true; }

		if (user==null) {
			context.PSEUDO="";
			context.EMAIL="";
			context.RANK=RANG_ANONYME_CONTRIBUTOR;
		} else {
			context.PSEUDO=user.PSEUDO;
			context.RANK=user.RANK;
			context.EMAIL=user.EMAIL;
		}
		if (RANK>=RANG_ADMIN) {
			context.ranks=[];
			for (i=0;i<ranks.length;i++){
				if (ranks[i]<RANK) {
					if (ranks[i]==context.RANK) { context.ranks.push({RANK:ranks[i], rankName:rankName(ranks[i]), sel:true}); }
					else { context.ranks.push({RANK:ranks[i], rankName:rankName(ranks[i])}); }
				}
			}
		}

		container=$("#mainContent");
		container.empty();
		
		var source   = $("#addMod-users-template").html();
		var template = Handlebars.compile(source);
		
		container.append(template(context));
		$("#addModUser").submit(function() { validAddModUser(); return false; });
	}
}

// Validation du formulaire précédent
function validAddModUser(){
	var reponse;
	var user;
	var id=$('#userID').val();
	var pseudo=trim($('#inputPseudo').val());
	var email=$('#inputEmail').val();
	var pwdInput1=$('#pwd1');
	var pwdInput2=$('#pwd2');
	var rank;

	if (RANK>=RANG_ADMIN) {	rank=$('#selectRank').val(); }
	else {rank=0;}

	if (pwdInput1.val()!=pwdInput2.val()) { addAlert("Mots de passe différents.",0); }
	else {
		if (pseudo.length<6) {
			addAlert("Votre pseudo est trop court.",0);
		} else {
			pwd=MD5(PWD_SEED+pwdInput1.val());
			pwdInput1.val('');
			pwdInput2.val('');
			reponse=manager.addModUser(pseudo, email, pwd, rank, id);
			if (reponse.state=="success") {
				if (id==-1) {
					user={ID:reponse.insertedID, PSEUDO:pseudo, EMAIL:email, RANK:rank, rankName:rankName(rank), editable:true, deletable:true};
					if (data.usersList!=null) { data.usersList.push(user); }
					addAlert("Succès de la création.",1);
				} else {
					user=data.getUserById(id);
					if (user!=null) {
						user.PSEUDO=pseudo;
						user.EMAIL=email;
						user.RANK=rank;
						user.rankName=rankName(rank);
						addAlert("Succès de la modification.",1);
					}
				}
				if (RANK>=RANG_ADMIN) { affichage.listeUsers(); }
				else {
					// On provoque la reconnexion avec  le nouvel utilisateur
					manager.post('./php/login.php',{pwd:pwd,login:pseudo});
					location.reload();
				}
			} else {
				if (id==-1) { addAlert("Échec de la création de l'utilisateur.", 0);}
				else { addAlert("Échec de la modification de l'utilisateur.", 0);}
			}

		}
	}
}

// Réponse à une pression sur un bouton d'effacement utilisateur
function delUser(id){
	var reponse;
	var index;
	var user=data.getUserById(id);
	if (user!=null){
		if(confirm("Confirmez-vous la suppression de "+user.PSEUDO+" ?")) {
			reponse=manager.delUser(id);
			if (reponse.state=="success") {
				if (data.usersList!=null) {
					index=data.usersList.indexOf(user);
					if (index>=0) { data.usersList.splice(index,1); }
				}
				addAlert("Succès de la suppression.",1);
				affichage.listeUsers();
			} else {
				addAlert("Échec de la suppression.",1);
			}

		}
	}
}

// Réponse au bouton de suppression d'un filtre
function delFilter(f) {
	var filter;
	switch(f) {
		case 'R' : filter={filtreR:null}; break;
		case 'S' : filter={filtreS:null}; break;
		case 'E' : filter={filtreE:null}; break;
		case 'V' : filter={filtreV:null}; break;
		case 'P' : filter={filtreP:null}; break;
		case 'Se' : filter={activeSearch:false}; break;
		case 'MC' : filter={filtreMyContribs:null}; break;
		default : filter={};
	}

	$("span[name='bf-"+f+"']").remove();
	data.setFilter(false,filter);
	data.applyFilter(true);
	switch(affichage.actif) {
		case "liste" : affichage.liste(); break;
		case "trombinoscope" : affichage.trombinoscope(); break;
	}
}

//--------------------------------
// Interface liée aux évènements
//--------------------------------

// Création de l'interface permettant de modifier un évènement
function afficherFormModificationEvenement(ev){
	var ev, id;
	var container;
	var context;
	
	affichage.actif="";

	if (typeof ev == 'object') {
		if (ev==null) {
			id=-1;
		} else {
			id=ev.ID;
		}
	} else {
		id=ev;
		ev=data.getEvenementById(id);
	}

	evNom="";
	if (ev!=null) evNom=ev.NOM;
	
	container=$("#mainContent");
	container.empty();
	
	var source   = $("#modAdd-event-template").html();
	var template = Handlebars.compile(source);
	
	context = {nom:evNom};
	if (id!=-1) { context.mod=true; }
	container.append(template(context));
	$("#modifEvenement").submit(function() { doAddModEvenement(id); return false; });
}

// Validation de l'interface précédente - id=-1 pour ajout
function doAddModEvenement(id){
	id = typeof id !== 'undefined' ? id : -1;
	
	var nom=trim($('#inputNom').val());
	var reponse=manager.addModEvenement(id,nom);

	if (reponse.state=="success") {
		if (id==-1) {
			data.addEvent({ID:reponse.insertedID, NOM:nom, liens:[]});
			addAlert("<i>"+nom+"</i> a bien été ajouté(e).",1);
		} else {
			data.modEvent(id,{NOM:nom});
			addAlert("L'évènement <i>"+nom+"</i> a bien été modifié(e).",1);
		}
	} else {
		if (id==-1) addAlert("L'ajout n'a pu être effectué.",0);
		else addAlert("La modification n'a pu être effectué.",0);
	}
}

// Affichage de la liste des évènements
function afficherListeEvenements(){
	var container=$("#mainContent");

	affichage.actif="";

	container.empty();

	var source   = $("#liste-event-template").html();
	var template = Handlebars.compile(source);
	
	context = {evenements:data.evenements};
	container.append(template(context));
	
	// Activation des boutons
	if (RANK>=RANG_ADMIN) {
		$("a[name|='edit']").bind("click",function(){afficherFormModificationEvenement(this.getAttribute("idE")); return false; });
	}
	$("a[name|='trombi']").bind("click",function(){data.setFilter(true,{filtreE:this.getAttribute("idE")}); data.applyFilter(true); affichage.setPageActive(null); affichage.trombinoscope(); return false;});
}

//------------------------------
// Interface liée aux personnes
//------------------------------

// Choisi, suivant le cas, entre l'affichage d'une personne et le formulaire de modification
function choixModifOuAffichage(id){
	var personne;
	if (typeof id == 'object') {
		if (id==null) {
			personne=null;
			id=-1;
		} else {
			personne=id;
			id=personne.ID;
		}
	} else {
		personne=data.getPersonneById(id);
	}

	if (personne==null) { afficherFormulaireModificationPersonne(null); }
	else {
		if ((RANK>RANG_ADMIN)||(personne.SUG==1)||(personne.IDA==data.user.ID)) {
			afficherFormulaireModificationPersonne(personne);
		} else {
			affichage.personne(personne);
		}
	}
}

// Création de l'interface permettant de modifier une personne
function afficherFormulaireModificationPersonne(id){
	var container=$("#mainContent");
	var context={};
	var personne, nouvelleRegion, nouvelEvenement;
	var i;
	
	affichage.actif="modification personne";

	if (typeof id == 'object') {
		if (id==null) {
			personne=null;
			id=-1;
		} else {
			personne=id;
			id=personne.ID;
		}
	} else {
		personne=data.getPersonneById(id);
	}
	
	//--- Création du contexte -------
	if (personne==null){
		context.ID=-1;
		context.NOM="";
		context.PRENOM="";
		context.VILLE="";
		context.HOBBY="";
		context.DIVERS="";
	} else {
		context.ID=personne.ID;
		context.NOM=personne.NOM;
		context.PRENOM=personne.PRENOM;
		context.VILLE=personne.VILLE;
		context.HOBBY=personne.HOBBY;
		context.DIVERS=personne.DIVERS;
		context.affCommandes=true;
		if (personne.VL==1) { context.VL=true; }
		if (personne.EP==1) { context.EP=true; }
		context.affPhoto=true;
		if (personne.PHOTO!='') { context.PHOTO=personne.PHOTO; }
	}
	context.regions=[];
	for (i=0;i<data.regions.length;i++) {
		nouvelleRegion={IDR:data.regions[i].ID, NOM:data.regions[i].NOM};
		if ((personne!=null)&&(personne.IDREGION==data.regions[i].ID)) { nouvelleRegion.sel=true; }
		context.regions.push(nouvelleRegion);
	}
	
	if (personne!=null) {
		context.evenements=[];
		for (i=0;i<data.evenements.length;i++) {
			nouvelEvenement={IDE:data.evenements[i].ID, NOM:data.evenements[i].NOM};
			if (personne.liens.indexOf(data.evenements[i])>=0) { nouvelEvenement.actif=true; }
			context.evenements.push(nouvelEvenement);
		}
	}
	
	//--- Applcation du contexte au template handlebars -------
	
	var source   = $("#modification-personne-template").html();
	var template = Handlebars.compile(source);

	container.empty();
	affichage.displayFilter(container);
	container.append(template(context));
	
	//--- Création des évènements  -------
	$('#precButton').bind("click", function() { choixModifOuAffichage(data.getPrev(this.getAttribute("idP"),false)); });
	$('#nextButton').bind("click", function() { choixModifOuAffichage(data.getNext(this.getAttribute("idP"))); });
	$('#delButton').bind("click", function() { var idP=this.getAttribute("idP"); if(delPersonne(idP)) { choixModifOuAffichage(data.getPrev(idP,true));} });
	$('#retourButton').bind("click", function() { affichage.setPageActive(this.getAttribute("idP")); if (affichage.retourSurListe) { affichage.liste(); } else {affichage.trombinoscope(); } });
	$('#nouveauButton').bind("click", function() { afficherFormulaireModificationPersonne(-1); });
	$('#personneModif').submit(function(){ var id=validerFormulaireModificationPersonne(this.getAttribute("idP")); if(id!=-1) { choixModifOuAffichage(id); } return false; });
	$("a[name|='evenement']").bind("click",toggleParticipationAEvenement);
	$('input[type=file]').bootstrapFileInput();
}

// Validation du formulaire de modification d'une personne
function validerFormulaireModificationPersonne(id){
	var reponse;
	var parametres={ID:id,VL:0,EP:0};
	
	parametres.NOM=trim($('#inputNom').val());
	parametres.PRENOM=trim($('#inputPrenom').val());
	parametres.VILLE=trim($('#inputVille').val());
	parametres.HOBBY=trim($('#inputHobbys').val());
	parametres.DIVERS=trim($('#inputDivers').val());
	parametres.IDREGION=trim($('#selectRegion').val());
	if ($('#inputVL').is(":checked")) { parametres.VL=1; }
	if ($('#inputEP').is(":checked")) { parametres.EP=1; }
	
	if (parametres.ID==-1) {
		reponse=manager.addPersonne(parametres);
	} else {
		reponse=manager.modPersonne(parametres);
	}
	
	if (reponse.state=="success") {
		if (id==-1) {
			parametres.ID=reponse.insertedID;
			if ((RANK==RANG_USER)||(RANK=RANG_WAITING_USER)) { parametres.IDA=data.user.ID; } else { parametres.IDA=0; }
			data.addPersonne(parametres);
			addAlert("<i>"+parametres.PRENOM+" "+parametres.NOM+"</i> a bien été ajouté(e).",1);
		} else {
			data.modPersonne(id,parametres);
			addAlert("<i>"+parametres.PRENOM+" "+parametres.NOM+"</i> a bien été modifié(e).",1);
		}
		return parametres.ID;
	} else {
		addAlert("La modification a échoué.",0);
	}
	return -1;
}

// Fait basculer Vrai/Faux la participation d'une personne à un évènement
function toggleParticipationAEvenement(){
	var actif=this.getAttribute("actif");
	var ide=this.getAttribute("idE");
	var idp=this.getAttribute("idP");
	var reponse;

	if (actif==0) {
		reponse=manager.addLien(idp,ide);
		if (reponse.state=="success"){
			data.addLien(idp,ide);
			$(this).addClass('list-group-item-success');
			this.setAttribute("actif",1);
		} else {
			addAlert("Échec de la modification",0);
		}
	} else {
		reponse=manager.removeLien(idp,ide);
		if (reponse.state=="success"){
			data.removeLien(idp,ide);
			$(this).removeClass('list-group-item-success');
			this.setAttribute("actif",0);
		} else {
			addAlert("Échec de la modification",0);
		}
	}
}


//suppression d'une personne (force permet d'éviter le prompt)
function delPersonne(id,force){
	var i;
	var personne;
	var reponse;
	force = typeof force !== 'undefined' ? force : false;

	personne=data.getPersonneById(id);
	if (personne!=null){
		if (force||confirm("Confirmez-vous la suppression de "+personne.PRENOM+" "+personne.NOM+" ?")) {
			reponse=manager.delPersonne(personne.ID);
			if ((reponse.state=="success")||(reponse.state=="failed (2)")) {
				data.supprimerLiensDePersonne(personne);
			}
			if (reponse.state=="success"){
				data.personnes.splice(data.personnes.indexOf(personne),1);
				addAlert("<i>"+personne.PRENOM+" "+personne.NOM+"</i> a bien été supprimé(e).",1);
				return true;
			}
		}
	}
	addAlert("<i>"+personne.PRENOM+" "+personne.NOM+"</i> n'a pu être supprimé(e).",0);
	return false;
}

// Retour du chargement d'une image
function loadTrigger(callBack){
	var it;
	var id;
	var photo;
	var nodePhoto;
	var urlImg="./img/";
	if (callBack.state=='success') {
		id=callBack.id;
		photo=callBack.PHOTO;
		it=data.getPersonneById(id);
		if (it!=null) { it.PHOTO=photo; }
		nodePhoto=$("#photo");
		if (nodePhoto.length>0){
			nodePhoto.attr("src", urlImg+photo+".jpg");
		}
	} else addAlert("<strong>Echec !</strong> "+callBack.error,0);
}

// Validation de personnes, avec le bouton dans la liste
function validationPersonnes(){
	var reponse;
	var checkeds=$("input:checked");
	checkeds.each(function(items){
		var id=$(this).attr("idP");
		var p=data.getPersonneById(id);
		if ((p!=null)&&(p.SUG==1)) {
			reponse=manager.validPersonne(p.ID);
			if(reponse.state=="success") { data.validPersonne(p); }
			else { addAlert("Échec lors de la validation de <i>"+p.PRENOM+" "+p.NOM+"</i>",0); }
		}
	});
	affichage.liste();
}

//-------------------------
//      Connexion
//-------------------------

// Gestion de la connexion, lancé par le formulaire de connexion
function doValidConx(){
	var loginInput = $('#loginInput');
	var pwdInput = $('#pwdInput');
	var login=loginInput.val();
	var pwd=pwdInput.val();
	loginInput.val('');
	pwdInput.val('');
	data.user=manager.post('./php/login.php',{pwd:MD5(PWD_SEED+pwd),login:login});
	location.reload();
}

function deconnexion() {
	manager.get("./php/authcheck.php?deco=1",null);
	location.reload();
}

function modifMonCompteForm(){
	var container=$("#mainContent");
	container.empty();
	var source   = $("#mod-monCompte-template").html();
	var template = Handlebars.compile(source);
	affichage.actif="";
	var context={PSEUDO:data.user.PSEUDO, EMAIL:data.user.EMAIL};
	container.append(template(context));
	$('#modMonCompte').submit(function(){ doModMonCompte(); return false; });
}

function doModMonCompte(){
	var reponse;
	var pwd;
	var email=$('#inputEmail').val();
	var pwdInput1=$('#pwd1');
	var pwdInput2=$('#pwd2');
	if (pwdInput1.val()!=pwdInput2.val()) addAlert("Mots de passe différents.",0);
	else {
		pwd=pwdInput1.val();
		pwdInput1.val('');
		pwdInput2.val('');
		reponse=manager.modMonCompte(pwd,email);
		if (reponse.state=="success") {
			addAlert("Les modifications ont été effectuées.",1);
			data.user.EMAIL=email;
		} else {
			addAlert("Les modifications ont échoué.",0);
		}
	}
}

//-------------------------
//      Les données
//-------------------------

var data = {
	user:null,	// ID, rang de l'utilisateur
	usersList:null, // Liste des utilisateurs, pour les admins
	personnes:[], // liste des personnes inscrites
	evenements:[], // Liste des évènements
	lastEventID:null, // ID du dernier évènement 
	regions : [ // Liste des régions
		{ID:0, NOM:"Inconnue"},
		{ID:1, NOM:"Alsace"},
		{ID:2, NOM:"Aquitaine"},
		{ID:3, NOM:"Auvergne"},
		{ID:4, NOM:"Bourgogne"},
		{ID:5, NOM:"Bretagne"},
		{ID:6, NOM:"Centre"},
		{ID:7, NOM:"Franche Comté"},
		{ID:8, NOM:"Ile de France"},
		{ID:9, NOM:"Languedoc Roussillon"},
		{ID:10, NOM:"Lorraine"},
		{ID:11, NOM:"Midi-Pyrénées"},
		{ID:12, NOM:"Nord"},
		{ID:13, NOM:"Normandie"},
		{ID:14, NOM:"Picardie"},
		{ID:15, NOM:"Provence"},
		{ID:16, NOM:"Rhônes Alpes"},
		{ID:17, NOM:"Etranger"}
	],
	filtreMyContribs:null, // Affiche seulement mes contributions
	filtreS:null, // Affiche seulement les éléments dont le statut suggestion est 0/1/Tous(null)
	filtreP:null, // Affiche seulement les éléments qui ont une photo 0/1/Tous(null)
	filtreV:null, // Affiche seulement les éléments qui souhaitent être visibles en ligne 0/1/Tous(null)
	filtreR:null, // Affiche seulement les membres d'une région dont l'id est donné / Tous (null) / Inconnu(-1)
	strFfiltreRegion:'',
	filtreE:null, // Affiche seulement les participant à un évènement / Tous (null)
	filtreEvent:null,
	activeSearch:false, // Indique si une recherche est en cours
	strSearch:'', // Chaîne sur laquelle la recherche a été faite
	listeAAfficher:[], // Liste des éléments à afficher
	pAP:null // liste des personnes ayant fourni leur photo (pour l'interface réduite)
}

data.load=function(){
	var i;
	var getData;
	var liens;
	var personne,ev;
	
	getData=manager.get("./php/getData.php",null);
	this.user=getData.user;
	this.personnes=getData.personnes;
	this.evenements=getData.evenements;
	liens=getData.liens;

	for (i=0;i<this.personnes.length;i++) {
		this.personnes[i].liens=[];
		this.personnes[i].flag=false;
		this.personnes[i].MAJ=majSansAccent(this.personnes[i].NOM+this.personnes[i].PRENOM);
		this.personnes[i].MAJ2=majSansAccent(this.personnes[i].PRENOM+this.personnes[i].NOM);
	}
	this.personnes.sort(comparePersonnesAlpha);

	for (i=0;i<this.evenements.length;i++) this.evenements[i].liens=[];
	
	for (i=0;i<liens.length;i++) {
		personne=this.getPersonneById(liens[i].IDP);
		ev=this.getEvenementById(liens[i].IDE);
		if ((personne!=null)&&(ev!=null)) {
			personne.liens.push(ev);
			ev.liens.push(personne);
		}
	}
	
	for(i=0;i<this.evenements.length;i++) this.evenements[i].liens.sort(comparePersonnesAlpha);	
}

data.getPersonneById=function(idP){
	var i;
	
	for(i=0;i<this.personnes.length;i++) {
		if (this.personnes[i].ID==idP) {
			return this.personnes[i];
		}
	}
	return null;
}

data.getUserById=function(idU){
	var i;
	if (this.usersList!=null) {
		for(i=0;i<this.usersList.length;i++) {
			if (this.usersList[i].ID==idU) {
				return this.usersList[i];
			}
		}
	}
	return null;
}

data.getEvenementById=function(idE){
	var i;
	
	for(i=0;i<this.evenements.length;i++) {
		if (this.evenements[i].ID==idE) {
			return this.evenements[i];
		}
	}
	return null;
}

data.getRegionById=function(idR){
	//var i;
	
	/*for(i=0;i<this.regions.length;i++) {
		if (this.regions[i].ID==idR) {
			return this.regions[i];
		}
	}*/
	if ((idR>=0)&&(idR<this.regions.length)){
		return this.regions[idR];
	}
	// À défaut, on renvoie l'item "inconnu"
	return this.regions[0];
}

// Ajoute un lien entre une personne et un évènement
data.addLien=function(personne,evenement){
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	evenement = typeof evenement == 'object' ? evenement : this.getEvenementById(evenement);
	if ((personne!=null)&&(evenement!=null)) {
		personne.liens.push(evenement);
		evenement.liens.push(personne);
		evenement.liens.sort(comparePersonnesAlpha);
	}
}

// Supprime un lien entre une personne et un évènement
data.removeLien=function(personne,evenement){
	var index;
	
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	evenement = typeof evenement == 'object' ? evenement : this.getEvenementById(evenement);
	if ((personne!=null)&&(evenement!=null)) {
		index=personne.liens.indexOf(evenement)
		while (index>=0) {
			personne.liens.splice(index,1);
			index=personne.liens.indexOf(evenement)
		}
		index=evenement.liens.indexOf(personne);
		while (index>=0) {
			evenement.liens.splice(index,1);
			index=evenement.liens.indexOf(personne);
		}
	}
}

// Ajoute un évènement
data.addEvent=function(submitedEvent){
	if (typeof submitedEvent.liens=='undefined') submitedEvent.liens=[];
	this.evenements.push(submitedEvent);
}

// Modifie un évènement
data.modEvent=function(evenement,submitedChange){
	var attr;
	evenement = typeof evenement == 'object' ? evenement : this.getEvenementById(evenement);
	for (attr in submitedChange) {
		evenement[attr]=submitedChange[attr];
	}
}

// Ajoute une personne - ID, NOM et PRENOM requis
data.addPersonne=function(submitedPersonne){
	if (typeof submitedPersonne.liens=='undefined') submitedPersonne.liens=[];
	if (typeof submitedPersonne.VL=='undefined') submitedPersonne.VL=0;
	if (typeof submitedPersonne.EP=='undefined') submitedPersonne.EP=0;
	if (typeof submitedPersonne.IDREGION=='undefined') submitedPersonne.IDREGION=-1;
	if (typeof submitedPersonne.VILLE=='undefined') submitedPersonne.VILLE='';
	if (typeof submitedPersonne.HOBBY=='undefined') submitedPersonne.HOBBY='';
	if (typeof submitedPersonne.DIVERS=='undefined') submitedPersonne.DIVERS='';
	submitedPersonne.flag=false;
	submitedPersonne.MAJ=majSansAccent(submitedPersonne.NOM+submitedPersonne.PRENOM);
	submitedPersonne.MAJ2=majSansAccent(submitedPersonne.PRENOM+submitedPersonne.NOM);
	submitedPersonne.DATE=getDate();
	submitedPersonne.HEURE=getTime();
	submitedPersonne.SUG = RANK>=RANG_ADMIN ? 0 : 1;
	submitedPersonne.PHOTO='';
	this.personnes.push(submitedPersonne);
	this.personnes.sort(comparePersonnesAlpha);
}

// Modifie une personne - ID requis
data.modPersonne=function(personne,submitedChange){
	var attr;
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	for (attr in submitedChange) {
		personne[attr]=submitedChange[attr];
	}
	personne.MAJ=majSansAccent(personne.NOM+personne.PRENOM);
	personne.MAJ2=majSansAccent(personne.PRENOM+personne.NOM);
	personne.DATE=getDate();
	personne.HEURE=getTime();
	this.personnes.sort(comparePersonnesAlpha);
}

// Supprime une personne
data.delPersonne=function(personne){
	var index;
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	if (personne!=null) {
		index=this.personnes.indexOf(personne);
		if (index!=-1) { this.personnes.splice(index,1); }
	}
}

// Supprime les liens d'une personne (et donc les liens contraires depuis les évènements)
data.supprimerLiensDePersonne=function(personne){
	var i;
	var ev;
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	if (personne!=null) {
		while (0<personne.liens.length){
			ev=personne.liens[0];
			i=0;
			while(i<ev.liens.length){
				if(ev.liens[i]==personne) { ev.liens.splice(i,1); }
				else { i++; }
			}
		personne.liens.splice(0,1);
		}
	}
}

// Filtrage selon la recherche
data.filtrerSelonRecherche=function(str){
	var i;
	str=trim(str);
	if (str!=''){
		this.setFilter(true,null);
		this.activeSearch=true;
		this.strSearch=str;
		str=majSansAccent(str);
		this.listeAAfficher.length=0;
		for(i=0;i<this.personnes.length;i++){
			if((this.personnes[i].MAJ.search(str) !== -1)||(this.personnes[i].MAJ2.search(str) !== -1))  this.listeAAfficher.push(this.personnes[i]);
		}
	}
}

// Enregistre les nouveaux paramètres de filtrage
data.setFilter=function(restoreDefault,parametres){
	var attr, txtToAppend;
	var region=null;
	if (restoreDefault) {
		this.filtreS=null;
		this.filtreP=null;
		this.filtreV=null;
		this.filtreR=null;
		this.filtreE=null;
		this.filtreMyContribs=null;
		this.activeSearch=false;
	}
	if (parametres!=null){
		for (attr in parametres) {
			this[attr]=parametres[attr];
			if (this.filtreE!=null) { this.filtreEvent=this.getEvenementById(this.filtreE); }
			else { this.filtreEvent=null; }
			if (this.filtreR!=null) {
				region=this.getRegionById(this.filtreR);
			}
			if (region!=null) { this.strFiltreRegion=region.NOM; }
			else { this.strFiltreRegion=''; }
		}
	}
}

// Effectue un filtrage en fonction du filtre actif
// L'argument indique s'il faut commencer le filtrage avec la totalité des personnes
data.applyFilter=function(restoreListe){
	var i;
	var it;
	var hasPhoto;
	
	if (restoreListe){
		this.listeAAfficher.length=0;
		for (i=0;i<this.personnes.length;i++) {
			this.listeAAfficher.push(this.personnes[i]);
		}
	}
	
	i=0;
	while (i<this.listeAAfficher.length){
		it=this.listeAAfficher[i];
		hasPhoto=(it.PHOTO!='');
		if (	((this.filtreS!=null) && (this.filtreS!=it.SUG)) ||
				((this.filtreMyContribs!=null) && (data.user.ID!=it.IDA)) ||
				((this.filtreP!=null) && (this.filtreP!=hasPhoto)) ||
				((this.filtreV!=null) && (this.filtreV!=it.VL)) ||
				((this.filtreR!=null) && (this.filtreR!=it.IDREGION)) ||
				((this.filtreE!=null) && (it.liens.indexOf(this.filtreEvent)==-1)) ) {
			this.listeAAfficher.splice(i,1);
		} else {
			i++;
		}
	}
}

// Renvoie la personne suivante dans la liste
data.getNext=function(personne){
	var index;
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	index=this.listeAAfficher.indexOf(personne);
	if (index==-1) { return null; }
	else {
		index++;
		if (index>=this.listeAAfficher.length) index=0;
	}
	return this.listeAAfficher[index];
}

// Renvoie la personne précédente dans la liste
// forceElse oblige à renvoyer une valeur différente de personne, ou null
// (utilisé pour passer à un autre lors d'une suppression)
data.getPrev=function(personne,forceElse){
	var index;
	var retour;
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	index=this.listeAAfficher.indexOf(personne);
	if (index==-1) { return null; }
	else {
		index--;
		if (index<0) index=this.listeAAfficher.length-1;
	}
	retour=this.listeAAfficher[index];
	if ((retour==personne)&&(forceElse)) { return null; }
	else { return retour; }
}

// Validation d'une personne
data.validPersonne=function(personne){
	personne = typeof personne == 'object' ? personne : this.getPersonneById(personne);
	if (personne!=null) { personne.SUG=0; }
}

//------------------------
// Fusion
//------------------------

var mergeItem={liste:[], resultat:null, currentAttribute:null} // objet personne servant à la fusion

function checkItemsToMerge(){
	mergeItem.liste.length=0;
	mergeItem.resultat={};
	var checkeds=$("input:checked");
	checkeds.each(function(items){
		var id=$(this).attr("idP");
		var p=data.getPersonneById(id);
		if (p!=null) mergeItem.liste.push(p);
	});
	mergeItem.liste.sort(comparePersonnesDate); // Pour les VL et EP, on prendra ceux du dernier en date
	if (mergeItem.liste.length>1) checkItemsDiff();
}

function checkItemsDiff(){
	var i,j,index;
	var attrValue, attr,diffFound;
	var listeAttr=["NOM", "PRENOM", "VILLE", "IDREGION", "DIVERS", "HOBBY", "PHOTO"];
	var reponse;
	var flagErreur=false;
	var personneAUpdater;
	i=-1;
	diffFound=false;
	while (i<listeAttr.length-1 && !diffFound){
		i++;
		var attr=listeAttr[i];
		if (typeof(mergeItem.resultat[attr])=='undefined') {
			attrValue=mergeItem.liste[0][attr];
			index=0;
			for (j=1;j<mergeItem.liste.length;j++) {
				if ((attrValue!=mergeItem.liste[j][attr])&&(mergeItem.liste[j][attr]!="")) {
					if(attrValue=="") {
						attrValue=mergeItem.liste[j][attr];
						index=j;
					} else {
						diffFound=true;
						break;
					}
				}
			}
			if (!diffFound) {
				mergeItem.resultat[attr]=attrValue;
				if (attr=="PHOTO") { mergeItem.resultat.ID=mergeItem.liste[index].ID; } // La photo est solidaire de l'id
			}
		}
	}
	if (diffFound) askForGoodAttribute(attr);
	else {
		var lastItemToMerge=mergeItem.liste[mergeItem.liste.length-1];
		mergeItem.resultat.VL=lastItemToMerge.VL;
		mergeItem.resultat.EP=lastItemToMerge.EP;

		// Dans tous les cas, un id a été retenu, selon la photo. Ce élément est gardé, les autres sont effacés.
		personneAUpdater=data.getPersonneById(mergeItem.resultat.ID);
		i=mergeItem.liste.indexOf(personneAUpdater);
		if (i>=0) mergeItem.liste.splice(i,1);
		
		for (i=0;i<mergeItem.liste.length;i++) {
			// Il faut ajouter tous les liens vers l'item à updater
			for (j=0;j<mergeItem.liste[i].liens.length;j++){
				if (personneAUpdater.liens.indexOf(mergeItem.liste[i].liens[j])==-1) {
					reponse=manager.addLien(personneAUpdater.ID,mergeItem.liste[i].liens[j].ID);
					if (reponse.state=="success") { data.addLien(personneAUpdater.ID,mergeItem.liste[i].liens[j].ID); }
					else { flagErreur=true; }
				}
			}
			reponse=manager.delPersonne(mergeItem.liste[i].ID);
			if (reponse.state=="success") {	data.delPersonne(mergeItem.liste[i]); }
			else { flagErreur=true; }
		}
		mergeItem.liste.length=0;
		
		var reponse=manager.modPersonne(mergeItem.resultat);
		if (reponse.state=="success"){
			data.modPersonne(personneAUpdater,mergeItem.resultat);
			choixModifOuAffichage(mergeItem.resultat.ID);
		} else {
			flagErreur=true;
		}
		if (flagErreur) { addAlert("La fusion ne s'est pas terminée correctement",0); }
		mergeItem.resultat=null;
	}
}

function askForGoodAttribute(attr){
	var container=$("#mainContent");
	var context={items:[]};
	var i;

	affichage.actif="";
	
	mergeItem.currentAttribute=attr;
	
	// Création du contexte
	if (attr=="PHOTO") {
		for(i=0;i<mergeItem.liste.length;i++){
			context.items.push({ID:mergeItem.liste[i].ID, PHOTO:mergeItem.liste[i].PHOTO});
		}		
	} else {
		if (attr=="IDREGION") {
			context.NOM="REGION";
			for(i=0;i<mergeItem.liste.length;i++){
				context.items.push({ID:mergeItem.liste[i].ID, VALEUR:data.getRegionById(mergeItem.liste[i].IDREGION).NOM, IDR:mergeItem.liste[i].IDREGION, DATE:shortDateFormat(mergeItem.liste[i].DATE)});
			}
		} else {
			context.NOM=attr;
			for(i=0;i<mergeItem.liste.length;i++){
				context.items.push({ID:mergeItem.liste[i].ID, VALEUR:mergeItem.liste[i][attr], DATE:shortDateFormat(mergeItem.liste[i].DATE)});
			}
		}
	}
	
	// Application au template handlebars
	var source;
	if (attr=="PHOTO") { source=$("#photo-fusion-template").html(); }
	else { source   = $("#attribute-fusion-template").html(); }
	var template = Handlebars.compile(source);

	container.empty();
	container.append(template(context));

	// Création des évènements
	$("input[type=radio]").bind("click",function(){ var node=$('#chk-'+this.value); mergeItem.selectAttribute(node.val());} );
	$("#chooseGoodAttribute").submit(function() {checkItemsDiff(); return false; });
	$("#cancelButton").bind("click",function(){ mergeItem.resultat=null; affichage.liste(); });
	$("a[name|='photo']").bind("click",function(){ var personne=data.getPersonneById(this.getAttribute('idP')); if (personne!=null) { mergeItem.resultat.ID=personne.ID; mergeItem.resultat.PHOTO=personne.PHOTO; }; checkItemsDiff(); });
}

mergeItem.selectAttribute=function(val){
	this.resultat[this.currentAttribute]=val;
}

//----------------------------------
// Affichage liste et trombinoscope
//----------------------------------
// Objet contenant les fonctions d'affichage des listes de nom et des trombinoscopes
var affichage = {
	actif:"", // Indique quel type d'affichage est en cours
	pageActiveTrombi:0, // Numéro de page courante si nécessaire
	pageActiveListe:0,
	photosParLigne:5, // Nombres de photos sur une ligne de trombinoscope
	photosParPage:25, // Nombres de photos par page de trombinoscope
	itemsParPage:50, // Nombres de personnes par ligne avec l'affichage tableau / Tous(null)
	divFiltres:null, // div contenant les filtres
	retourSurListe:true, // Indique si le retour se fait sur liste ou sur trombi
	usersPage:0 // Page courante de l'affichage des utilisateurs
}

// affichage d'éventuels filtres
affichage.displayFilter=function(container){
	var txtToAppend="";
	var context={items:[]};
	
	if ((data.filtreS!=null)||(data.filtreMyContribs!=null)||(data.filtreP!=null)||(data.filtreV!=null)||(data.filtreR!=null)||(data.filtreE!=null)||(data.activeSearch)){
		txtToAppend+="<span class='glyphicon glyphicon-filter'></span>";
		if (data.filtreS==1) {	context.items.push({className:'label-warning', text:'Non validés', f:'S'}); }
		else { if(data.filtreS==0) { context.items.push({className:'label-success', text:'Validés', f:'S'}); } }
		if (data.filtreP==0) {	context.items.push({className:'label-warning', text:'Pas de photo', f:'P'}); }
		else { if(data.filtreP==1) { context.items.push({className:'label-success', text:'Photo présente', f:'P'}); }}
		if (data.filtreV==0) {	context.items.push({className:'label-warning', text:'Invisible en ligne', f:'V'}); }
		else { if(data.filtreV==1) { context.items.push({className:'label-success', text:'Visible en ligne', f:'V'}); }}
		
		if (data.filtreMyContribs) { context.items.push({className:'label-info', text:'Mes photos', f:'MC'}); }
		if (data.filtreR!=null) { context.items.push({className:'label-info', text:data.strFiltreRegion, f:'R'}); }
		if ((data.filtreE!=null)&&(data.filtreEvent!=null)) {context.items.push({className:'label-info', text:data.filtreEvent.NOM, f:'E'}); }
		
		if (data.activeSearch) { context.items.push({className:'label-success', text:"Recherche : "+data.strSearch, f:'Se'}); }
	}

	var source=$("#filtres-template").html();
	var template = Handlebars.compile(source);
	container.append(template(context));
	$("a[name='filtre']").bind("click",function(){ delFilter(this.getAttribute('f')); return false; });
}

// Création de l'interface permettant de modifier une personne
affichage.personne=function(id){
	var container=$("#mainContent");
	var context={};
	var personne, nouvelEvenement;
	var i;
	
	this.actif="affichage personne";

	if (typeof id == 'object') {
		if (id==null) {
			personne=null;
			id=-1;
		} else {
			personne=id;
			id=personne.ID;
		}
	} else {
		personne=data.getPersonneById(id);
	}

	if (personne!=null) {
		//--- Création du contexte -------
		context.ID=personne.ID;
		context.NOM=personne.NOM;
		context.PRENOM=personne.PRENOM;
		context.VILLE=personne.VILLE;
		context.HOBBY=personne.HOBBY;
		context.DIVERS=personne.DIVERS;
		context.affCommandes=true;
		if (personne.PHOTO!='') { context.PHOTO=personne.PHOTO; }
		context.REGION=data.getRegionById(personne.IDREGION).NOM;

		context.evenements=[];
		for (i=0;i<data.evenements.length;i++) {
			nouvelEvenement={IDE:data.evenements[i].ID, NOM:data.evenements[i].NOM};
			if (personne.liens.indexOf(data.evenements[i])>=0) { nouvelEvenement.actif=true; }
			context.evenements.push(nouvelEvenement);
		}

		//--- Applcation du contexte au template handlebars -------
		
		var source   = $("#affichage-personne-template").html();
		var template = Handlebars.compile(source);

		container.empty();
		affichage.displayFilter(container);
		container.append(template(context));

		//--- Création des évènements  -------
		$('#precButton').bind("click", function() { choixModifOuAffichage(data.getPrev(this.getAttribute("idP"),false)); });
		$('#nextButton').bind("click", function() { choixModifOuAffichage(data.getNext(this.getAttribute("idP"))); });
		$('#retourButton').bind("click", function() { affichage.setPageActive(this.getAttribute("idP")); if (affichage.retourSurListe) { affichage.liste(); } else {affichage.trombinoscope(); } });
		$("a[name|='evenement']").bind("click",function(){
			data.setFilter(true,{filtreE:this.getAttribute("idE")});
			data.applyFilter(true);
			affichage.setPageActive(this.getAttribute("idP"));
			if (affichage.retourSurListe) { affichage.liste(); }
			else {affichage.trombinoscope(); }
		});
	}
}

// Donne la liste des éléments à afficher en tenant compte du filtrage
// Affiche les éléments de la liste
affichage.liste=function(){
	var i,Npage,rangInitial,rangFinal;
	var container=$("#mainContent");
	var it;
	var context={};
	var itToAdd, nouvellePage;

	this.actif="liste";

	this.retourSurListe=true; 

	//--- Création du contexte -------
	
	// Pagination
	if (this.itemsParPage!=null) {
		Npage=Math.floor(data.listeAAfficher.length/this.itemsParPage);
		if (Npage>0){
			context.pages=[];
			if (this.pageActiveListe<=0) { context.premierePage=true; }
			for (i=0;i<=Npage;i++) {
				nouvellePage={index:i};
				if (i==this.pageActiveListe) { nouvellePage.active=true; }
				context.pages.push(nouvellePage);
			}
			if (this.pageActiveListe>=Npage) { context.dernierePage=true; }
		}
		rangInitial=this.pageActiveListe*this.itemsParPage;
		rangFinal=Math.min(rangInitial+this.itemsParPage,data.listeAAfficher.length);
	} else {
		rangInitial=0;
		rangFinal=data.listeAAfficher.length;
	}
	
	// Boutons
	if (RANK>=RANG_ADMIN) {
		context.fusionButton=true;
		context.validationButton=true;
	}
	
	context.personnes=[];
	for (i=rangInitial;i<rangFinal;i++){
		it=data.listeAAfficher[i];
		itToAdd = {ID:it.ID, NOM:it.NOM, PRENOM:it.PRENOM, VILLE:it.VILLE, IDREGION:it.IDREGION, nomRegion:data.getRegionById(it.IDREGION).NOM};
		if (it.PHOTO!='') { itToAdd.PHOTO=it.PHOTO; }
		if (it.SUG==1) { itToAdd.SUG=1; }
		if ((RANK>=RANG_ADMIN)||(it.SUG==1)||(it.IDA==data.user.ID)) { itToAdd.writable=true; }
		context.personnes.push(itToAdd);
	}

	//--- Applcation du contexte au template handlebars -------
	
	var source   = $("#liste-personne-template").html();
	var template = Handlebars.compile(source);

	container.empty();
	affichage.displayFilter(container);
	container.append(template(context));
	
	//--- Création des évènements  -------
	
	$("a[name|='affEdit']").bind("click",function(){ choixModifOuAffichage(this.getAttribute('idP')); return false;});
	$("a[name|='del']").bind("click",function(){ var id=this.getAttribute('idP'); if (delPersonne(id)) { $("#tr"+id).remove(); } return false;});
	$("a[name|='region']").bind("click",function(){ var idR=this.getAttribute('idR'); data.setFilter(false,{filtreR:idR}); data.applyFilter(false); affichage.liste(); return false;});
	$("#pagePrecedente").bind("click",function(){affichage.pageActiveListe--; affichage.liste(); return false;});
	$("#pageSuivante").bind("click",function(){affichage.pageActiveListe++; affichage.liste(); return false;});
	$("a[name|='page']").bind("click",function(){ affichage.pageActiveListe=this.getAttribute('index'); affichage.liste(); return false;});
	$('#fusionButton').bind("click",checkItemsToMerge);
	$('#validationButton').bind("click",validationPersonnes);
}

// Spécifie la page à afficher en fonction de l'utilisateur
affichage.setPageActive=function(personne){
	var rang=-1;
	personne = typeof personne == 'object' ? personne : data.getPersonneById(personne);
	if (personne!=null) { rang=data.listeAAfficher.indexOf(personne); }
	
	if ((personne==null)||(rang==-1)) {
		this.pageActiveTrombi=0;
		this.pageActiveListe=0;
	} else {
		if (this.photosParPage != null) { this.pageActiveTrombi=Math.floor(rang/this.photosParPage); }
		else { this.pageActiveTrombi=0; }
		if (this.itemsParPage!= null) { this.pageActiveListe=Math.floor(rang/this.itemsParPage); }
		else { this.pageActiveListe=0; }
	}
}

// Affichage du trombinoscope
affichage.trombinoscope=function(){
	var container=$("#mainContent");
	var it, nouvellePage, nouvelleLigne, nouvellePersonne;
	var i, Npage;
	var context={};

	this.actif="trombinoscope";

	this.retourSurListe=false;
	
	//--- Création du contexte -------
	
	// Pagination
	if (this.photosParPage!=null) {
		Npage=Math.floor(data.listeAAfficher.length/this.photosParPage);
		if (Npage>0){
			context.pages=[];
			if (this.pageActiveTrombi<=0) { context.premierePage=true; }
			for (i=0;i<=Npage;i++) {
				nouvellePage={index:i};
				if (i==this.pageActiveTrombi) { nouvellePage.active=true; }
				context.pages.push(nouvellePage);
			}
			if (this.pageActiveTrombi>=Npage) { context.dernierePage=true; }
		}
		rangInitial=this.pageActiveTrombi*this.photosParPage;
		rangFinal=Math.min(rangInitial+this.photosParPage,data.listeAAfficher.length);
	} else {
		rangInitial=0;
		rangFinal=data.listeAAfficher.length;
	}
	
	context.lignes=[];
	nouvelleLigne={personnes:[]};
	for (i=rangInitial;i<rangFinal;i++){
		it=data.listeAAfficher[i];
		nouvellePersonne={ID:it.ID, NOM:it.NOM, PRENOM:it.PRENOM};
		if (it.PHOTO!="") { nouvellePersonne.PHOTO=it.PHOTO; }
		if (it.SUG==1) { nouvellePersonne.SUG=1; }
		nouvelleLigne.personnes.push(nouvellePersonne);
		if (nouvelleLigne.personnes.length==this.photosParLigne) {
			context.lignes.push(nouvelleLigne);
			nouvelleLigne={personnes:[]};
		}
	}
	if (nouvelleLigne.personnes.length>0) {
		context.lignes.push(nouvelleLigne);
	}

	//--- Applcation du contexte au template handlebars -------
	
	var source   = $("#trombinoscope-personne-template").html();
	var template = Handlebars.compile(source);

	container.empty();
	affichage.displayFilter(container);
	container.append(template(context));

	//--- Création des évènements  -------
	
	$("a[name|='photo']").bind("click",function(){ choixModifOuAffichage(this.getAttribute('idP')); return false;});
	$("#pagePrecedente").bind("click",function(){affichage.pageActiveTrombi--; affichage.trombinoscope(); return false;});
	$("#pageSuivante").bind("click",function(){affichage.pageActiveTrombi++; affichage.trombinoscope(); return false;});
	$("a[name|='page']").bind("click",function(){ affichage.pageActiveTrombi=this.getAttribute('index'); affichage.trombinoscope(); return false;});
	
	
}

// Fonction qui affiche simplement la liste de ceux dont on a déjà la photo
affichage.personnesAvecPhoto=function(page){
	page = typeof page !== 'undefined' ? page : 0;
	var i;
	var container=$("#mainContent");
	var context={};
	var N;
	var nPage;
	
	this.actif="";

	// Acquisition de la liste des personnes ayant fourni leur photo
	manager.getListeAP();
	if (data.pAP!=null) {
		// Préparation du contexte
		N=data.pAP.length;
		if (page*this.itemsParPage>N) { page=0; }
		context.personnes=[];
		for (i=page*this.itemsParPage;i<Math.min(N,(page+1)*this.itemsParPage);i++) { context.personnes.push(data.pAP[i]); }
		if (this.itemsParPage<N) {	// création de la pagination
			context.pages=[];
			for (i=0;i<=Math.floor(N/this.itemsParPage);i++) {
				nPage={index:i};
				if (i==page) { nPage.active=true; }
				context.pages.push(nPage);
			}
		}
		
		// Application du context au template handlebars
		
		var source   = $("#listeAvecPhoto-personne-template").html();
		var template = Handlebars.compile(source);
		container.empty();
		container.append(template(context));
		
		// Création des évènements
		$("a[name|='page']").bind("click",function(){ affichage.personnesAvecPhoto(this.getAttribute('index')); return false;});

	}
}

// Fonction qui affiche simplement la liste de ceux dont on a déjà la photo
affichage.listeUsers=function(page){
	this.usersPage = typeof page !== 'undefined' ? page : this.usersPage;
	var i;
	var container=$("#mainContent");
	var context={};
	var N;
	var nPage;
	var reponse;
	
	this.actif="";

	// Acquisition de la liste des personnes ayant fourni leur photo
	if (data.usersList==null) {
		reponse=manager.getUsersList();
		if (reponse.state=="success") {
			data.usersList = reponse.liste;
			for (i=0;i<data.usersList.length;i++) {
				data.usersList[i].rankName = rankName(data.usersList[i].RANK);
				if ((data.usersList[i].RANK<RANK) || (data.usersList[i].ID==data.user.ID)) { data.usersList[i].editable=true; }
				if (data.usersList[i].RANK<RANK) { data.usersList[i].deletable=true; }
				data.usersList[i].shortDate=shortDateFormat(data.usersList[i].DATE);
			}
		} else {
			addAlert("La liste des utilisateurs n'a pas pu être récupérée.",0);
		}
	}
	if (data.usersList!=null) {
		// Préparation du contexte
		N=data.usersList.length;
		if (this.usersPage*this.itemsParPage>N) { this.usersPage=0; }
		context.users=[];
		for (i=this.usersPage*this.itemsParPage;i<Math.min(N,(this.usersPage+1)*this.itemsParPage);i++) { context.users.push(data.usersList[i]); }
		if (this.itemsParPage<N) {	// création de la pagination
			context.pages=[];
			for (i=0;i<=Math.floor(N/this.itemsParPage);i++) {
				nPage={index:i};
				if (i==this.usersPage) { nPage.active=true; }
				context.pages.push(nPage);
			}
		}
		// Application du context au template handlebars
		
		var source   = $("#liste-users-template").html();
		var template = Handlebars.compile(source);
		container.empty();
		container.append(template(context));
		
		// Création des évènements
		$("a[name|='page']").bind("click",function(){ affichage.listeUsers(this.getAttribute('index')); return false;});
		$("a[name|='edit']").bind("click",function(){ addModUser(this.getAttribute('idU')); return false;});
		$("a[name|='del']").bind("click",function(){ delUser(this.getAttribute('idU')); return false;});
		$("a[name='addU']").bind("click",function(){ addModUser(-1); return false;});

	}
}

//----------------------
// Tri
//----------------------
// Tri par ordre alphabétique NOM, PRENOM
function comparePersonnesAlpha(a,b){
	if (a.MAJ<b.MAJ) {
		return -1;
	} else {
		if(a.MAJ==b.MAJ) {
			return 0;
		} else {
			return 1;
		}
	}
}

// Tri par ordre chronologique DATE, HEURE
function comparePersonnesDate(a,b){
	var dA=a.DATE+a.HEURE;
	var dB=b.DATE+b.HEURE;
	if (dA<dB) {
		return -1;
	} else {
		if(dA==dB) {
			return 0;
		} else {
			return 1;
		}
	}
}

//------------------------
// manager BDD
//------------------------

var manager={
	xhr:null
}

// Création d'un objet XMLHttpRequest pour requête ajax
manager.getXhr=function(){ 
	this.xhr=null;
	if(window.XMLHttpRequest) this.xhr = new XMLHttpRequest();
	else if(window.ActiveXObject){
		try{
			this.xhr = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			this.xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
	} else {
		addAlert("Votre navigateur ne supporte pas les requêtes AJAX.",0);
	}
}

// Envoi d'une requête POST synchrone
manager.post=function(url,parameters){
	var strParameters="";
	var attr;
	
	if (this.xhr==null) this.getXhr();
	if (this.xhr!=null) {
		if (parameters!=null) for (attr in parameters) {
			if (strParameters!="") strParameters+="&";
			if (typeof(parameters[attr])=='string') parameters[attr]=enleveCarSpec(parameters[attr]);
			strParameters+=attr+"="+encodeURIComponent(parameters[attr]);
		}
	
		this.xhr.open('POST',url,false);
		this.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		this.xhr.send(strParameters);
	
		return eval(this.xhr.responseText);
	} else return null;
}

// Envoi d'une requête SEND synchrone
manager.get=function(url,parameters) {
	var strParameters="";
	var attr;
	if (this.xhr==null) this.getXhr();
	if (this.xhr!=null) {
		if (parameters!=null) for (var attr in parameters) {
			if (strParameters!="") strParameters+="&";
			strParameters+=attr+"="+parameters[attr];
		}
		if (strParameters!="") strParameters="?"+strParameters;
			
		this.xhr.open('GET',url+strParameters,false);
		this.xhr.send(null);
		
		return eval(this.xhr.responseText);
	} else return null;
}

// Récupère la liste des utilisateurs
manager.getUsersList=function(){
	return this.get('./php/getUsersList.php',null);
}

// Ajoute un lien entre personne et évènement en BDD
manager.addLien=function(idp,ide){
	return this.post('./php/addLien.php',{IDP:idp,IDE:ide});
}

// Enlève un lien entre personne et évènement en BDD
manager.removeLien=function(idp,ide){
	return this.post('./php/removeLien.php',{IDP:idp,IDE:ide});
}

// Ajoute (id=-1) ou modifie (id>=0) un évènement en BDD
manager.addModEvenement=function(id,nom){
	return this.post('./php/modEvenement.php',{id:id,nom:nom});
}

// Modifie une personne en BDD - ID requis au minimum
manager.modPersonne=function(parameters){
	return this.post('./php/modPersonne.php',parameters);
}

// Ajoute une personne en BDD - NOM et PRENOM requis au minimum
manager.addPersonne=function(parameters){
	return this.post('./php/addPersonne.php',parameters);
}

// Supprime une personne de la BDD
manager.delPersonne=function(id){
	return this.post('./php/delPersonne.php',{id:id});
}

// Modifie le compte dans la BDD
manager.modMonCompte=function(newPwd,email){
	return this.post('./php/modMonCompte.php',{pwd:MD5(PWD_SEED+newPwd),email:email});
}

// ajoute ou modifie un compte en BDD
manager.addModUser=function(pseudo, email, pwd, rank, id){
	var params={pseudo:pseudo, email:email, pwd:pwd, id:id};
	if (rank!=0) { params.rank=rank; }
	return this.post('./php/addModUser.php',params);
}

// Suppression utilisateur en BDD
manager.delUser=function(id){
	return this.post('./php/delUser.php',{id:id});
}


// Demande la liste des personnes avec photo
manager.getListeAP=function(){
	var reponse;
	var i;
	if (data.pAP==null) {
		reponse=this.get("./php/personnesAvecPhoto.php",null);
		if (reponse.state=='success') {
			data.pAP=reponse.liste;
			for (i=0;i<data.pAP.length;i++) {
				data.pAP[i].DATE=shortDateFormat(data.pAP[i].DATE);
			}
		}
		else { addAlert("Échec du chargement de la liste.",0); }
	}
}

// Validation d'une personne
manager.validPersonne=function(id){
	return this.post('./php/validPersonne.php',{ID:id});
}
//------------------------
// Typographique et divers
//------------------------

function enleveCarSpec(str){
	var reg1 = new RegExp("\n","g");
	var reg2 = new RegExp('"',"g");
	str.replace(reg1,"&#13;");
	return str.replace(reg2,"&#34;");
}

// Heure au format HH::mm:ss
function getTime() {
	var d=new Date();
	return d.getHours()+":"+d.getMinutes()+":"+d.getSeconds();
}

// Date au format YY-mm-jj
function getDate(){
	var d=new Date();
	return d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate();
}

// Ecrire la date au format jj/mm/aa
function shortDateFormat(date){
	var tab=date.split('-');
	return tab[2]+"/"+tab[1]+"/"+tab[0];
}

// Affiche un message
function addAlert(mess,type){
	var cl="";
	var sign;
	if (type==1) { cl=" alert-success"; sign="<span class='glyphicon glyphicon-ok'></span>"; }
	else { cl=" alert-warning"; sign="<span class='glyphicon glyphicon-warning-sign'></span>"; }
	var node=$("#alertes");
	node.append("<div id='al"+alCount+"'class='alert"+cl+"'><button type='button' class='close' data-dismiss='alert'>&times;</button>"+sign+" "+mess+"</div>");	
	//node.children().last().fadeOut(2000,function(){$(this).remove();});
	setTimeout(clearAlert, 2000, alCount);
	alCount++;
}

// Effacement du message
function clearAlert(id){
	$('#al'+id).fadeOut('slow',function(){$(this).remove();});
}

// Passer en majuscule en retirant les caractères accentués
function majSansAccent(myString){
	var correctedString = myString.toUpperCase();
	correctedString = correctedString.replace(/[ÁÀÄÂ]/, 'A');
	correctedString = correctedString.replace(/[ÉÈËÊ]/, 'E');
	correctedString = correctedString.replace(/[ÍÌÏÎ]/, 'I');
	correctedString = correctedString.replace(/[ÓÒÖÔ]/, 'O');
	correctedString = correctedString.replace(/[' ]/, '');
	return correctedString.replace(/[ÚÙÜÛ]/, 'U');
}

// Enlève les espaces atour de la chaîne
function trim(chaine){
	 return chaine.replace(/(^\s*)|(\s*$)/g, ""); 
}

// Échappement des '
function echappement(str){
  var reg = new RegExp("'","g");
  return(str.replace(reg,"&#39;"));
}


/* ----------------------------------------------------------------------
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Copyright (C) Paul Johnston 1999 - 2000.
 * Updated by Greg Holt 2000 - 2001.
 * See http://pajhome.org.uk/site/legal.html for details.
----------------------------------------------------------------------- */

var hex_chr = "0123456789abcdef";
function rhex(num) {
// Convert a 32-bit number to a hex string with ls-byte first
  str = "";
  for(j = 0; j <= 3; j++)
    str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) +
           hex_chr.charAt((num >> (j * 8)) & 0x0F);
  return str;
}

function str2blks_MD5(str) {
// Convert a string to a sequence of 16-word blocks, stored as an array.
// Append padding bits and the length, as described in the MD5 standard.
  nblk = ((str.length + 8) >> 6) + 1;
  blks = new Array(nblk * 16);
  for(i = 0; i < nblk * 16; i++) blks[i] = 0;
  for(i = 0; i < str.length; i++)
    blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
  blks[i >> 2] |= 0x80 << ((i % 4) * 8);
  blks[nblk * 16 - 2] = str.length * 8;
  return blks;
}

function add(x, y) {
// Add integers, wrapping at 2^32. This uses 16-bit operations internally to work around bugs in some JS interpreters.
  var lsw = (x & 0xFFFF) + (y & 0xFFFF);
  var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
  return (msw << 16) | (lsw & 0xFFFF);
}

function rol(num, cnt) {
// Bitwise rotate a 32-bit number to the left
	return (num << cnt) | (num >>> (32 - cnt));
}

// These functions implement the basic operation for each round of the algorithm.
function cmn(q, a, b, x, s, t) { return add(rol(add(add(a, q), add(x, t)), s), b); }
function ff(a, b, c, d, x, s, t) { return cmn((b & c) | ((~b) & d), a, b, x, s, t); }
function gg(a, b, c, d, x, s, t) { return cmn((b & d) | (c & (~d)), a, b, x, s, t); }
function hh(a, b, c, d, x, s, t) { return cmn(b ^ c ^ d, a, b, x, s, t); }
function ii(a, b, c, d, x, s, t) { return cmn(c ^ (b | (~d)), a, b, x, s, t); }

function MD5(str) {
// Take a string and return the hex representation of its MD5.
  x = str2blks_MD5(str);
  var a =  1732584193;
  var b = -271733879;
  var c = -1732584194;
  var d =  271733878;
 
  for(i = 0; i < x.length; i += 16) {
    var olda = a;
    var oldb = b;
    var oldc = c;
    var oldd = d;

    a = ff(a, b, c, d, x[i+ 0], 7 , -680876936);
    d = ff(d, a, b, c, x[i+ 1], 12, -389564586);
    c = ff(c, d, a, b, x[i+ 2], 17,  606105819);
    b = ff(b, c, d, a, x[i+ 3], 22, -1044525330);
    a = ff(a, b, c, d, x[i+ 4], 7 , -176418897);
    d = ff(d, a, b, c, x[i+ 5], 12,  1200080426);
    c = ff(c, d, a, b, x[i+ 6], 17, -1473231341);
    b = ff(b, c, d, a, x[i+ 7], 22, -45705983);
    a = ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
    d = ff(d, a, b, c, x[i+ 9], 12, -1958414417);
    c = ff(c, d, a, b, x[i+10], 17, -42063);
    b = ff(b, c, d, a, x[i+11], 22, -1990404162);
    a = ff(a, b, c, d, x[i+12], 7 ,  1804603682);
    d = ff(d, a, b, c, x[i+13], 12, -40341101);
    c = ff(c, d, a, b, x[i+14], 17, -1502002290);
    b = ff(b, c, d, a, x[i+15], 22,  1236535329);    

    a = gg(a, b, c, d, x[i+ 1], 5 , -165796510);
    d = gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
    c = gg(c, d, a, b, x[i+11], 14,  643717713);
    b = gg(b, c, d, a, x[i+ 0], 20, -373897302);
    a = gg(a, b, c, d, x[i+ 5], 5 , -701558691);
    d = gg(d, a, b, c, x[i+10], 9 ,  38016083);
    c = gg(c, d, a, b, x[i+15], 14, -660478335);
    b = gg(b, c, d, a, x[i+ 4], 20, -405537848);
    a = gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
    d = gg(d, a, b, c, x[i+14], 9 , -1019803690);
    c = gg(c, d, a, b, x[i+ 3], 14, -187363961);
    b = gg(b, c, d, a, x[i+ 8], 20,  1163531501);
    a = gg(a, b, c, d, x[i+13], 5 , -1444681467);
    d = gg(d, a, b, c, x[i+ 2], 9 , -51403784);
    c = gg(c, d, a, b, x[i+ 7], 14,  1735328473);
    b = gg(b, c, d, a, x[i+12], 20, -1926607734);
    
    a = hh(a, b, c, d, x[i+ 5], 4 , -378558);
    d = hh(d, a, b, c, x[i+ 8], 11, -2022574463);
    c = hh(c, d, a, b, x[i+11], 16,  1839030562);
    b = hh(b, c, d, a, x[i+14], 23, -35309556);
    a = hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
    d = hh(d, a, b, c, x[i+ 4], 11,  1272893353);
    c = hh(c, d, a, b, x[i+ 7], 16, -155497632);
    b = hh(b, c, d, a, x[i+10], 23, -1094730640);
    a = hh(a, b, c, d, x[i+13], 4 ,  681279174);
    d = hh(d, a, b, c, x[i+ 0], 11, -358537222);
    c = hh(c, d, a, b, x[i+ 3], 16, -722521979);
    b = hh(b, c, d, a, x[i+ 6], 23,  76029189);
    a = hh(a, b, c, d, x[i+ 9], 4 , -640364487);
    d = hh(d, a, b, c, x[i+12], 11, -421815835);
    c = hh(c, d, a, b, x[i+15], 16,  530742520);
    b = hh(b, c, d, a, x[i+ 2], 23, -995338651);

    a = ii(a, b, c, d, x[i+ 0], 6 , -198630844);
    d = ii(d, a, b, c, x[i+ 7], 10,  1126891415);
    c = ii(c, d, a, b, x[i+14], 15, -1416354905);
    b = ii(b, c, d, a, x[i+ 5], 21, -57434055);
    a = ii(a, b, c, d, x[i+12], 6 ,  1700485571);
    d = ii(d, a, b, c, x[i+ 3], 10, -1894986606);
    c = ii(c, d, a, b, x[i+10], 15, -1051523);
    b = ii(b, c, d, a, x[i+ 1], 21, -2054922799);
    a = ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
    d = ii(d, a, b, c, x[i+15], 10, -30611744);
    c = ii(c, d, a, b, x[i+ 6], 15, -1560198380);
    b = ii(b, c, d, a, x[i+13], 21,  1309151649);
    a = ii(a, b, c, d, x[i+ 4], 6 , -145523070);
    d = ii(d, a, b, c, x[i+11], 10, -1120210379);
    c = ii(c, d, a, b, x[i+ 2], 15,  718787259);
    b = ii(b, c, d, a, x[i+ 9], 21, -343485551);

    a = add(a, olda);
    b = add(b, oldb);
    c = add(c, oldc);
    d = add(d, oldd);
  }
  return rhex(a) + rhex(b) + rhex(c) + rhex(d);
}

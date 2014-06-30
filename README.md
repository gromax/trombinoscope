trombinoscope
=============

Gestion d'un trombinoscope évènementiel

Installation
============
* Créer le répertoire `./lib/` et y installer :
  * `bootstrap`
  * `bootstrap-file-input.js`
  * `handlebar.js`
  * `jquery-1.9.1.min.js`
  * Un sous répertoire Cropper contenant l'image Cropper de fengyuanchen
	* `cropper.min.css`
	* `cropper.min.js`
* Copier le fichier `php/conx/connexion.php.dist` vers `php/conx/connexion.php` et y indiquer les bonnes valeurs pour la base de données.
* Copier le fichier `php/config.php.dist` vers `php/config.php` et y indiquer les bonnes valeurs.
* Exécuter le script `php/createDataBase.php`
* Créer le répertoire `./img/` et s'assurer du droit à uploader dedans
* Démarrer avec le compte superAdmin :
  * login = root
  * pwd = ''

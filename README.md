# projetTutore2Backend

Matthieu FRANCOIS, Erwin MOREL, Nicolas NENNIG, Sélène VIOLA

Lien vers le trello public : https://trello.com/b/15sldvGX/projettutore2francoismorelnennigviola

Lien vers le dossier Google Drive : https://drive.google.com/drive/folders/1joKBNuC0VUoMo8whEOoOFFnmP3igkOk-?usp=sharing

Lien vers le dépot Git Front : https://github.com/Selene-V/projetTutore2


Manuel d'installation :

1) Cloner le repository grâce à cette commande : git clone https://github.com/Selene-V/projetTutore2Backend.git

2) Exécuter les commande suivante depuis le dossier "projetTut2BackEnd" : composer install

3) Lancer elasticsearch avec la commande suivante depuis le dossier elasticsearch-7.XX.X : "bin/elasticsearch.bat" (windows) ou "bin/elasticsearch" (mac)

4) Ouvrir un terminal dans le dossier "Script" et lancer ces commandes : npm install, node script.csv

5) Créer le virtual host nommé "projettutore2back" en indiquant le chemin qui mène à projetTutore2Backend/projetTut2BackEnd/public

6) Importer la base de données grâce au fichier contenu dans le dossier SQL

7) Faire un fichier config.local.php dans le dossier "projetTut2BackEnd" contenant ceci avec vos informations de connexion à votre base de données :

<?php

return [
    'pdo_dbname' => 'projettutore2',
    'pdo_host' => '127.0.0.1',
    'pdo_user' => 'root',
    'pdo_password' => '',
];


Comptes existants dans la db :

emails : 

laroche.pierre@univ-lorraine.fr
ronin.a@sfeir.com
pi.zamperini@gmail.com

mot de passe (commun):

aze

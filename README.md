# gul.petite-montagne.net
Ce repo contient le code du site web du GUL Petite Montagne. Envoyez des pull request pour ajouter ou modifier des pages. 

Il peut être utilisé comme base pour créer un site de gul

Il vous faudra éditer index.php pour remplacer les mentions du GUL Petite Montagne par le nom de votre GUL : en début de page, en fin de page, principalement

Ensuite, le tableau contenant la liste des localités concernées par votre GUL doit être modifié pour indiquer les localités correctes. 

Pas besoin que ce soit des communes, du moment que le nom est reconnu dans OSM. 

À ce moment vous pouvez déployer les deux fichiers index.php et style.css sur votre serveur. 

Ce logiciel fonctionne avec de la serialisation key-value : il ne requiert pas de base de données. 

Au premier affichage de la page, le script va aller interroger l'API d'OSM pour obtenir les coordonnées lat/longitude de chacune des localités précédement déclarées. 

Cela peut prendre un peu de temps. 

Elle seront sauvegardée dans un fichier nommé  geocities.dat

À ce moment là vous pouvez vous créer un compte utilisateur, et inviter les membres de votre GUL à faire de même. 

Note:si vous souhaitez conserver le lien vers Nodni, modifiez son href pour remplacer "Valzin+en+petite+Montagne" par un nom de localité plus ou moins central dans votre zone d'activité. 


TODO : 

ajouter les fonction d'édition du profil (photo, nom ou pseudo, description)
ajouter la fonction d'envoi de message
ajouter la carte des membres


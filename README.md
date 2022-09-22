# Installation


### 1. Créer une application Facebook comportant :
    - Le module facebook login (https://developers.facebook.com/docs/facebook-login) 
    ######1.1. Pour configurer le module facebook login, il faut ajouter les informations suivantes :
        - L'url de redirect {ROOT_PATH}/instagram/publish/oauth_redirect (HTTPS obligatoire)
  
        - L'url de votre application {ROOT_PATH}
    
    - Le module Instagram Graph (Aucune configuration necessaire) (https://developers.facebook.com/docs/instagram-api)

    - Une page Facebook business Lié au compte développeur (https://www.facebook.com/business/pages/create)

    - Une page Instagram business liée a la page Facebook Business (https://www.facebook.com/business/instagram/create)

### 2. Les commandes pour lancer le projet :
    - `composer i`
    - `php artisan key:generate`
    - `sudo chmod -R 777 ./`

### 3. Remplir le fichier .env

    - INSTAGRAM_APP_ID = votre identifiant d'application sur [developers.facebook.com](developers.facebook.com)
    - INSTAGRAM_APP_SECRET = votre secret d'application sur [developers.facebook.com](developers.facebook.com)

### 4. Lancer le serveur
### 5. Rendez-vous sur {ROOT_PATH}/instagram/publish/login et le tour est joué

## 999. Bientôt : 
    - Une interface pour choisir ses photos à publier, le texte
    - Une interface pour publier des Story / Réels

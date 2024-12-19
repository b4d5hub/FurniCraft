# FurniCraft - Boutique WooCommerce  

Ce projet est une boutique créée utilisant le CMS WordPress. Voici les étapes pour cloner le projet, configurer l'environnement local, importer la base de données, et exécuter le site sur votre ordinateur.

---

### 🚀 Démo

Accédez à la version en ligne : [FurniCraft Demo](https://furnicraft.ouajih.com)

---  

## Prérequis  

Avant de commencer, assurez-vous d'avoir installé les outils suivants sur votre ordinateur :  
- **Git** : Pour cloner le projet. [Télécharger Git](https://git-scm.com/)  
- **XAMPP/WAMP/MAMP** : Pour exécuter un serveur local (Apache, MySQL, PHP). [Télécharger XAMPP](https://www.apachefriends.org/index.html)  

---  

## Étapes pour exécuter le projet  

### 1. Créer un dossier pour le projet  

1. Créez un nouveau dossier sur votre ordinateur, par exemple nommé `wordpress`.  
2. Ouvrez un terminal ou une invite de commande.  
3. Accédez au répertoire de ce dossier avec la commande suivante :  
   ```bash  
   cd chemin/vers/le/dossier/wordpress  
   ```  

---  

### 2. Cloner le projet depuis GitHub  

1. Exécutez la commande suivante pour cloner le projet dans ce dossier :  
   ```bash  
   git clone https://github.com/b4d5hub/FurniCraft.git .  
   ```  

2. Une fois le clonage terminé, vous devriez voir les fichiers du projet dans le dossier.  

---  

### 3. Placer les fichiers dans le serveur local  

1. Déplacez les fichiers clonés dans le dossier approprié pour le serveur local :  
   - Pour **XAMPP** : placez-les dans `C:/xampp/htdocs/wordpress`.  
   - Pour **WAMP** : placez-les dans `C:/wamp/www/wordpress`.  

2. Lancez le panneau de contrôle de XAMPP/WAMP et démarrez les services **Apache** et **MySQL**.  

---  

### 4. Configurer la base de données  

#### Accéder à phpMyAdmin  
1. Ouvrez votre navigateur et accédez à : [http://localhost/phpmyadmin](http://localhost/phpmyadmin).  

#### Créer une base de données  
1. Cliquez sur **Nouvelle base de données**.  
2. Donnez-lui un nom, par exemple : `wordpress_db`.  
3. Cliquez sur **Créer**.  

#### Importer la base de données  
1. Cliquez sur **Importer** dans phpMyAdmin.  
2. Sélectionnez le fichier SQL inclus dans le projet, qui se trouve dans le dossier `database/wordpress_db.sql`.  
3. Cliquez sur **Exécuter**.  

---  

### 5. Configurer le fichier wp-config.php  

1. Dans le dossier du projet, trouvez le fichier `wp-config-sample.php` et renommez-le en `wp-config.php`.  
2. Ouvrez ce fichier avec un éditeur de texte (par exemple : VSCode).  
3. Modifiez les paramètres suivants pour qu'ils correspondent à votre base de données locale :  
   ```php  
   define('DB_NAME', 'wordpress_db'); // Nom de la base de données  
   define('DB_USER', 'root');         // Nom d'utilisateur (par défaut : root)  
   define('DB_PASSWORD', '');         // Mot de passe (par défaut : vide)  
   define('DB_HOST', 'localhost');    // Hôte (par défaut : localhost)  
   ```  
4. Sauvegardez le fichier.  

---  

### 6. Accéder au site WordPress  

1. Ouvrez votre navigateur web.  
2. Rendez-vous sur : [http://localhost/wordpress](http://localhost/wordpress).  
   Si tout est configuré correctement, le site WordPress devrait s'afficher.  
3. Pour accéder au tableau de bord d'administration WordPress :  
   - URL : [http://localhost/wordpress/wp-admin](http://localhost/wordpress/wp-admin)  
   - Identifiants par défaut :  
     - **Administrateur** :  
       - Nom d'utilisateur : `admin`  
       - Mot de passe : `12345678`  
     - **Éditeur** :  
       - Nom d'utilisateur : `editeur`  
       - Mot de passe : `12345678`  
     - **Contributeur** :  
       - Nom d'utilisateur : `contributeur`  
       - Mot de passe : `12345678`  

---  

### 🗂️ Structure du projet  

- **wp-content/** : Contient les thèmes, plugins, et fichiers uploadés.  
- **database/** : Contient le fichier SQL pour importer la base de données.  
- **wp-config-sample.php** : Exemple de fichier de configuration pour connecter WordPress à la base de données.  

---  

### ❓ Support  

Si vous avez des problèmes pour exécuter le projet, n'hésitez pas à ouvrir une issue sur le dépôt GitHub.  

---  

### 🔒 Remarque  

- Ce projet est destiné à être exécuté en local pour des fins de développement.  
- Si vous souhaitez déployer ce site sur un serveur distant, vous devrez configurer un environnement serveur similaire (Apache, MySQL, PHP) et modifier les configurations en conséquence.  

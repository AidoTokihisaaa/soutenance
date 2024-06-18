# EventPulse

EventPulse est une application web de gestion d'événements, permettant aux utilisateurs de créer, gérer et participer à divers événements. Cette application offre des fonctionnalités de création d'événements, de gestion des participants, de notifications, et bien plus encore.

## Table des matières

1. [Pré-requis](#pré-requis)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Utilisation](#utilisation)
5. [Structure du projet](#structure-du-projet)
6. [Fonctionnalités](#fonctionnalités)
7. [Contributions](#contributions)
8. [Aide](#aide)

## Pré-requis

Avant de commencer, assurez-vous d'avoir les logiciels suivants installés sur votre machine :

- WAMP64 (pour le serveur web et la base de données MySQL)
- Node.js et npm
- Git

## Installation

1. Clonez le dépôt de l'application :
   ```bash
   git clone https://gitlab.com/soutenance1253225/soutenance.git
   ```

2. Déplacez les fichiers dans le répertoire `www` de WAMP :
   - Déplacez le dossier cloné dans `C:\wamp64\www\soutenance`.

3. Installez les dépendances du projet :
   ```bash
   cd C:\wamp64\www\app-event
   npm install
   ```

### Installation des dépendances supplémentaires

Si vous n'avez pas les dépendances nécessaires installées sur votre machine, exécutez les commandes suivantes :

```bash
Installation de Babel et des presets nécessaires :
npm install @babel/core @babel/preset-env @babel/preset-react @babel/preset-typescript babel-loader --save-dev

Installation des loaders CSS et Sass :
npm install css-loader node-sass sass-loader style-loader --save-dev

Installation d'ESLint et du plugin React pour ESLint :
npm install eslint eslint-plugin-react --save-dev

Installation du loader de fichiers (file-loader) :
npm install file-loader --save-dev

Installation de HTMLWebpackPlugin pour Webpack :
npm install html-webpack-plugin --save-dev

Installation de TypeScript et des loaders associés pour Webpack :
npm install ts-loader typescript --save-dev

Installation de Webpack et de ses outils CLI et dev-server :
npm install webpack webpack-cli webpack-dev-server --save-dev
```

## Configuration

### Base de données

1. Ouvrez phpMyAdmin via WAMP64 et créez une base de données nommée `projet_event`.
2. Importez le fichier SQL pour créer les tables nécessaires :
   - Allez dans phpMyAdmin, sélectionnez la base de données `projet_event`.
   - Cliquez sur l'onglet "Importer" et sélectionnez le fichier `projet_event.sql` situé dans le répertoire `backend/database`.

### Variables d'environnement

1. Créez un fichier `.env` à la racine du dossier `backend` et ajoutez les variables suivantes :
   ```plaintext
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=motdepasse
   DB_NAME=projet_event
   ```

### Configuration Webpack

Vérifiez que le fichier `webpack.config.js` est correctement configuré pour le développement et la production.

## Utilisation

1. Assurez-vous que WAMP64 est en cours d'exécution.
2. Ouvrez votre navigateur et accédez à `http://localhost/soutenance/backend` pour le backend et à `http://localhost/soutenance/frontend` pour le frontend.

### Compilation et Construction du Projet

#### Pour compiler les fichiers CSS :

```bash
npm run sass
```

#### Pour surveiller et compiler automatiquement les fichiers SCSS :

```bash
npm run watch:sass
```

## Structure du projet

Le projet est structuré comme suit :

```
app-event/
├── backend/                  # Dossier backend
│   ├── config/               # Fichiers de configuration
│   ├── controllers/          # Contrôleurs
│   ├── database/             # Fichiers de base de données
│   ├── models/               # Modèles de données
│   ├── routes/               # Routes API
│   ├── utils/                # Utilitaires
│   ├── app.js                # Fichier principal backend
│   └── .env                  # Variables d'environnement
├── frontend/                 # Dossier frontend
│   ├── public/               # Fichiers publics
│   ├── src/                  # Code source frontend
│   │   ├── components/       # Composants React
│   │   ├── pages/            # Pages React
│   │   ├── services/         # Services API
│   │   ├── styles/           # Styles CSS/SCSS
│   │   ├── App.js            # Composant principal
│   │   └── index.js          # Point d'entrée frontend
├── .gitignore                # Fichiers et dossiers ignorés par Git
├── package.json              # Fichier de configuration npm
├── package-lock.json         # Fichier de verrouillage des dépendances npm
├── webpack.config.js         # Configuration de Webpack
├── tsconfig.json             # Configuration TypeScript
├── tsconfig.eslint.json      # Configuration ESLint pour TypeScript
├── README.md                 # Documentation du projet
└── tree.txt                  # Structure du projet
```

## Fonctionnalités

- **Création et gestion d'événements** : Créez des événements avec des descriptions détaillées, des dates et des lieux.
- **Gestion des participants** : Ajoutez, modifiez et supprimez des participants à des événements.
- **Notifications** : Recevez des notifications pour les nouveaux événements, les commentaires, etc.
- **Authentification** : Inscription, connexion et déconnexion des utilisateurs.
- **Tableau de bord** : Vue d'ensemble des événements créés et des participations.

## Contributions

Les contributions sont les bienvenues ! Pour contribuer :

1. Forkez le projet.
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/ma-fonctionnalité`).
3. Commitez vos modifications (`git commit -am 'Ajout de ma fonctionnalité'`).
4. Poussez votre branche (`git push origin feature/ma-fonctionnalité`).
5. Ouvrez une Pull Request.

## Aide

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub.

---

### Importation de la base de données

Pour importer la base de données, suivez les étapes ci-dessous :

1. Ouvrez phpMyAdmin via WAMP64.
2. Créez une nouvelle base de données nommée `projet_event`.
3. Sélectionnez la base de données `projet_event`.
4. Cliquez sur l'onglet "Importer".
5. Sélectionnez le fichier `projet_event.sql` situé dans le répertoire `backend/database`.
6. Cliquez sur "Exécuter" pour importer les tables et les données.

### Lancement de l'application

1. Assurez-vous que WAMP64 est en cours d'exécution.
2. Accédez à `http://localhost/soutenance/` dans votre navigateur.

### Structure du projet

```
app-event/
├── backend/
│   ├── config/
│   ├── controllers/
│   ├── database/
│   ├── models/
│   ├── routes/
│   ├── utils/
│   ├── app.js
│   └── .env
├── frontend/
│   ├── public/
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── services/
│   │   ├── styles/
│   │   ├── App.js
│   │   └── index.js
├── .gitignore
├── package.json
├── package-lock.json
├── webpack.config.js
├── tsconfig.json
├── tsconfig.eslint.json
├── README.md
└── tree.txt
```

Assurez-vous que tout est correctement configuré et que les dépendances sont installées. 
Si vous rencontrez des problèmes, n'hésitez pas à me le faire savoir.

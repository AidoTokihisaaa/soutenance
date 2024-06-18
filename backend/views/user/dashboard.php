<?php
// Démarrer une nouvelle session ou reprendre la session existante
session_start();

// Définir le fuseau horaire par défaut à utiliser
date_default_timezone_set('Europe/Paris');

// Inclure le fichier de configuration de la base de données
require_once "../../config/database.php";

// Créer une nouvelle instance de la classe Database et obtenir la connexion à la base de données
$database = new Database();
$link = $database->getConnection();

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: ../../views/user/login.php');
    exit;
}

// Préparer une instruction SQL pour sélectionner les événements associés à l'utilisateur connecté
$stmt = $link->prepare("
    SELECT events.id, events.name, events.description, events.date
    FROM events
    LEFT JOIN event_permissions ON events.id = event_permissions.event_id
    WHERE events.user_id = :user_id OR event_permissions.user_id = :user_id
    GROUP BY events.id
");
// Lier le paramètre :user_id à l'ID de l'utilisateur connecté stocké dans la session
$stmt->bindParam(':user_id', $_SESSION['id']);
// Exécuter l'instruction préparée
$stmt->execute();
// Récupérer tous les résultats sous forme de tableau associatif
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Définir les métadonnées du document HTML -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <!-- Lier les feuilles de style externes -->
    <link rel="stylesheet" href="../../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
    <!-- Début du conteneur de l'en-tête -->
    <div class="container">
        <!-- Logo de l'application -->
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i> EventPulse</h1>
        </div>
        <!-- Navigation principale -->
        <nav>
            <ul class="menu">
                <!-- Lien vers la page d'accueil -->
                <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <!-- Lien vers la page de création d'événement -->
                <li><a href="../event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
                <!-- Lien vers la page de gestion des événements -->
                <li><a href="../event/manage.php"><i class="fas fa-tasks"></i> Gérer les Événements</a></li>
                <!-- Menu utilisateur avec options de profil et de déconnexion -->
                <li class="user-menu">
                    <a href="#" id="user-icon"><i class="fas fa-user"></i></a>
                    <div class="dropdown-content">
                        <!-- Afficher les informations de l'utilisateur -->
                        <div class="user-info">
                            <p><strong>Nom:</strong> <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email'] ?? 'N/A') ?></p>
                        </div>
                        <!-- Lien de déconnexion -->
                        <a href="../user/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
<main>
    <!-- Conteneur principal centré -->
    <div class="container centered">
        <h1>Vos Événements</h1>
        <!-- Grille des événements -->
        <div class="events-grid">
            <!-- Boucle pour afficher chaque événement -->
            <?php foreach ($events as $event): ?>
                <div class="event-item">
                    <h2><?= htmlspecialchars($event['name']) ?></h2>
                    <p><?= htmlspecialchars($event['description']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($event['date']) ?></p>
                    <!-- Lien vers les détails de l'événement -->
                    <a href="../event/view.php?id=<?= $event['id'] ?>" class="btn"><i class="fas fa-eye"></i> Voir Détails</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<footer>
    <!-- Conteneur du pied de page -->
    <div class="container">
        <div class="footer-content">
            <!-- Liens vers les réseaux sociaux -->
            <div class="social-media">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <!-- Texte du copyright -->
            <p>&copy; 2024 EventPulse. Tous droits réservés.</p>
        </div>
    </div>
</footer>
<script>
// Attendre que le contenu de la page soit entièrement chargé
document.addEventListener("DOMContentLoaded", function() {
    // Sélectionner l'élément de l'icône utilisateur et le menu déroulant associé
    const userIcon = document.getElementById('user-icon');
    const dropdown = document.querySelector('.dropdown-content');

    // Ajouter un événement de clic à l'icône utilisateur pour afficher/masquer le menu déroulant
    userIcon.addEventListener('click', function(event) {
        event.preventDefault();
        dropdown.classList.toggle('show');
    });

    // Ajouter un événement de clic à la fenêtre pour masquer le menu déroulant si l'utilisateur clique en dehors
    window.onclick = function(event) {
        if (!event.target.matches('#user-icon') && !dropdown.contains(event.target)) {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };
});
</script>
</body>
</html>

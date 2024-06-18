<?php
// Démarrer la session
session_start();

// Définir le fuseau horaire
date_default_timezone_set('Europe/Paris');

// Inclure les fichiers de configuration et de fonctions
require_once "../../config/database.php";
require_once "../../functions/event_functions.php";

// Créer une instance de la base de données et obtenir la connexion
$database = new Database();
$link = $database->getConnection();

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../views/user/login.php');
    exit;
}

// Récupérer l'ID de l'événement depuis les paramètres GET
$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    echo "Événement non trouvé.";
    exit;
}

// Obtenir les détails de l'événement
$eventDetails = getEventDetails($link, $eventId);
if (!$eventDetails) {
    echo "Détails de l'événement non disponibles.";
    exit;
}

// Si une requête POST est envoyée pour ajouter un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    addEventComment($link, $eventId, $_SESSION['id'], $comment);
    addNotification($link, $eventDetails['user_id'], "Nouveau commentaire sur votre événement : " . $eventDetails['name']);
    $_SESSION['success'] = "Commentaire ajouté avec succès.";
    header("Location: view.php?id=" . $eventId);
    exit;
}

// Récupérer les commentaires de l'événement
$eventComments = getEventComments($link, $eventId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'événement</title>
    <link rel="stylesheet" href="../../../css/view.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i> AppEvent</h1>
        </div>
        <nav>
            <ul class="menu">
                <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="../event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
                <li><a href="../user/dashboard.php"><i class="fas fa-tasks"></i> Dashboard</a></li>
                <li><a href="../event/manage.php"><i class="fas fa-tasks"></i> Gérer les Événements</a></li>
                <li class="user-menu">
                    <a href="#" id="user-icon"><i class="fas fa-user"></i></a>
                    <div class="dropdown-content">
                        <div class="user-info">
                            <!-- Afficher le nom d'utilisateur -->
                            <p><strong>Nom:</strong> <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></p>
                            <!-- Afficher l'email de l'utilisateur -->
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
    <div class="container event-details">
        <h2>Détails de l'événement</h2>
        <div class="event-info">
            <!-- Afficher les détails de l'événement -->
            <p><strong>Nom :</strong> <?= htmlspecialchars($eventDetails['name'] ?? 'N/A') ?></p>
            <p><strong>Description :</strong> <?= htmlspecialchars($eventDetails['description'] ?? 'N/A') ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars(date('d-m-Y', strtotime($eventDetails['date'] ?? 'today'))) ?></p>
            <p><strong>Heure :</strong> <?= htmlspecialchars(date('H:i:s', strtotime($eventDetails['created_at'] ?? 'now'))) ?></p>
            <p><strong>Lieu :</strong> <?= htmlspecialchars($eventDetails['location'] ?? 'Non spécifié') ?></p>
            <p><strong>Créé par :</strong> <?= htmlspecialchars($eventDetails['creator_name'] ?? 'Inconnu') ?></p>
        </div>

        <h3>Ajouter un commentaire</h3>
        <form class="comment-form" method="post">
            <label for="comment">Votre commentaire :</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit" class="btn">Envoyer le commentaire</button>
        </form>
        <div class="comments">
            <ul>
                <!-- Boucle pour afficher chaque commentaire -->
                <?php foreach ($eventComments as $comment): ?>
                    <li>
                        <p><strong><?= htmlspecialchars($comment['username']) ?></strong> <em>(<?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($comment['created_at']))) ?></em></p>
                        <p><?= htmlspecialchars($comment['comment']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</main>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="social-media">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <p>&copy; 2024 AppEvent. Tous droits réservés.</p>
        </div>
    </div>
</footer>
<script>
// Attendre que le document soit complètement chargé
document.addEventListener("DOMContentLoaded", function() {
    // Sélectionner l'icône utilisateur et le menu déroulant
    const userIcon = document.getElementById('user-icon');
    const dropdown = document.querySelector('.dropdown-content');

    // Ajouter un événement de clic à l'icône utilisateur
    userIcon.addEventListener('click', function(event) {
        event.preventDefault();
        dropdown.classList.toggle('show'); // Afficher/masquer le menu déroulant
    });

    // Fermer le menu déroulant si l'utilisateur clique en dehors
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

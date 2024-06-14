<?php
session_start();
date_default_timezone_set('Europe/Paris');
require_once "../../config/database.php";
$database = new Database();
$link = $database->getConnection();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../views/user/login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "Événement non trouvé.";
    exit;
}

$eventId = intval($_GET['id']);

function getEventDetails($link, $eventId) {
    $stmt = $link->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->bindParam(1, $eventId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getEventRating($link, $eventId) {
    $stmt = $link->prepare('SELECT AVG(rating) as average_rating FROM event_ratings WHERE event_id = ?');
    $stmt->bindParam(1, $eventId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['average_rating'];
}

function getEventComments($link, $eventId) {
    $stmt = $link->prepare('SELECT c.comment, c.created_at, u.username FROM event_comments c JOIN users u ON c.user_id = u.id WHERE c.event_id = ? ORDER BY c.created_at DESC');
    $stmt->bindParam(1, $eventId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$eventDetails = getEventDetails($link, $eventId);
$eventRating = getEventRating($link, $eventId);
$eventComments = getEventComments($link, $eventId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rating'])) {
        $rating = intval($_POST['rating']);
        $stmt = $link->prepare('INSERT INTO event_ratings (event_id, user_id, rating) VALUES (?, ?, ?)');
        $stmt->bindParam(1, $eventId, PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindParam(3, $rating, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: view.php?id=$eventId");
        exit;
    } elseif (isset($_POST['comment'])) {
        $comment = htmlspecialchars($_POST['comment']);
        $stmt = $link->prepare('INSERT INTO event_comments (event_id, user_id, comment) VALUES (?, ?, ?)');
        $stmt->bindParam(1, $eventId, PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION['id'], PDO::PARAM_INT);
        $stmt->bindParam(3, $comment, PDO::PARAM_STR);
        $stmt->execute();
        header("Location: view.php?id=$eventId");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'événement</title>
    <link rel="stylesheet" href="../../../css/dashboard.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i> AppEvent</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="../event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
                <li><a href="../user/dashboard.php"><i class="fas fa-tasks"></i> Dashboard</a></li>
                <li><a href="../event/manage.php"><i class="fas fa-tasks"></i> Gérer les Événements</a></li>
                <li class="user-menu">
                    <a href="#" id="user-icon"><i class="fas fa-user"></i></a>
                    <div class="dropdown-content">
                        <div class="user-info">
                            <p><strong>Nom:</strong> <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email'] ?? 'N/A') ?></p>
                        </div>
                        <a href="../user/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
<main>
    <div class="container">
        <h2>Détails de l'événement</h2>
        <p><strong>Nom:</strong> <?= htmlspecialchars($eventDetails['name']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($eventDetails['description']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($eventDetails['date']) ?></p>
        <p><strong>Lieu:</strong> <?= htmlspecialchars($eventDetails['location']) ?></p>
        <p><strong>Note moyenne:</strong> <?= is_null($eventRating) ? 'Pas encore de notes' : number_format($eventRating, 1) ?>/5</p>
        
        <h3>Noter cet événement</h3>
        <form method="post">
            <label for="rating">Votre note (0-5):</label>
            <input type="number" id="rating" name="rating" min="0" max="5" step="1" required>
            <button type="submit">Envoyer la note</button>
        </form>
        
        <h3>Ajouter un commentaire</h3>
        <form method="post">
            <label for="comment">Votre commentaire:</label>
            <textarea id="comment" name="comment" required></textarea>
            <button type="submit">Envoyer le commentaire</button>
        </form>
        
        <h3>Commentaires</h3>
        <ul>
            <?php foreach ($eventComments as $comment): ?>
                <li>
                    <p><strong><?= htmlspecialchars($comment['username']) ?></strong> <em>(<?= htmlspecialchars($comment['created_at']) ?>)</em></p>
                    <p><?= htmlspecialchars($comment['comment']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
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
document.addEventListener("DOMContentLoaded", function() {
    const userIcon = document.getElementById('user-icon');
    const dropdown = document.querySelector('.dropdown-content');
    const notificationIcon = document.getElementById('notification-icon');
    const notificationDropdown = document.querySelector('.notifications');

    userIcon.addEventListener('click', function(event) {
        event.preventDefault();
        dropdown.classList.toggle('show');
    });

    notificationIcon.addEventListener('click', function(event) {
        event.preventDefault();
        notificationDropdown.classList.toggle('show');
    });

    window.onclick = function(event) {
        if (!event.target.matches('#user-icon') && !dropdown.contains(event.target)) {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
        if (!event.target.matches('#notification-icon') && !notificationDropdown.contains(event.target)) {
            if (notificationDropdown.classList.contains('show')) {
                notificationDropdown.classList.remove('show');
            }
        }
    };
});
</script>
</body>
</html>

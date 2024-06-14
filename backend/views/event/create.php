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

function addNotification($link, $userId, $message) {
    $stmt = $link->prepare("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->execute();
}

function getUnreadNotificationCount($link, $userId) {
    $stmt = $link->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? '';

    if (!$title || !$description || !$date) {
        $_SESSION['error'] = "Tous les champs sont requis.";
    } else {
        $stmt = $link->prepare("INSERT INTO events (name, description, date, user_id, created_at, updated_at) VALUES (:name, :description, :date, :user_id, NOW(), NOW())");
        $stmt->bindParam(':name', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user_id', $_SESSION['id']);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Événement créé avec succès.";
            addNotification($link, $_SESSION['id'], "Événement créé avec succès.");
            header('Location: manage.php');
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la création de l'événement.";
        }
    }
}

$unreadCount = getUnreadNotificationCount($link, $_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Événement</title>
    <link rel="stylesheet" href="../../../css/create.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i> Dashboard</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="../event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
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
                <li class="notification-menu">
                    <a href="#" id="notification-icon">
                        <i class="fas fa-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-content notifications">
                        <h3>Notifications</h3>
                        <ul id="notification-list">
                            <li><i class="fas fa-info-circle"></i> Aucune nouvelle notification pour le moment.</li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
<div class="event-form-container">
    <h1>Créer un Événement</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="title">Titre de l'événement:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <button type="submit" class="btn">Créer l'événement</button>
    </form>
</div>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="social-media">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i the "fab fa-twitter"></i></a>
                <a href="#"><i the "fab fa-instagram"></i></a>
                <a href="#"><i the "fab fa-linkedin-in"></i></a>
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

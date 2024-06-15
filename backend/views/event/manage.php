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

function getUnreadNotifications($link, $userId) {
    $stmt = $link->prepare('SELECT message, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getUnreadNotificationCount($link, $userId) {
    $stmt = $link->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function markNotificationsAsRead($link, $userId) {
    $stmt = $link->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    markNotificationsAsRead($link, $_SESSION['id']);
    header('Location: manage.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $eventId = $_GET['id'];
    $stmt = $link->prepare("DELETE FROM events WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Événement supprimé avec succès.";
        addNotification($link, $_SESSION['id'], "Événement supprimé avec succès.");
        header('Location: manage.php');
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression de l'événement.";
        header('Location: manage.php');
        exit;
    }
}

$stmt = $link->prepare("SELECT id, name, description, date, created_at FROM events WHERE user_id = :user_id ORDER BY date ASC");
$stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$unreadNotifications = getUnreadNotifications($link, $_SESSION['id']);
$unreadCount = count($unreadNotifications);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Événements</title>
    <link rel="stylesheet" href="../../../css/manage.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i>EventPulse</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="../event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
                <li><a href="../event/manage.php"><i class="fas fa-tasks"></i> Gérer les Événements</a></li>
                <li><a href="../user/dashboard.php"><i class="fas fa-tasks"></i> Dashboard</a></li>
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
                            <?php if (empty($unreadNotifications)): ?>
                                <li><i class="fas fa-info-circle"></i> Aucune nouvelle notification pour le moment.</li>
                            <?php else: ?>
                                <?php foreach ($unreadNotifications as $notification): ?>
                                    <li>
                                        <i class="fas fa-info-circle"></i>
                                        <?= htmlspecialchars($notification->message) ?>
                                        <br>
                                        <small><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($notification->created_at))) ?></small>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        <?php if ($unreadCount > 0): ?>
                            <form method="post" class="mark-read-form" style="text-align: center;">
                                <input type="hidden" name="mark_read" value="1">
                                <button type="submit" class="btn mark-read-btn">Marquer comme lu</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
<div class="manage-container">
    <h1>Gérer les Événements</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: '<?= $_SESSION['success']; ?>',
                showConfirmButton: false,
                timer: 3000
            });
            <?php unset($_SESSION['success']); ?>
        </script>
    <?php endif; ?>
    <div class="events-grid">
        <?php foreach ($events as $event): ?>
            <div class="event-card">
                <div class="event-header">
                    <h3><?= htmlspecialchars($event['name']); ?></h3>
                </div>
                <div class="event-body">
                    <p><?= htmlspecialchars($event['description']); ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars(date('d-m-Y', strtotime($event['date']))); ?></p>
                    <p><strong>Créé:</strong> <?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($event['created_at']))); ?></p>
                </div>
                <div class="event-footer">
                    <a href="edit.php?id=<?= $event['id']; ?>" class="btn btn-primary">Modifier</a>
                    <a href="?action=delete&id=<?= $event['id']; ?>" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="social-media">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <p>&copy; 2024 EventPulse. Tous droits réservés.</p>
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

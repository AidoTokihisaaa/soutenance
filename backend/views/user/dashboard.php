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

function getUserInfo($link, $userId) {
    $stmt = $link->prepare('SELECT username, email FROM users WHERE id = ?');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUpcomingEvents($link, $userId) {
    $stmt = $link->prepare('SELECT name, date, description, created_at, updated_at FROM events WHERE user_id = ? AND date >= CURDATE() ORDER BY date ASC');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getUnreadNotifications($link, $userId) {
    $stmt = $link->prepare('SELECT message, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function markNotificationsAsRead($link, $userId) {
    $stmt = $link->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
}

$userInfo = getUserInfo($link, $_SESSION['id']);
$upcomingEvents = getUpcomingEvents($link, $_SESSION['id']);
$notifications = getUnreadNotifications($link, $_SESSION['id']);
$unreadCount = count($notifications);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    markNotificationsAsRead($link, $_SESSION['id']);
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/dashboard.css">
    <title>Tableau de Bord - AppEvent</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i> AppEvent Dashboard</h1>
        </div>
        <nav>
            <ul>
                <li><a href="../event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
                <li><a href="../event/manage.php"><i class="fas fa-tasks"></i> Gérer les Événements</a></li>
                <li><a href="../event/stats.php"><i class="fas fa-chart-line"></i> Statistiques</a></li>
                <li class="user-menu">
                    <a href="#" id="user-icon"><i class="fas fa-user"></i></a>
                    <div class="dropdown-content">
                        <div class="user-info">
                            <p><strong>Nom:</strong> <?= htmlspecialchars($userInfo['username'] ?? 'N/A') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($userInfo['email'] ?? 'N/A') ?></p>
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
                            <?php if ($unreadCount === 0): ?>
                                <li><i class="fas fa-info-circle"></i> Aucune nouvelle notification pour le moment.</li>
                            <?php else: ?>
                                <?php foreach ($notifications as $notification): ?>
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
                            <form method="post" style="text-align: center;">
                                <input type="hidden" name="mark_read" value="1">
                                <button type="submit" class="btn">Marquer comme lu</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
<main>
    <div class="container">
        <div class="dashboard-grid">
            <div class="card">
                <h3><i class="fas fa-clock"></i> Événements Récents</h3>
                <ul>
                    <?php if (empty($upcomingEvents)): ?>
                        <li>Aucun événement à venir.</li>
                    <?php else: ?>
                        <?php foreach ($upcomingEvents as $event): ?>
                            <li>
                                <i class="fas fa-calendar-alt"></i>
                                <span><?= htmlspecialchars($event->name) ?> - <?= htmlspecialchars($event->date) ?></span>
                                <p>Créé: <?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($event->created_at))) ?></p>
                                <p>Mis à jour: <?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($event->updated_at))) ?></p>
                                <button class="view-details" data-name="<?= htmlspecialchars($event->name) ?>" data-date="<?= htmlspecialchars($event->date) ?>" data-description="<?= htmlspecialchars($event->description) ?>">Voir détails</button>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <h3><i class="fas fa-tasks"></i> Mes Tâches</h3>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Créer un nouvel événement</li>
                    <li><i class="fas fa-check-circle"></i> Gérer les événements existants</li>
                    <li><i class="fas fa-check-circle"></i> Analyser les statistiques des événements</li>
                </ul>
            </div>
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

    const viewDetailsButtons = document.querySelectorAll('.view-details');
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const name = this.dataset.name;
            const date = this.dataset.date;
            const description = this.dataset.description;
            Swal.fire({
                title: name,
                html: `<strong>Date:</strong> ${date}<br><strong>Description:</strong> ${description}`,
                icon: 'info'
            });
        });
    });
});
</script>
</body>
</html>

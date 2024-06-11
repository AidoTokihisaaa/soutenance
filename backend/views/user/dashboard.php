<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require_once "../../config/database.php";
$database = new Database();
$link = $database->getConnection();
function getUserInfo($link, $userId) {
    $stmt = $link->prepare('SELECT username, email FROM users WHERE id = ?');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUpcomingEvents($link, $userId) {
    $stmt = $link->prepare('SELECT name, date FROM events WHERE user_id = ? AND date >= CURDATE() ORDER BY date ASC');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

$userInfo = getUserInfo($link, $_SESSION['id']);
$events = getUpcomingEvents($link, $_SESSION['id']);
if (isset($_GET['logout'])) {
    setcookie('user_login', '', time() - 3600, "/"); // Supprimer le cookie
    session_unset();
    session_destroy();
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                alert('Vous avez été déconnecté avec succès.');
                window.location.href = 'login.php';
            });
          </script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <title>Tableau de Bord - AppEvent</title>
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
                    <li><a href="../user/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">
            <section class="dashboard-overview">
                <h2><i class="fas fa-tachometer-alt"></i> Vue d'ensemble</h2>
                <div class="overview-cards">
                    <div class="card">
                        <h3><i class="fas fa-calendar-day"></i> Événements à venir</h3>
                        <p>Vous avez <?php echo count($events); ?> événements planifiés.</p>
                    </div>
                </div>
            </section>
            <section class="recent-events">
                <h2><i class="fas fa-clock"></i> Événements Récents</h2>
                <ul>
                    <?php foreach ($events as $event): ?>
                    <li><i class="fas fa-calendar-check"></i> <?= htmlspecialchars($event->name) ?> - <?= $event->date ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
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
</body>
</html>

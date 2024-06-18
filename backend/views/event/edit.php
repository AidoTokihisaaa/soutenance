<?php
// Démarre une session PHP
session_start();

// Définit le fuseau horaire par défaut pour toutes les fonctions de date/heure en PHP
date_default_timezone_set('Europe/Paris');

// Inclut les fichiers de configuration de la base de données et des fonctions liées aux événements
require_once "../../config/database.php";

// Crée une instance de la classe Database pour obtenir une connexion à la base de données
$database = new Database();
$link = $database->getConnection();

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../views/user/login.php');
    exit;
}

// Récupère l'ID de l'événement à partir des paramètres de la requête
$eventId = $_GET['id'] ?? null;

// Si aucun ID d'événement n'est spécifié, redirige vers la page de gestion des événements avec un message d'erreur
if (!$eventId) {
    $_SESSION['error'] = "Aucun événement spécifié.";
    header('Location: manage.php');
    exit;
}

// Prépare une requête pour récupérer les détails de l'événement à partir de l'ID
$stmt = $link->prepare("SELECT id, name, description, date FROM events WHERE id = :id");
$stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'événement n'est pas trouvé, redirige vers la page de gestion des événements avec un message d'erreur
if (!$event) {
    $_SESSION['error'] = "Événement non trouvé.";
    header('Location: manage.php');
    exit;
}

// Fonction pour ajouter une notification
function addNotification($link, $userId, $message) {
    $stmt = $link->prepare("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->execute();
}

// Fonction pour récupérer les notifications non lues
function getUnreadNotifications($link, $userId) {
    $stmt = $link->prepare('SELECT message, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Fonction pour compter les notifications non lues
function getUnreadNotificationCount($link, $userId) {
    $stmt = $link->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Fonction pour marquer les notifications comme lues
function markNotificationsAsRead($link, $userId) {
    $stmt = $link->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
}

// Si la méthode de la requête est POST et que le bouton "mark_read" est défini, marque les notifications comme lues
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    markNotificationsAsRead($link, $_SESSION['id']);
    header('Location: edit.php?id=' . $eventId);
    exit;
}

// Si la méthode de la requête est POST et que le bouton "mark_read" n'est pas défini, traite la mise à jour de l'événement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['mark_read'])) {
    // Récupère les données du formulaire
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? '';

    // Vérifie si tous les champs obligatoires sont remplis
    if (!$title || !$description || !$date) {
        $_SESSION['error'] = "Tous les champs sont requis.";
    } else {
        // Prépare une requête pour mettre à jour l'événement dans la base de données
        $updateStmt = $link->prepare("UPDATE events SET name = :name, description = :description, date = CONCAT(:date, ' ', TIME(NOW())), updated_at = NOW() WHERE id = :id");
        $updateStmt->bindParam(':name', $title);
        $updateStmt->bindParam(':description', $description);
        $updateStmt->bindParam(':date', $date);
        $updateStmt->bindParam(':id', $eventId);
        
        // Si la mise à jour est réussie, définit un message de succès et redirige vers la page de gestion des événements
        if ($updateStmt->execute()) {
            $_SESSION['success'] = "Événement mis à jour avec succès.";
            addNotification($link, $_SESSION['id'], "Événement mis à jour avec succès.");
            header('Location: manage.php');
            exit;
        } else {
            // Définit un message d'erreur si la mise à jour échoue
            $_SESSION['error'] = "Erreur lors de la mise à jour de l'événement.";
        }
    }
}

// Récupère les notifications non lues pour l'utilisateur connecté
$unreadNotifications = getUnreadNotifications($link, $_SESSION['id']);
$unreadCount = count($unreadNotifications);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/edit.css">
    <link rel="stylesheet" href="../../../css/notifications.css">
    <title>Tableau de Bord - AppEvent</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
</head>
<body>
<!-- Début du header de la page -->
<header>
    <!-- Conteneur pour le header -->
    <div class="container">
        <!-- Section du logo -->
        <div class="logo">
            <!-- Titre du logo avec une icône de calendrier -->
            <h1><i class="fas fa-calendar-alt"></i>EventPulse</h1>
        </div>
        <!-- Section de navigation -->
        <nav>
            <!-- Liste des éléments du menu -->
            <ul>
                <!-- Lien vers la page d'accueil -->
                <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <!-- Lien vers la page de création d'événement -->
                <li><a href="../event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
                <!-- Lien vers la page de gestion des événements -->
                <li><a href="../event/manage.php"><i class="fas fa-tasks"></i> Gérer les Événements</a></li>
                <!-- Lien vers le tableau de bord utilisateur -->
                <li><a href="../user/dashboard.php"><i class="fas fa-tasks"></i> Dashboard</a></li>
                <!-- Menu utilisateur avec icône -->
                <li class="user-menu">
                    <a href="#" id="user-icon"><i class="fas fa-user"></i></a>
                    <!-- Contenu déroulant pour les informations utilisateur -->
                    <div class="dropdown-content">
                        <div class="user-info">
                            <!-- Affiche le nom d'utilisateur -->
                            <p><strong>Nom:</strong> <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></p>
                            <!-- Affiche l'email de l'utilisateur -->
                            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email'] ?? 'N/A') ?></p>
                        </div>
                        <!-- Lien pour se déconnecter -->
                        <a href="../user/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </li>
                <!-- Menu des notifications avec icône -->
                <li class="notification-menu">
                    <a href="#" id="notification-icon">
                        <i class="fas fa-bell"></i>
                        <!-- Affiche le nombre de notifications non lues si supérieur à 0 -->
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                    <!-- Contenu déroulant pour les notifications -->
                    <div class="dropdown-content notifications">
                        <h3>Notifications</h3>
                        <ul id="notification-list">
                            <!-- Affiche un message si aucune notification n'est disponible -->
                            <?php if (empty($unreadNotifications)): ?>
                                <li><i class="fas fa-info-circle"></i> Aucune nouvelle notification pour le moment.</li>
                            <?php else: ?>
                                <!-- Affiche les notifications non lues -->
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
                        <!-- Formulaire pour marquer les notifications comme lues -->
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

<!-- Conteneur pour le formulaire de modification de l'événement -->
<div class="event-form-container">
    <h1>Modifier l'événement</h1>
    <!-- Affiche un message d'erreur s'il y en a un -->
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <!-- Affiche un message de succès s'il y en a un -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <!-- Formulaire de modification de l'événement -->
    <form action="" method="post">
        <!-- Champ caché pour l'ID de l'événement -->
        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
        <!-- Champ pour le titre de l'événement -->
        <div class="form-group">
            <label for="title">Titre de l'événement:</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($event['name']); ?>">
        </div>
        <!-- Champ pour la description de l'événement -->
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        <!-- Champ pour la date de l'événement -->
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d', strtotime($event['date'])); ?>">
        </div>
        <!-- Bouton pour soumettre le formulaire et mettre à jour l'événement -->
        <button type="submit" class="btn">Mettre à jour l'événement</button>
    </form>
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

    // Ajoute un événement de clic pour afficher/masquer le menu déroulant de l'utilisateur
    userIcon.addEventListener('click', function(event) {
        event.preventDefault();
        dropdown.classList.toggle('show');
    });

    // Ajoute un événement de clic pour afficher/masquer le menu déroulant des notifications
    notificationIcon.addEventListener('click', function(event) {
        event.preventDefault();
        notificationDropdown.classList.toggle('show');
    });

    // Ajoute un événement de clic pour masquer les menus déroulants si l'utilisateur clique en dehors
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

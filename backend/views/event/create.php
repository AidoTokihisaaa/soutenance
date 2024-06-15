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

// Retrieve all users for permission setting form
$stmt = $link->prepare("SELECT id, username FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? '';

    if (!$title || !$description || !$date) {
        $_SESSION['error'] = "Tous les champs sont requis.";
        header('Location: create.php');
        exit;
    } else {
        $stmt = $link->prepare("INSERT INTO events (name, description, date, user_id, created_at, updated_at) VALUES (:name, :description, :date, :user_id, NOW(), NOW())");
        $stmt->bindParam(':name', $title);
        $stmt->bindParam(':description', $description);
        $vehicle_type = 'date';
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':user_id', $_SESSION['id']);
        if ($stmt->execute()) {
            $eventId = $link->lastInsertId();

            foreach ($_POST['users'] as $userId => $permissions) {
                $can_edit = isset($permissions['can_edit']) ? 1 : 0;
                $can_comment = isset($permissions['can_comment']) ? 1 : 0;
                $can_rate = isset($permissions['can_rate']) ? 1 : 0;
                $stmt = $link->prepare("INSERT INTO event_permissions (event_id, user_id, can_edit, can_comment, can_rate) VALUES (:event_id, :user_id, :can_edit, :can_comment, :can_rate) ON DUPLICATE KEY UPDATE can_edit = :can_edit, can_comment = :can_comment, can_rate = :can_rate");
                $stmt->bindParam(':event_id', $eventId);
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':can_edit', $can_edit, PDO::PARAM_INT);
                $stmt->bindParam(':can_comment', $can_comment, PDO::PARAM_INT);
                $stmt->bindParam(':can_rate', $can_rate, PDO::PARAM_INT); // Corrected to use a constant
                $stmt->execute();
            }
            $_SESSION['success'] = "Événement créé avec succès.";
            header('Location: manage.php');
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la création de l'événement.";
            header('Location: create.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Événement</title>
    <link rel="stylesheet" href="../../../css/create.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i> EventPulse</h1>
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
            </ul>
        </nav>
    </div>
</header>
<main>
    <div class="event-form-container">
        <h1>Créer un Événement</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
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
            <div class="permissions-container">
                <h2>Permissions pour d'autres utilisateurs</h2>
                <?php foreach ($users as $user): ?>
                    <div class="form-group">
                        <label><?= htmlspecialchars($user['username']); ?></label>
                        <input type="checkbox" name="users[<?= $user['id']; ?>][can_edit]" value="1"> Modifier
                        <input type="checkbox" name="users[<?= $user['id']; ?>][can_comment]" value="1"> Commenter
                        <input type="checkbox" name="users[<?= $user['id']; ?>][can_rate]" value="1"> Noter
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn">Créer l'événement</button>
        </form>
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
            <p>&copy; 2024 EventPulse. Tous droits réservés.</p>
        </div>
    </div>
</footer>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const userIcon = document.getElementById('user-icon');
    const dropdown = document.querySelector('.dropdown-content');

    userIcon.addEventListener('click', function(event) {
        event.preventDefault();
        dropdown.classList.toggle('show');
    });

    window.onclick = function(event) {
        if (!event.target.matches('#user-icon') && !dropdown.contains(event.target)) {
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };

    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach((group, index) => {
        group.style.animationDelay = `${index * 0.1}s`;
    });

    const submitButton = document.querySelector('.btn');
    submitButton.style.animationDelay = `${formGroups.length * 0.1}s`;
});
</script>
</body>
</html>
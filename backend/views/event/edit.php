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

$eventId = $_GET['id'] ?? null;
if (!$eventId) {
    $_SESSION['error'] = "Aucun événement spécifié.";
    header('Location: manage.php');
    exit;
}
$stmt = $link->prepare("SELECT id, name, description, date FROM events WHERE id = :id");
$stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    $_SESSION['error'] = "Événement non trouvé.";
    header('Location: manage.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? '';

    if (!$title || !$description || !$date) {
        $_SESSION['error'] = "Tous les champs sont requis.";
    } else {
        $updateStmt = $link->prepare("UPDATE events SET name = :name, description = :description, date = CONCAT(:date, ' ', TIME(NOW())), updated_at = NOW() WHERE id = :id");
        $updateStmt->bindParam(':name', $title);
        $updateStmt->bindParam(':description', $description);
        $updateStmt->bindParam(':date', $date);
        $updateStmt->bindParam(':id', $eventId);
        if ($updateStmt->execute()) {
            $_SESSION['message'] = "Événement mis à jour avec succès.";
            header('Location: manage.php');
            exit;
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour de l'événement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/edit.css">
    <title>Tableau de Bord - AppEvent</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
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
                            <p><strong>Nom:</strong> <?= htmlspecialchars($userInfo['username'] ?? 'N/A') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($userInfo['email'] ?? 'N/A') ?></p>
                        </div>
                        <a href="../user/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>
<div class="event-form-container">
    <h1>Modifier l'événement</h1>
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
        <div class="form-group">
            <label for="title">Titre de l'événement:</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($event['name']); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d', strtotime($event['date'])); ?>">
        </div>
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
            <p>&copy; 2024 AppEvent. Tous droits réservés.</p>
        </div>
    </div>
</footer>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const userIcon = document.getElementById('user-icon');
        const dropdown = document.querySelector('.dropdown-content');

        userIcon.addEventListener('click', function(event) {
            event.preventDefault();
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                dropdown.classList.add('hidden');
            } else {
                dropdown.classList.remove('hidden');
                dropdown.classList.add('show');
            }
        });

        window.onclick = function(event) {
            if (!event.target.matches('#user-icon') && !event.target.matches('.dropdown-content') && !dropdown.contains(event.target)) {
                if (dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                    dropdown.classList.add('hidden');
                }
            }
        };
    });
</script>
</body>
</html>

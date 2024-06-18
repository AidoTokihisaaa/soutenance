<?php
// Démarre une session PHP
session_start();

// Définit le fuseau horaire par défaut pour toutes les fonctions de date/heure en PHP
date_default_timezone_set('Europe/Paris');

// Inclut les fichiers de configuration de la base de données et des fonctions liées aux événements
require_once "../../config/database.php";
require_once "../../functions/event_functions.php";

// Crée une instance de la classe Database pour obtenir une connexion à la base de données
$database = new Database();
$link = $database->getConnection();

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page de connexion
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../views/user/login.php');
    exit;
}

// Récupère tous les utilisateurs de la base de données pour les afficher dans une liste déroulante
$stmt = $link->prepare("SELECT id, username FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si la méthode de la requête est POST, traite la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les données du formulaire
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $date = $_POST['date'] ?? '';
    $location = $_POST['location'] ?? '';
    $selectedUserId = $_POST['selected-user'] ?? '';

    // Vérifie si tous les champs obligatoires sont remplis
    if (!$title || !$description || !$date || !$location) {
        $_SESSION['error'] = "Tous les champs sont requis.";
        header('Location: create.php');
        exit;
    } else {
        // Insère un nouvel événement dans la base de données
        $stmt = $link->prepare("INSERT INTO events (name, description, date, location, user_id, created_at, updated_at) VALUES (:name, :description, :date, :location, :user_id, NOW(), NOW())");
        $stmt->bindParam(':name', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        
        // Si l'insertion est réussie, récupère l'ID de l'événement inséré
        if ($stmt->execute()) {
            $eventId = $link->lastInsertId();

            // Si un utilisateur a été sélectionné, attribue les permissions
            if ($selectedUserId) {
                $permissions = $_POST['permissions'] ?? [];
                $can_edit = in_array('can_edit', $permissions) ? 1 : 0;
                $can_comment = in_array('can_comment', $permissions) ? 1 : 0;
                $can_delete = in_array('can_delete', $permissions) ? 1 : 0;
                
                // Insère ou met à jour les permissions de l'utilisateur pour l'événement
                $stmt = $link->prepare("INSERT INTO event_permissions (event_id, user_id, can_edit, can_comment, can_delete) VALUES (:event_id, :user_id, :can_edit, :can_comment, :can_delete) ON DUPLICATE KEY UPDATE can_edit = :can_edit, can_comment = :can_comment, can_delete = :can_delete");
                $stmt->bindParam(':event_id', $eventId);
                $stmt->bindParam(':user_id', $selectedUserId, PDO::PARAM_INT);
                $stmt->bindParam(':can_edit', $can_edit, PDO::PARAM_INT);
                $stmt->bindParam(':can_comment', $can_comment, PDO::PARAM_INT);
                $stmt->bindParam(':can_delete', $can_delete, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Définit un message de succès et redirige vers la page de gestion des événements
            $_SESSION['success'] = "Événement créé avec succès.";
            header('Location: manage.php');
            exit;
        } else {
            // Définit un message d'erreur si l'insertion échoue et redirige vers la page de création d'événement
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
    <!-- Inclut les fichiers CSS nécessaires -->
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
                <div class="form-group">
                    <label for="location">Lieu de l'événement:</label>
                    <input type="text" id="location" name="location" required>
                </div>

                <div class="permissions-container">
                    <h2>Permissions pour d'autres utilisateurs</h2>
                    <div class="form-group">
                        <label for="user-select">Sélectionner un utilisateur:</label>
                        <select id="user-select" name="selected-user">
                            <option value="">Choisir un utilisateur</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="permissions[]" value="can_edit"> Modifier</label>
                        <label><input type="checkbox" name="permissions[]" value="can_comment"> Commenter</label>
                        <label><input type="checkbox" name="permissions[]" value="can_delete"> Supprimer</label>
                    </div>
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
</body>
</html>

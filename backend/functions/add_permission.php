<?php
// Démarre une session PHP
session_start();
// Inclut le fichier de configuration de la base de données
require_once "../../config/database.php";

// Crée une nouvelle instance de la classe Database et établit une connexion à la base de données
$database = new Database();
$link = $database->getConnection();

// Vérifie si la méthode de requête est POST et si l'utilisateur est connecté
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Récupère les données du formulaire
    $event_id = $_POST['event_id'];
    $user_id = $_POST['user_id'];
    $can_edit = isset($_POST['can_edit']) ? 1 : 0;
    $can_comment = isset($_POST['can_comment']) ? 1 : 0;
    $can_rate = isset($_POST['can_rate']) ? 1 : 0;

    // Prépare et exécute la requête SQL pour mettre à jour les permissions de l'événement
    $stmt = $link->prepare("REPLACE INTO event_permissions (event_id, user_id, can_edit, can_comment, can_rate) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $event_id);
    $stmt->bindParam(2, $user_id);
    $stmt->bindParam(3, $can_edit);
    $stmt->bindParam(4, $can_comment);
    $stmt->bindParam(5, $can_rate);
    $stmt->execute();

    // Redirige l'utilisateur vers le tableau de bord après l'ajout des permissions
    header('Location: dashboard.php');
    exit;
}
?>

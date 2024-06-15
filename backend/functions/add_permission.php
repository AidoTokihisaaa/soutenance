<?php
session_start();
require_once "../../config/database.php";
$database = new Database();
$link = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $event_id = $_POST['event_id'];
    $user_id = $_POST['user_id'];
    $can_edit = isset($_POST['can_edit']) ? 1 : 0;
    $can_comment = isset($_POST['can_comment']) ? 1 : 0;
    $can_rate = isset($_POST['can_rate']) ? 1 : 0;

    $stmt = $link->prepare("REPLACE INTO event_permissions (event_id, user_id, can_edit, can_comment, can_rate) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $event_id);
    $stmt->bindParam(2, $user_id);
    $stmt->bindParam(3, $can_edit);
    $stmt->bindParam(4, $can_comment);
    $stmt->bindParam(5, $can_rate);
    $stmt->execute();

    header('Location: dashboard.php'); // Redirigez l'utilisateur vers le tableau de bord après l'ajout
    exit;
}
?>

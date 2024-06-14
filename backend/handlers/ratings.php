<?php
session_start();
require_once "../../config/database.php";
$database = new Database();
$link = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_rating'])) {
    $eventId = $_POST['event_id'];
    $rating = $_POST['rating'];
    $userId = $_SESSION['id'];

    $stmt = $link->prepare("INSERT INTO ratings (event_id, user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
    $stmt->bindParam(1, $eventId, PDO::PARAM_INT);
    $stmt->bindParam(2, $userId, PDO::PARAM_INT);
    $stmt->bindParam(3, $rating, PDO::PARAM_INT);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Note ajoutée avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout de la note.";
    }
    header("Location: /views/event/view.php?id=$eventId");
    exit;
}
?>

<?php
session_start();
require_once "../../config/database.php";
$database = new Database();
$link = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $eventId = $_POST['event_id'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['id'];

    $stmt = $link->prepare("INSERT INTO comments (event_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $eventId, PDO::PARAM_INT);
    $stmt->bindParam(2, $userId, PDO::PARAM_INT);
    $stmt->bindParam(3, $comment, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Commentaire ajouté avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout du commentaire.";
    }
    header("Location: /views/event/view.php?id=$eventId");
    exit;
}
?>

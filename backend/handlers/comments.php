<?php
session_start();
require_once "../../config/database.php";
require_once "../../functions/event_functions.php";

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
        addNotification($link, $userId, "Nouveau commentaire ajouté à l'événement.");
        $_SESSION['success'] = "Commentaire ajouté avec succès.";
        header("Location: /views/event/view.php?id=$eventId");
        exit;
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout du commentaire.";
        header("Location: /views/event/view.php?id=$eventId");
        exit;
    }
}
?>

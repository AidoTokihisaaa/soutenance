<?php
// Démarre une session PHP pour gérer les informations de l'utilisateur
session_start();

// Inclut le fichier de configuration de la base de données
require_once "../../config/database.php";
// Inclut les fonctions d'événements
require_once "../../functions/event_functions.php";

// Crée une nouvelle instance de la classe Database et établit une connexion à la base de données
$database = new Database();
$link = $database->getConnection();

// Vérifie si la méthode de la requête est POST et si le formulaire d'ajout de commentaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    // Récupère les valeurs du formulaire
    $eventId = $_POST['event_id'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['id']; // Obtient l'ID de l'utilisateur à partir de la session

    // Prépare et exécute une requête SQL pour insérer le commentaire dans la base de données
    $stmt = $link->prepare("INSERT INTO comments (event_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $eventId, PDO::PARAM_INT); // L'ID de l'événement
    $stmt->bindParam(2, $userId, PDO::PARAM_INT); // L'ID de l'utilisateur
    $stmt->bindParam(3, $comment, PDO::PARAM_STR); // Le commentaire

    // Exécute la requête et vérifie si elle a réussi
    if ($stmt->execute()) {
        // Ajoute une notification pour l'utilisateur
        addNotification($link, $userId, "Nouveau commentaire ajouté à l'événement.");
        // Définit un message de succès dans la session
        $_SESSION['success'] = "Commentaire ajouté avec succès.";
        // Redirige vers la page de l'événement
        header("Location: /views/event/view.php?id=$eventId");
        exit;
    } else {
        // Définit un message d'erreur dans la session
        $_SESSION['error'] = "Erreur lors de l'ajout du commentaire.";
        // Redirige vers la page de l'événement
        header("Location: /views/event/view.php?id=$eventId");
        exit;
    }
}
?>

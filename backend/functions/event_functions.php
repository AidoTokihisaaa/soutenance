<?php
// Inclut le fichier de configuration de la base de données
require_once "../../config/database.php";

// Fonction pour obtenir les détails d'un événement
function getEventDetails($link, $eventId) {
    // Prépare et exécute une requête SQL pour récupérer les détails de l'événement
    $stmt = $link->prepare("SELECT e.name, e.description, e.date, e.created_at, e.location, u.username as creator_name, e.user_id FROM events e JOIN users u ON e.user_id = u.id WHERE e.id = :event_id");
    $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir les commentaires d'un événement
function getEventComments($link, $eventId) {
    // Prépare et exécute une requête SQL pour récupérer les commentaires de l'événement
    $stmt = $link->prepare('SELECT ec.comment, ec.created_at, u.username FROM event_comments AS ec JOIN users u ON ec.user_id = u.id WHERE ec.event_id = ? ORDER BY ec.created_at DESC');
    $stmt->execute([$eventId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour obtenir les événements d'un utilisateur
function getUserEvents($link, $userId) {
    // Prépare et exécute une requête SQL pour récupérer les événements associés à l'utilisateur
    $stmt = $link->prepare('SELECT e.*, p.can_edit, p.can_comment FROM events e LEFT JOIN event_permissions p ON e.id = p.event_id WHERE e.user_id = ? OR p.user_id = ?');
    $stmt->execute([$userId, $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour ajouter un commentaire à un événement
function addEventComment($link, $eventId, $userId, $comment) {
    // Prépare et exécute une requête SQL pour insérer un commentaire
    $stmt = $link->prepare('INSERT INTO event_comments (event_id, user_id, comment) VALUES (?, ?, ?)');
    $stmt->execute([$eventId, $userId, $comment]);
}

// Fonction pour supprimer un commentaire
function deleteComment($link, $commentId, $userId) {
    // Prépare et exécute une requête SQL pour supprimer un commentaire
    $stmt = $link->prepare('DELETE FROM event_comments WHERE id = ? AND user_id = ?');
    $stmt->execute([$commentId, $userId]);
}

// Fonction pour obtenir les événements à venir d'un utilisateur
function getUpcomingEvents($link, $userId) {
    // Prépare et exécute une requête SQL pour récupérer les événements à venir
    $stmt = $link->prepare('SELECT id, name, date, description, created_at, updated_at FROM events WHERE user_id = ? AND date >= CURDATE() ORDER BY date ASC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Fonction pour obtenir les notifications non lues d'un utilisateur
function getUnreadNotifications($link, $userId) {
    // Prépare et exécute une requête SQL pour récupérer les notifications non lues
    $stmt = $link->prepare('SELECT message, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Fonction pour marquer les notifications comme lues
function markNotificationsAsRead($link, $userId) {
    // Prépare et exécute une requête SQL pour mettre à jour les notifications
    $stmt = $link->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
    $stmt->execute([$userId]);
}

// Fonction pour ajouter une notification
function addNotification($link, $userId, $message) {
    // Prépare et exécute une requête SQL pour insérer une notification
    $stmt = $link->prepare("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->execute();
}
?>

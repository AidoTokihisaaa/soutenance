<?php
require_once "../../config/database.php";

// Récupère les détails de l'événement
function getEventDetails($link, $eventId) {
    $stmt = $link->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([$eventId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupère la moyenne des évaluations pour l'événement
function getEventRating($link, $eventId) {
    $stmt = $link->prepare('SELECT AVG(rating) AS average_rating FROM event_ratings WHERE event_id = ?');
    $stmt->execute([$eventId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['average_rating'] ?? null;
}

// Récupère les commentaires pour l'événement
function getEventComments($link, $eventId) {
    $stmt = $link->prepare('SELECT ec.comment, ec.created_at, u.username FROM event_comments AS ec JOIN users u ON ec.user_id = u.id WHERE ec.event_id = ? ORDER BY ec.created_at DESC');
    $stmt->execute([$eventId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupère les événements associés à un utilisateur
function getUserEvents($link, $userId) {
    $stmt = $link->prepare('SELECT e.*, p.can_edit, p.can_comment, p.can_rate FROM events e LEFT JOIN event_permissions p ON e.id = p.event_id WHERE e.user_id = ? OR p.user_id = ?');
    $stmt->execute([$userId, $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajoute un commentaire à un événement
function addEventComment($link, $eventId, $userId, $comment) {
    $stmt = $link->prepare('INSERT INTO event_comments (event_id, user_id, comment) VALUES (?, ?, ?)');
    $stmt->execute([$eventId, $userId, $comment]);
}

// Ajoute une évaluation à un événement
function addEventRating($link, $eventId, $userId, $rating) {
    $stmt = $link->prepare('INSERT INTO event_ratings (event_id, user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)');
    $stmt->execute([$eventId, $userId, $rating]);
}

// Supprime un commentaire
function deleteComment($link, $commentId, $userId) {
    $stmt = $link->prepare('DELETE FROM event_comments WHERE id = ? AND user_id = ?');
    $stmt->execute([$commentId, $userId]);
}

// Récupère les événements à venir pour un utilisateur
function getUpcomingEvents($link, $userId) {
    $stmt = $link->prepare('SELECT id, name, date, description, created_at, updated_at FROM events WHERE user_id = ? AND date >= CURDATE() ORDER BY date ASC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Récupère les notifications non lues
function getUnreadNotifications($link, $userId) {
    $stmt = $link->prepare('SELECT message, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Marque les notifications comme lues
function markNotificationsAsRead($link, $userId) {
    $stmt = $link->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
    $stmt->execute([$userId]);
}
?>
<?php
// Inclure le modèle Event pour interagir avec les données des événements dans la base de données
require_once 'backend/models/Event.php';

// Définir la classe EventController pour gérer les actions liées aux événements
class EventController {
    // Propriété pour contenir une instance du modèle Event
    private $eventModel;

    // Constructeur pour initialiser l'instance du modèle Event
    public function __construct() {
        $this->eventModel = new Event();
    }

    // Méthode pour créer un nouvel événement
    public function createEvent($title, $description, $date) {
        // Appeler la méthode addEvent du modèle Event et retourner un message de succès ou d'échec
        return $this->eventModel->addEvent($title, $description, $date) ? "Événement créé avec succès." : "Échec de la création de l'événement.";
    }

    // Méthode pour obtenir tous les événements
    public function getAllEvents() {
        // Appeler la méthode getEvents du modèle Event et retourner la liste des événements
        return $this->eventModel->getEvents();
    }

    // Méthode pour obtenir un événement spécifique par son ID
    public function getEvent($id) {
        // Appeler la méthode getEventById du modèle Event et retourner les détails de l'événement
        return $this->eventModel->getEventById($id);
    }

    // Méthode pour mettre à jour un événement existant
    public function updateEvent($id, $title, $description, $date) {
        // Appeler la méthode updateEvent du modèle Event et retourner un message de succès ou d'échec
        return $this->eventModel->updateEvent($id, $title, $description, $date) ? "Événement mis à jour avec succès." : "Échec de la mise à jour de l'événement.";
    }

    // Méthode pour supprimer un événement
    public function deleteEvent($id) {
        // Appeler la méthode deleteEvent du modèle Event et retourner un message de succès ou d'échec
        return $this->eventModel->deleteEvent($id) ? "Événement supprimé avec succès." : "Échec de la suppression de l'événement.";
    }
}
?>

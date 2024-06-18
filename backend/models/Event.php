<?php
// Inclut le fichier de configuration de la base de données
require_once 'Database.php';

class Event {
    private $conn; // Propriété pour la connexion à la base de données
    private $table_name = "events"; // Nom de la table des événements

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fonction pour ajouter un événement à la base de données
    public function addEvent($title, $description, $date) {
        $query = "INSERT INTO " . $this->table_name . " (title, description, date) VALUES (:title, :description, :date)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title); // Lie le paramètre :title à la variable $title
        $stmt->bindParam(':description', $description); // Lie le paramètre :description à la variable $description
        $stmt->bindParam(':date', $date); // Lie le paramètre :date à la variable $date

        // Exécute la requête et retourne true en cas de succès, false sinon
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Fonction pour récupérer tous les événements
    public function getEvents() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les événements sous forme de tableau associatif
    }

    // Fonction pour récupérer un événement par son ID
    public function getEventById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // Lie le paramètre :id à la variable $id
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'événement sous forme de tableau associatif
    }

    // Fonction pour mettre à jour un événement
    public function updateEvent($id, $title, $description, $date) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description, date = :date WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // Lie le paramètre :id à la variable $id
        $stmt->bindParam(':title', $title); // Lie le paramètre :title à la variable $title
        $stmt->bindParam(':description', $description); // Lie le paramètre :description à la variable $description
        $stmt->bindParam(':date', $date); // Lie le paramètre :date à la variable $date

        // Exécute la requête et retourne true en cas de succès, false sinon
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Fonction pour supprimer un événement
    public function deleteEvent($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // Lie le paramètre :id à la variable $id
        return $stmt->execute(); // Exécute la requête et retourne true en cas de succès, false sinon
    }
}
?>

<?php
require_once 'Database.php';

class Event {
    private $conn;
    private $table_name = "events";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addEvent($title, $description, $date) {
        $query = "INSERT INTO " . $this->table_name . " (title, description, date) VALUES (:title, :description, :date)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':date', $date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getEvents() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateEvent($id, $title, $description, $date) {
        $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description, date = :date WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':date', $date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteEvent($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>

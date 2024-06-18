<?php
// Définir la classe Database pour gérer la connexion à la base de données MySQL.
class Database {
    // Détails de connexion à la base de données
    private $host = "localhost"; // Nom de l'hôte
    private $db_name = "projet_event"; // Nom de la base de données
    private $username = "root"; // Nom d'utilisateur de la base de données
    private $password = ""; // Mot de passe de la base de données
    public $conn; // Objet de connexion à la base de données

    // Méthode pour établir et retourner la connexion à la base de données
    public function getConnection() {
        // Initialiser la connexion à null
        $this->conn = null;
        try {
            // Essayer de créer une nouvelle connexion PDO avec les détails fournis
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Définir le mode d'erreur de PDO sur exception pour une meilleure gestion des erreurs
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Capturer toute erreur de connexion et afficher un message d'erreur
            echo "Erreur de connexion : " . $exception->getMessage();
        }
        // Retourner l'objet de connexion
        return $this->conn;
    }
}
?>

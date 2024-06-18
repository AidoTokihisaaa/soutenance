<?php
// Inclut le fichier de configuration de la base de données
require_once 'Database.php';

class User {
    private $conn; // Propriété pour la connexion à la base de données
    private $table_name = "users"; // Nom de la table des utilisateurs

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fonction pour ajouter un utilisateur à la base de données
    public function addUser($username, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
        $query = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username); // Lie le paramètre :username à la variable $username
        $stmt->bindParam(':email', $email); // Lie le paramètre :email à la variable $email
        $stmt->bindParam(':password', $hashed_password); // Lie le paramètre :password à la variable $hashed_password

        return $stmt->execute(); // Exécute la requête et retourne true en cas de succès, false sinon
    }

    // Fonction pour récupérer tous les utilisateurs
    public function getUsers() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retourne tous les utilisateurs sous forme de tableau associatif
    }

    // Fonction pour récupérer un utilisateur par son ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // Lie le paramètre :id à la variable $id
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'utilisateur sous forme de tableau associatif
    }

    // Fonction pour mettre à jour un utilisateur
    public function updateUser($id, $username, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
        $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // Lie le paramètre :id à la variable $id
        $stmt->bindParam(':username', $username); // Lie le paramètre :username à la variable $username
        $stmt->bindParam(':email', $email); // Lie le paramètre :email à la variable $email
        $stmt->bindParam(':password', $hashed_password); // Lie le paramètre :password à la variable $hashed_password

        return $stmt->execute(); // Exécute la requête et retourne true en cas de succès, false sinon
    }

    // Fonction pour supprimer un utilisateur
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id); // Lie le paramètre :id à la variable $id
        return $stmt->execute(); // Exécute la requête et retourne true en cas de succès, false sinon
    }
}
?>

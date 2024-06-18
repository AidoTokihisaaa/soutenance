<?php
// Inclure le modèle User pour interagir avec les données des utilisateurs dans la base de données
require_once 'backend/models/User.php';

// Définir la classe UserController pour gérer les actions liées aux utilisateurs
class UserController {
    // Propriété pour contenir une instance du modèle User
    private $userModel;

    // Constructeur pour initialiser l'instance du modèle User
    public function __construct() {
        $this->userModel = new User();
    }

    // Méthode pour créer un nouvel utilisateur
    public function createUser($username, $email, $password) {
        // Appeler la méthode addUser du modèle User et retourner un message de succès ou d'échec
        return $this->userModel->addUser($username, $email, $password) ? "Utilisateur créé avec succès." : "Échec de la création de l'utilisateur.";
    }

    // Méthode pour obtenir tous les utilisateurs
    public function getAllUsers() {
        // Appeler la méthode getUsers du modèle User et retourner la liste des utilisateurs
        return $this->userModel->getUsers();
    }

    // Méthode pour obtenir un utilisateur spécifique par son ID
    public function getUser($id) {
        // Appeler la méthode getUserById du modèle User et retourner les détails de l'utilisateur
        return $this->userModel->getUserById($id);
    }

    // Méthode pour mettre à jour un utilisateur existant
    public function updateUser($id, $username, $email, $password) {
        // Appeler la méthode updateUser du modèle User et retourner un message de succès ou d'échec
        return $this->userModel->updateUser($id, $username, $email, $password) ? "Utilisateur mis à jour avec succès." : "Échec de la mise à jour de l'utilisateur.";
    }

    // Méthode pour supprimer un utilisateur
    public function deleteUser($id) {
        // Appeler la méthode deleteUser du modèle User et retourner un message de succès ou d'échec
        return $this->userModel->deleteUser($id) ? "Utilisateur supprimé avec succès." : "Échec de la suppression de l'utilisateur.";
    }
}
?>

<?php
require_once 'backend\models\User.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function createUser($username, $email, $password) {
        return $this->userModel->addUser($username, $email, $password) ? "User created successfully." : "Failed to create user.";
    }

    public function getAllUsers() {
        return $this->userModel->getUsers();
    }

    public function getUser($id) {
        return $this->userModel->getUserById($id);
    }

    public function updateUser($id, $username, $email, $password) {
        return $this->userModel->updateUser($id, $username, $email, $password) ? "User updated successfully." : "Failed to update user.";
    }

    public function deleteUser($id) {
        return $this->userModel->deleteUser($id) ? "User deleted successfully." : "Failed to delete user.";
    }
}
?>

<?php
session_start();

// Vider toutes les variables de session
$_SESSION = array();

// Détruire la session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Supprimer le cookie de "Se souvenir de moi" s'il existe
if (isset($_COOKIE['user_login'])) {
    setcookie('user_login', '', time() - 3600, '/');
}

// Rediriger vers la page de connexion avec un paramètre de déconnexion
header("Location: login.php?logout=success");
exit;
?>

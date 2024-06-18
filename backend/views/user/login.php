<?php
// Démarrer une session
session_start();

// Vider la session si elle existe
if (!empty($_SESSION)) {
    session_destroy();
    session_start();
}

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Rediriger vers la page d'accueil s'il est connecté
    header("location: homepage.php");
    exit;
}

// Inclure le fichier de configuration de la base de données
require_once "../../config/database.php";
$database = new Database();
$link = $database->getConnection();

$username = $password = "";
$username_err = $password_err = "";

// Vérifier la méthode de la requête
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si le nom d'utilisateur est vide
    if (empty(trim($_POST["username"]))) {
        $username_err = "Veuillez entrer votre nom d'utilisateur.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Vérifier si le mot de passe est vide
    if (empty(trim($_POST["password"]))) {
        $password_err = "Veuillez entrer votre mot de passe.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Valider les identifiants
    if (empty($username_err) && empty($password_err)) {
        // Préparer une requête SQL pour sélectionner l'utilisateur par nom d'utilisateur
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if ($stmt = $link->prepare($sql)) {
            $stmt->bindParam(1, $username, PDO::PARAM_STR);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $id = $row['id'];
                        $username = $row['username'];
                        $hashed_password = $row['password'];
                        if (password_verify($password, $hashed_password)) {
                            // Démarrer une nouvelle session et stocker les informations de l'utilisateur
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            // Rediriger vers le tableau de bord
                            header("location: dashboard.php");
                        } else {
                            $password_err = "Le mot de passe que vous avez entré n'est pas valide.";
                        }
                    }
                } else {
                    $username_err = "Aucun compte trouvé avec ce nom d'utilisateur.";
                }
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }
        }
    }
    $link = null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../../../css/auth.scss">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>EventPulse</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="../../../backend/views/user/register.php"><i class="fas fa-user-plus"></i> Inscription</a></li>
                    <li><a href="about.php"><i class="fas fa-info-circle"></i> À Propos</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="container">
                <h2>Connexion</h2>
                <form id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-group">
                        <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur:</label>
                        <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>
                        <span class="help-block"><?php echo $username_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label for="password"><i class="fas fa-lock"></i> Mot de passe:</label>
                        <input type="password" id="password" name="password" required>
                        <span class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label for="remember-me" class="remember-me">
                            <input type="checkbox" id="remember-me" name="remember-me"> Se souvenir de moi </label>
                    </div>
                    <button type="submit" class="cta-button"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
                </form>
                <p class="forgot-password"><a href="../../views/user/forgot_password.php"><i class="fas fa-unlock-alt"></i> Mot de passe oublié?</a></p>
            </div>
        </section>
    </main>
    <footer>
        <div class="container">
            <p>&copy; 2024 EventPulse. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>

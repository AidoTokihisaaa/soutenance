<?php
// Démarrer une session
session_start();

// Inclure le fichier de configuration de la base de données
require_once '../../config/database.php';

// Vérifier si la méthode de la requête est POST et si le champ 'email' est défini
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    // Récupérer l'email soumis par le formulaire
    $email = $_POST["email"];
    
    // Créer une nouvelle instance de la classe Database et obtenir la connexion à la base de données
    $database = new Database();
    $conn = $database->getConnection();

    // Préparer une requête SQL pour sélectionner un utilisateur par email
    $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $query->bindParam(1, $email);
    $query->execute();

    // Vérifier si un utilisateur avec cet email existe
    if ($query->rowCount() > 0) {
        // Simuler l'envoi d'un email
        $_SESSION['message'] = "Si cet e-mail est enregistré chez nous, un lien de réinitialisation de mot de passe sera envoyé.";
    } else {
        $_SESSION['message'] = "Aucun compte trouvé avec cet e-mail.";
    }
    // Rediriger vers la page forgot_password.php
    header("Location: forgot_password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de Passe Oublié</title>
    <link rel="stylesheet" href="../../../css/reset.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-calendar-check"></i> AppEvent</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Inscription</a></li>
                    <li><a href="about.php"><i class="fas fa-info-circle"></i> À Propos</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="form-container">
            <h2><i class="fas fa-envelope"></i> Mot de Passe Oublié</h2>
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Adresse e-mail:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Envoyer</button>
            </form>
        </div>
    </main>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="social-media">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <p>&copy; 2024 AppEvent. Tous droits réservés.</p>
            </div>
    </footer>
</body>
</html>

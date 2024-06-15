<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation de l'email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Veuillez entrer votre adresse e-mail.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Veuillez entrer une adresse e-mail valide.";
    } else {
        // Traitement de la réinitialisation du mot de passe
        $email = trim($_POST["email"]);
        // Redirection vers le script de traitement
        header("Location: process_forgot_password.php?email=" . urlencode($email));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de Passe Oublié</title>
    <link rel="stylesheet" href="../../../css/reset.scss">
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
                    <li><a href="../../../backend/views/user/login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                    <li><a href="../../../backend/views/user/register.php"><i class="fas fa-user-plus"></i> Inscription</a></li>
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
            <?php if(!empty($email_err)): ?>
                <div class="alert alert-danger">
                    <?php echo $email_err; ?>
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
        </div>
    </footer>
</body>
</html>

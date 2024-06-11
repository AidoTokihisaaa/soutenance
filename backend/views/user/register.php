<?php
session_start();
require_once '../../config/database.php';
$database = new Database();
$link = $database->getConnection();

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Veuillez entrer un nom d'utilisateur.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Le nom d'utilisateur ne peut contenir que des lettres, des chiffres et des underscores.";
    } else {
        $sql = "SELECT id FROM users WHERE username = :username";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $username_err = "Ce nom d'utilisateur est déjà pris.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }
            unset($stmt);
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Veuillez entrer un email.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Veuillez entrer un mot de passe.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Le mot de passe doit avoir au moins 6 caractères.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"] ?? ''))) {
        $confirm_password_err = "Veuillez confirmer le mot de passe.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Les mots de passe ne correspondent pas.";
        }
    }

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $param_email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $param_password, PDO::PARAM_STR);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            if ($stmt->execute()) {
                header("location: ../../../backend/views/user/login.php");
            } else {
                echo "Oops! Quelque chose s'est mal passé. Veuillez réessayer plus tard.";
            }
            unset($stmt);
        }
    }
    unset($link);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../../../css/auth.scss">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>AppEvent</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../../backend/views/user/login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="container">
                <h2><i class="fas fa-user-plus"></i> Inscription</h2>
                <form id="register-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-group">
                        <label for="username"><i class="fas fa-user"></i> Nom d'utilisateur:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        <span class="help-block"><?php echo $username_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        <span class="help-block"><?php echo $email_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label for="password"><i class="fas fa-lock"></i> Mot de passe:</label>
                        <input type="password" id="password" name="password" required>
                        <div class="password-strength-bar"></div>
                        <span class="help-block"><?php echo $password_err; ?></span>
                    </div>
                    <div class="input-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirmer le mot de passe:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <span class="help-block"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <button type="submit" class="cta-button"><i class="fas fa-user-plus"></i> S'inscrire</button>
                </form>
            </div>
        </section>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordStrengthBar = document.createElement('div');
    passwordStrengthBar.classList.add('password-strength-bar');
    passwordInput.parentNode.insertBefore(passwordStrengthBar, passwordInput.nextSibling);

    function checkPasswordStrength(value) {
        let strength = 0;
        if (value.match(/[a-z]+/)) {
            strength += 1; // Minuscule
        }
        if (value.match(/[A-Z]+/)) {
            strength += 1; // Majuscule
        }
        if (value.match(/[0-9]+/)) {
            strength += 1; // Chiffres
        }
        if (value.match(/[\W]+/)) {
            strength += 1; // Caractères spéciaux
        }
        if (value.length > 7) {
            strength += 1; // Longueur minimale
        }
        return strength;
    }

    passwordInput.addEventListener('input', function() {
        const strength = checkPasswordStrength(passwordInput.value);

        passwordStrengthBar.style.width = `${strength * 20}%`;

        switch(strength) {
            case 0:
            case 1:
            case 2:
                passwordStrengthBar.style.backgroundColor = 'red';
                break;
            case 3:
            case 4:
                passwordStrengthBar.style.backgroundColor = 'orange';
                break;
            case 5:
                passwordStrengthBar.style.backgroundColor = 'green';
                break;
        }
    });

    // Fonction pour générer un mot de passe sécurisé
    const generateButton = document.createElement('button');
    generateButton.innerHTML = '<i class="fas fa-random"></i>';
    generateButton.type = 'button';
    generateButton.classList.add('generate-password');
    passwordInput.parentNode.insertBefore(generateButton, passwordInput.nextSibling);

    generateButton.addEventListener('click', function() {
        const newPassword = generateSecurePassword();
        passwordInput.value = newPassword;
        // Mise à jour manuelle de l'affichage de la force du mot de passe
        passwordInput.dispatchEvent(new Event('input'));
    });

    function generateSecurePassword() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+?><:{}[]';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    // Fonction pour basculer la visibilité du mot de passe
    function togglePasswordVisibility(input) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
    }

    // Ajouter des boutons pour voir le mot de passe
    function addTogglePasswordButton(input) {
        const toggleButton = document.createElement('button');
        toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
        toggleButton.type = 'button';
        toggleButton.classList.add('toggle-password');
        input.parentNode.insertBefore(toggleButton, input.nextSibling);

        toggleButton.addEventListener('click', function() {
            togglePasswordVisibility(input);
            toggleButton.innerHTML = toggleButton.innerHTML.includes('eye') ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        });
    }

    addTogglePasswordButton(passwordInput);
    addTogglePasswordButton(confirmPasswordInput);
});

    </script>
</body>
</html>

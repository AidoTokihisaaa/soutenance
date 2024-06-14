<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion d'Événements</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="backend/views/user/login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                    <li><a href="backend/views/user/register.php"><i class="fas fa-user-plus"></i> Inscription</a></li>
                    <li><a href="about.php"><i class="fas fa-info-circle"></i> À Propos</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>
<main>
    <section class="hero">
        <div class="container">
            <h2>Bienvenue sur AppEvent</h2>
            <p>Découvrez une nouvelle façon d'organiser vos événements. Avec AppEvent, tout devient plus simple. Que vous planifiez une petite réunion ou un grand rassemblement, notre plateforme vous accompagne à chaque étape.</p>
            <a href="backend/views/user/register.php" class="cta-button"><i class="fas fa-play"></i>Rejoignez-nous</a>
        </div>
    </section>
    <section class="features">
        <div class="container">
            <div class="card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Organisation Simplifiée</h3>
                <p>Facilitez la planification et la gestion de vos événements, sans stress ni complications. Avec AppEvent, tout est sous contrôle.</p>
            </div>
            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Communauté Engagée</h3>
                <p>Interagissez facilement avec vos participants. Envoyez des invitations, suivez les réponses et communiquez en temps réel.</p>
            </div>
            <div class="card">
                <i class="fas fa-share-alt"></i>
                <h3>Partage Facile</h3>
                <p>Partagez vos événements avec votre réseau. Augmentez la portée de vos invitations et assurez-vous que personne ne manque vos événements.</p>
            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctaButton = document.querySelector('.cta-button');
        ctaButton.addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.1)';
        });
        ctaButton.addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });

        const cards = document.querySelectorAll('.features .card');
        cards.forEach(card => {
            card.addEventListener('mouseover', function() {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 8px 15px rgba(0, 0, 0, 0.3)';
            });
            card.addEventListener('mouseout', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.15)';
            });
        });
    });
</script>
</script>
</body>
</html>
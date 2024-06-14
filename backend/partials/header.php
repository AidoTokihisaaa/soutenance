<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<header>
    <div class="container">
        <div class="logo">
            <h1><i class="fas fa-calendar-alt"></i> Dashboard</h1>
        </div>
        <nav>
            <ul>
                <li><a href="/index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="/views/event/create.php"><i class="fas fa-plus-circle"></i> Créer un Événement</a></li>
                <li><a href="/views/user/dashboard.php"><i class="fas fa-tasks"></i> Dashboard</a></li>
                <li><a href="/views/event/manage.php"><i class="fas fa-tasks"></i> Gérer les Événements</a></li>
                <li class="user-menu">
                    <a href="#" id="user-icon"><i class="fas fa-user"></i></a>
                    <div class="dropdown-content">
                        <div class="user-info">
                            <p><strong>Nom:</strong> <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email'] ?? 'N/A') ?></p>
                        </div>
                        <a href="/views/user/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>

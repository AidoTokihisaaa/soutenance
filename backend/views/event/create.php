<?php
session_start();

require_once "../../config/database.php";
$database = new Database();
$link = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $date = $_POST['date'];
    $userId = $_SESSION['id'];

    if (!empty($title) && !empty($description) && !empty($date)) {
        $sql = "INSERT INTO events (user_id, name, description, date) VALUES (:user_id, :name, :description, :date)";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Event Created Successfully!";
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['message'] = "Error: Could not create event.";
            }
        }
    } else {
        $_SESSION['message'] = "Please fill in all fields.";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/create.css">
    <title>Create Event</title>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-calendar-alt"></i> Create Event</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../../index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="../../../backend/views/user/dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="about.php"><i class="fas fa-info-circle"></i> À Propos</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="../../../backend/views/user/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="container">
            <h1>Create New Event</h1>
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="title">Event Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                <button type="submit" class="btn">Create Event</button>
            </form>
        </div>
    </main>
    <footer>
        <div class="container">
            <p>&copy; 2024 AppEvent. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>

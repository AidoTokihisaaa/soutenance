<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <form action="backend\controllers\EventController.php" method="post">
            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
            <div class="form-group">
                <label for="title">Event Title:</label>
                <input type="text" id="title" name="title" required value="<?php echo $event['title']; ?>">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo $event['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required value="<?php echo $event['date']; ?>">
            </div>
            <button type="submit" class="btn">Update Event</button>
        </form>
    </div>
</body>
</html>

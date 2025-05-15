<?php
session_start();
require_once '../../config/database.php';

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Profil szerkesztése</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>

<nav>
    <a href="../../index.php">Kezdőlap</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="../../controllers/logout.php" class="logout">Kijelentkezés</a>
</nav>

<h1>Profil szerkesztése</h1>

<div class="container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="../../controllers/user/updateProfileController.php" method="POST" id="editForm" novalidate>
        <label for="name">Név:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <button type="submit">Mentés</button>
    </form>
</div>



</body>
</html>
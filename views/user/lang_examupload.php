<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Nyelvvizsga hozzáadása</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
<nav>
    <a href="../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/admin/admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
        <?php else: ?>
            <a href="views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/dashboard.php') ? 'active' : '' ?>">Dashboard</a>
        <?php endif; ?>
        <a href="../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
         <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="company/login.php?type=company">Bejelentkezés cégként</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == './views/register.php') ? 'active' : '' ?>">Regisztráció</a>
            <div class="dropdown-content">
                <a href="register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="company/register.php?type=company">Regisztráció cégként</a>
            </div>
        </div>
    <?php endif; ?>
</nav>
    
        <h1>Új nyelvvizsga hozzáadása</h1>

        <form action="../../controllers/user/addLanguageCertificate.php" method="POST">
            <label for="language">Nyelv:</label>
            <input type="text" name="language" id="language" required>

            <label for="level">Szint:</label>
            <input type="text" name="level" id="level" placeholder="pl. B2" required>

            <label for="exam_date">Vizsga dátuma:</label>
            <input type="date" name="exam_date" id="exam_date" required>

            <button type="submit">Hozzáadás</button>
        </form>

        <br>
        <a href="dashboard.php">⬅ Vissza a főoldalra</a>
</body>
</html>

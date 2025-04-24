<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Főoldal</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <h1>Üdvözöllek a weboldalon!</h1>

    <nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="./views/admin/admindashboard.php">Admin Dashboard</a>
        <?php elseif ($_SESSION['user_role'] === 'company'): ?>
            <a href="views/company/companydashboard.php">Cég Dashboard</a>
            <a href="views/company/createad.php">Álláshirdetés létrehozása</a>
            <a href="views/company/companyads.php" class="active">Álláshirdetések</a>
        <?php endif; ?>
        <a href="controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
        <div class="dropdown">
            <a href="#">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="views/login.php?type=user">Magánszemély</a>
                <a href="views/company/login.php?type=company">Cég</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#">Regisztráció</a>
            <div class="dropdown-content">
                <a href="views/register.php?type=individual">Magánszemély</a>
                <a href="views/company/register.php?type=company">Cég</a>
            </div>
        </div>
    <?php endif; ?>
</nav>


</body>
</html>

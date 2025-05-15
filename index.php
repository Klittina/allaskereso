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
<nav class="navbar">
    <div class="navbar-left">
        <a href="index.php" class="logo">HireMePls</a>
    </div>

    <div class="navbar-center">
        <a href="./views/user/showJobs.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'showJobs.php') ? 'active' : '' ?>">Állások</a>
        <a href="applications.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'applications.php') ? 'active' : '' ?>">Jelentkezéseim</a>
    </div>

    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'user'): ?>
            <div class="dropdown">
                <button class="dropbtn">Profilom ▼</button>
                <div class="dropdown-content">
                    <a href="./views/dashboard.php">Profilom</a>
                    <a href="./views/user/cvupload.php">Önéletrajz feltöltése</a>
                    <a href="./views/user/lang_examupload.php">Új nyelvvizsga</a>
                    <a href="./views/user/newschool.php">Új képzettség</a>
                    <a href="controllers/logout.php" class="logout">Kijelentkezés</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Bejelentkezés/Regisztráció ha nincs bejelentkezve -->
            <div class="dropdown">
                <button class="dropbtn">Bejelentkezés</button>
                <div class="dropdown-content">
                <a href="views/login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="views/company/login.php?type=company">Bejelentkezés cégként</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Regisztráció</button>
                <div class="dropdown-content">
                <a href="views/register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="views/company/register.php?type=company">Regisztráció cégként</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>
<div class="hero">
        <div class="hero-text">
            <h1>Találd meg a <span class="highlight">melót</span>, ami megér<br>egy hétfőt!</h1>
            <p>Gyors és egyszerű álláskeresés modern felülettel: okos szűrőkkel és személyre szabott ajánlatokkal.<br>
               Görgess, kattints, jelentkezz – ennyire könnyű is lehet!</p>
        </div>
        <div class="hero-image">
            <img src="img/jobsearch.png" alt="Job search illustration">
        </div>
    </div>
</body>
</html>

<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Cég bejelentkezés</title>
    <link rel="stylesheet" href="../../assets/styles.css">
    <script src="../../assets/js/formValidation.js"></script>
</head>
<body>
<nav class="navbar">
    <div class="navbar-left">
        <a href="../../index.php" class="logo">HireMePls</a>
    </div>

    <div class="navbar-center">
        <a href="showJobs.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'showJobs.php') ? 'active' : '' ?>">Állások</a>
        <a href="application.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'applications.php') ? 'active' : '' ?>">Jelentkezéseim</a>
    </div>

    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'user'): ?>
            <div class="dropdown">
                <button class="dropbtn">Profilom ▼</button>
                <div class="dropdown-content">
                    <a href="../../views/dashboard.php">Profilom</a>
                    <a href="cvupload.php">Önéletrajz</a>
                    <a href="lang_examupload.php">Új nyelvvizsga</a>
                    <a href="newschool.php">Új képzettség</a>
                    <a href="../../controllers/logout.php" class="logout">Kijelentkezés</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Bejelentkezés/Regisztráció ha nincs bejelentkezve -->
            <div class="dropdown">
                <button class="dropbtn">Bejelentkezés</button>
                <div class="dropdown-content">
                    <a href="../login.php">Magánszemély</a>
                    <a href="login.php">Cég</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Regisztráció</button>
                <div class="dropdown-content">
                    <a href="../register.ph">Magánszemély</a>
                    <a href="register.php">Cég</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>

    <h2>Cég bejelentkezés</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form method="POST" action="../../controllers/company/loginCompanyController.php" id="regForm" novalidate>
        <label for="email">Email cím:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Jelszó:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Bejelentkezés</button>
    </form>
</body>
</html>

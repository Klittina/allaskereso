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
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <a href="./views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
<?php elseif ($_SESSION['user_role'] === 'company'): ?>
    <a href="companydashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Cég Dashboard</a>
    <a href="createad.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'createad.php') ? 'active' : '' ?>">Álláshirdetés létrehozása</a>
    <a href="companyads.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'companyads.php') ? 'active' : '' ?>">Álláshirdetések</a>
    <?php else: ?>
    <a href="../../views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="cvupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Önéletrajz</a>
    <a href="lang_examupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Új nyelvvizsga</a>
<?php endif; ?>

        <a href="../../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
          <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="../login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="login.php?type=company">Bejelentkezés cégként</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == './views/register.php') ? 'active' : '' ?>">Regisztráció</a>
            <div class="dropdown-content">
                <a href="../register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="register.php?type=company">Regisztráció cégként</a>
            </div>
        </div>
    <?php endif; ?>
</nav>
    
        <h1>Új nyelvvizsga hozzáadása</h1>

        <form action="../../controllers/user/lang_examuploadController.php" method="POST">
            <label for="language">Nyelv:</label>
            <input type="text" name="language" id="language" required>

            <label for="level">Szint:</label>
            <input type="text" name="level" id="level" placeholder="pl. B2" required>


            <button type="submit">Hozzáadás</button>
        </form>

        <br>
        <a href="dashboard.php">⬅ Vissza a főoldalra</a>
</body>
</html>
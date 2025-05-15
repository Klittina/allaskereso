<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/styles.css">
    <script src="../../assets/js/formValidation.js"></script>
    <title>Öntéletrajz feltöltése</title>
</head>
<body>

<nav>
    <a href="index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Ha a felhasználó be van jelentkezve -->
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <!-- Ha admin a felhasználó, akkor az admin dashboardra irányítunk -->
            <a href="./views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/admin/admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
        <?php else: ?>
            <!-- Ha sima felhasználó a bejelentkezett felhasználó, akkor a sima dashboardra -->
            <a href="./views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/dashboard.php') ? 'active' : '' ?>">Dashboard</a>
        <?php endif; ?>
        <a href="./controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
        <!-- Ha a felhasználó nincs bejelentkezve -->
        <!-- 🔽 Bejelentkezés dropdown -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="views/login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="views/company/login.php?type=company">Bejelentkezés cégként</a>
            </div>
        </div>
        <!-- Regisztráció dropdown menü -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == './views/register.php') ? 'active' : '' ?>">Regisztráció</a>
            <div class="dropdown-content">
                <a href="./views/register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="./views/company/register.php?type=company">Regisztráció cégként</a>
            </div>
        </div>
    <?php endif; ?>
</nav>

    <h1>Önéletrajz feltöltése</h1>
    <form action="../../controllers/user/cvuploadController.php" method="POST" id="regForm" novalidate>
        <label for="email">Teljes név</label>
        <input type="text" name="name" placeholder="pl.: Horváth Bence" required>
        <br>
        <label for="email">Önéletrajz nyelve:</label>
        <input type="text" name="lang" placeholder="pl.: angol" required>
        <br>
        <label for="email">Önéletrajz elérési útja</label>
        <input type="text" name="path" placeholder="" required>
        <br>
        <button type="submit">Feltöltés</button>
    </form>
</body>
</html>